<?php
namespace QuanKim\LaravelDynamoDBMigrations\Commands;

use Illuminate\Console\Command;
use Aws\DynamoDb\Exception\DynamoDbException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use QuanKim\LaravelDynamoDBMigrations\DBClient;

class BaseCommand extends Command
{
    protected $dbClient;
    
    public function __construct()
    {
        parent::__construct();
        $this->dbClient = DBClient::factory();
    }
    
    protected function isTableExists($tableName)
    {
        try {
            $result = $this->dbClient->describeTable([
                'TableName' => $tableName,
            ]);
        } catch (DynamoDbException $e) {
            return false;
        }
        
        return true;
    }
    
    protected function getLastBatchNumber($data)
    {
        return collect($data)->max('batch') ?: 0;
    }
    
    protected function runMigrate($file, $batch)
    {
        $instance = $this->newInstance($file);
        $instance->up();
        $this->writeMigrationLog($file, $batch);
        $this->line('<info>Migrated: </info>' . $file);
    }
    
    protected function getMigrationsData()
    {
        if ($this->isTableExists(config('aws.prefix') . 'migrations')) {
            $results = $this->dbClient->scan([
                'TableName' => config('aws.prefix') . 'migrations',
            ]);
            $data = [];
            foreach ($results['Items'] as $row) {
                $data[] = [
                    'name' => $row['name']['S'],
                    'batch' => $row['batch']['N'],
                ];
            }
            
            return $data;
        }
        
        $this->createMigrationsTable();
        
        return [];
    }
    
    protected function createMigrationsTable()
    {
        $this->dbClient->createTable([
            'TableName' => config('aws.prefix') . 'migrations',
            'AttributeDefinitions' => [
                [
                    'AttributeName' => 'name',
                    'AttributeType' => 'S',
                ],
                [
                    'AttributeName' => 'batch',
                    'AttributeType' => 'N',
                ],
            ],
            'KeySchema' => [
                [
                    'AttributeName' => 'batch',
                    'KeyType' => 'HASH',
                ],
                [
                    'AttributeName' => 'name',
                    'KeyType' => 'RANGE',
                ],
            ],
            'ProvisionedThroughput' => [
                'ReadCapacityUnits' => 1,
                'WriteCapacityUnits' => 1,
            ],
        ]);
        $this->dbClient->waitUntil('TableExists', [
            'TableName' => config('aws.prefix') . 'migrations',
            '@waiter' => [
                'delay' => 5,
                'maxAttempts' => 20,
            ],
        ]);
    }
    
    protected function getAllMigrationsFile($migrationsPath)
    {
        return Collection::make($migrationsPath)->flatMap(function ($path) {
            return File::glob($path.'/*_*.php');
        })->filter()->sortBy(function ($file) {
            return str_replace('.php', '', basename($file));
        })->values()->keyBy(function ($file) {
            return str_replace('.php', '', basename($file));
        })->all();
    }
    
    protected function writeMigrationLog($file, $batch)
    {
        $this->dbClient->putItem([
            'TableName' => config('aws.prefix') . 'migrations',
            'Item' => [
                'name' => ['S' => $file],
                'batch' => ['N' => (string)$batch],
            ],
        ]);
    }
    
    private function newInstance($file)
    {
        $class = 'Database\Migration\DynamoDB\\' . studly_case(implode('_', array_slice(explode('_', $file), 4)));
        
        return new $class;
    }
    
    protected function runRollback($file, $batch)
    {
        $canRollback = true;
        $instance = $this->newInstance($file);
        $instance->down($canRollback);
        if ($canRollback) {
            $this->deleteMigrationLog($file, $batch);
            $this->line('<info>Rollback: </info>' . $file);
        }
    }
    
    protected function deleteMigrationLog($file, $batch)
    {
        $this->dbClient->deleteItem([
            'TableName' => config('aws.prefix') . 'migrations',
            'Key' => [
                'batch' => [
                    'N' => (string)$batch,
                ],
                'name' => [
                    'S' => $file,
                ],
            ],
        ]);
    }
}
