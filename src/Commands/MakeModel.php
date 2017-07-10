<?php
namespace QuanKim\LaravelDynamoDBMigrations\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;

class MakeModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dynamodb:make_model {name} {--table=}';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make model eloquent DynamoDB';
    
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
        $table = $this->option('table');
        $this->createModel($name, $table);
    }
    
    private function createModel($name, $table)
    {
        $modelsPath = app_path('Models');
        if (!$this->files->exists($modelsPath)) {
            $this->files->makeDirectory($modelsPath);
        }
        
        $this->writeFile($name, $modelsPath, $table);
    }
    
    private function writeFile($name, $path, $table)
    {
        $path .= DIRECTORY_SEPARATOR . $name . '.php';
        $stub = $this->getStub();
        $this->files->put($path, $this->getContentFile($name, $stub, $table));
        $this->composer->dumpAutoloads();
        $this->line('<info>Created DynamoDB Model: </info>' . $path);
    }
    
    private function getStub()
    {
        return $this->files->get(__DIR__ . '/stubs/model.stub');
    }
    
    private function getContentFile($name, $stub, $table)
    {
        $className = Str::studly($name);
        $stub = str_replace('DummyClass', $className, $stub);
        
        return ($table) ? str_replace('DummyTable', $table, $stub) : $stub;
    }
}
