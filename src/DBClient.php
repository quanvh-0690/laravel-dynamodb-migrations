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
        $config = [
            'region' => config('aws.region'),
            'version' => config('aws.version'),
            'credentials' => config('aws.credentials'),
            'http' => [
                'verify' => config('aws.http.verify'),
            ]
        ];
        if (config('aws.endpoint')) {
            $config['endpoint'] = config('aws.endpoint');
        }
    
        return DynamoDbClient::factory($config);
    }
}
