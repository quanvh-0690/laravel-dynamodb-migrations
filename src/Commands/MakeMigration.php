<?php
namespace QuanKim\LaravelDynamoDBMigrations\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;

class MakeMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dynamodb:make_migration {name} {--create=} {--table=}';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make migration for DynamoDB';
    
    private $files;
    private $composer;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct();
        $this->files = $files;
        $this->composer = $composer;
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        $create = $this->option('create') ?: false;
        $table = $this->option('table');
        $this->createMigration($name, $table, $create);
    }
    
    private function createMigration($name, $table, $create)
    {
        $migrationsPath = database_path() . '/migrations/dynamodb/';
        if (!$this->files->exists($migrationsPath)) {
            $this->files->makeDirectory($migrationsPath);
        }
        
        $this->writeFile($name, $migrationsPath, $table, $create);
    }
    
    private function writeFile($name, $path, $table, $create)
    {
        $path .= date('Y_m_d_His') . '_' . $name . '.php';
        $stub = $this->getStub($table, $create);
        $this->files->put($path, $this->getContentFile($name, $stub, $table));
        $this->composer->dumpAutoloads();
        $this->line('<info>Created DynamoDB Migration: </info>' . $path);
    }
    
    private function getStub($table, $create)
    {
        $stubsPath = __DIR__ . '/stubs';
        if (!$table) {
            return $this->files->get($stubsPath . '/blank.stub');
        }
        
        $stubFile = $create ? '/create.stub' : '/update.stub';
        
        return $this->files->get($stubsPath . $stubFile);
    }
    
    private function getContentFile($name, $stub, $table)
    {
        $className = Str::studly($name);
        $stub = str_replace('DummyClass', $className, $stub);
        
        return ($table) ? str_replace('DummyTable', $table, $stub) : $stub;
    }
}
