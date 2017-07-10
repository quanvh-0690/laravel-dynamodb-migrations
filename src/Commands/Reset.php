<?php
namespace QuanKim\LaravelDynamoDBMigrations\Commands;

use Illuminate\Console\ConfirmableTrait;
use Symfony\Component\Console\Input\InputOption;

class Reset extends BaseCommand
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'dynamodb:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback all migration for DynamoDB';

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
        if (! $this->confirmToProceed()) {
            return;
        }

        $migrationsData = $this->getMigrationsData();
        $migrationsSorted = collect($migrationsData)->sortByDesc(function ($migration) {
            return sprintf('%-12s%s', $migration['batch'], $migration['name']);
        });

        foreach ($migrationsSorted as $item) {
            $this->runRollback($item['name'], $item['batch']);
        }
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
        ];
    }
}
