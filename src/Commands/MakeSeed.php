<?php

namespace QuanKim\LaravelDynamoDBMigrations\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;

class MakeSeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dynamodb:make_seed {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new DynamoDB Seeder class';
    
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
        $this->createSeeder($name);
    }
    
    private function createSeeder($name)
    {
        $seederPath = database_path() . '/seeds/dynamodb/';
        if (!$this->files->exists($seederPath)) {
            $this->files->makeDirectory($seederPath);
        }
    
        $this->writeFile($name, $seederPath);
    }
    
    private function writeFile($name, $seederPath)
    {
        $seederPath .= $name . '.php';
        $stub = $this->getStub();
        $this->files->put($seederPath, $this->getContentFile($name, $stub));
        $this->composer->dumpAutoloads();
        $this->line('<info>Created DynamoDB Seeder: </info>' . $seederPath);
    }
    
    private function getStub()
    {
        return $this->files->get(__DIR__ . '/stubs/seeder.stub');
    }
    
    private function getContentFile($name, $stub)
    {
        $className = Str::studly($name);
        $stub = str_replace('DummyClass', $className, $stub);
        
        return $stub;
    }
}
