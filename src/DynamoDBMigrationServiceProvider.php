<?php
namespace QuanKim\LaravelDynamoDBMigrations;

use Illuminate\Support\ServiceProvider;
use QuanKim\LaravelDynamoDBMigrations\Commands\MakeMigration;
use QuanKim\LaravelDynamoDBMigrations\Commands\MakeModel;
use QuanKim\LaravelDynamoDBMigrations\Commands\MakeSeed;
use QuanKim\LaravelDynamoDBMigrations\Commands\Migrate;
use QuanKim\LaravelDynamoDBMigrations\Commands\Reset;
use QuanKim\LaravelDynamoDBMigrations\Commands\Rollback;
use QuanKim\LaravelDynamoDBMigrations\Commands\Seed;

class DynamoDBMigrationServiceProvider extends ServiceProvider
{
    protected $commands = [
        MakeMigration::class,
        Migrate::class,
        Rollback::class,
        MakeSeed::class,
        Seed::class,
        Reset::class,
        MakeModel::class,
    ];
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }
}
