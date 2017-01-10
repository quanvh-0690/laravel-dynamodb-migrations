<?php
namespace QuanKim\LaravelDynamoDBMigrations\Commands;

class Rollback extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dynamodb:rollback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback migration for DynamoDB';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $migrationsData = $this->getMigrationsData();
        $batch = $this->getLastBatchNumber($migrationsData);
        $migrationsRunFile = array_where($migrationsData, function($value) use ($batch) {
            return $value['batch'] == $batch;
        });
        foreach ($migrationsRunFile as $item) {
            $this->runRollback($item['name'], $item['batch']);
        }
    }
}
