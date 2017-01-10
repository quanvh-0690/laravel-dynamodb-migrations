<?php
namespace QuanKim\LaravelDynamoDBMigrations;

use Illuminate\Support\ServiceProvider;
use QuanKim\LaravelDynamoDBMigrations\Commands\MakeMigration;
use QuanKim\LaravelDynamoDBMigrations\Commands\Migrate;
use QuanKim\LaravelDynamoDBMigrations\Commands\Rollback;

class DynamoDBMigrationServiceProvider extends ServiceProvider
{
    protected $commands = [
        MakeMigration::class,
        Migrate::class,
        Rollback::class,
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
