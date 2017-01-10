<?php
namespace QuanKim\LaravelDynamoDBMigrations;

use Aws\DynamoDb\DynamoDbClient;

class DBClient
{
    protected $dbClient;
    
    public function __construct()
    {
        $this->dbClient = static::factory();
    }
    
    public static function factory()
    {
        return DynamoDbClient::factory([
            'endpoint' => config('aws.endpoint'),
            'region' => config('aws.region'),
            'version' => config('aws.version'),
            'credentials' => config('aws.credentials'),
        ]);
    }
}
