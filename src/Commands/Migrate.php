<?php
namespace QuanKim\LaravelDynamoDBMigrations\Commands;

use Illuminate\Filesystem\Filesystem;

class Migrate extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dynamodb:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migration for DynamoDB';

    private $files;
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $migrationsPath = database_path() . '/migrations/dynamodb';
        $allMigrationsFile = $this->getAllMigrationsFile($migrationsPath);
        $migrationsData = $this->getMigrationsData();
        $migrationsRunFile = array_except($allMigrationsFile, array_pluck($migrationsData, 'name'));
        $batch = $this->getLastBatchNumber($migrationsData) + 1;
        foreach ($migrationsRunFile as $fileName => $path) {
            $this->runMigrate($fileName, $batch);
        }
    }
}
