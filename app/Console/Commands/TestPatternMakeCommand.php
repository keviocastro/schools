<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class TestPatternMakeCommand extends GeneratorCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:test:pattern';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new test class for schools project';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Test';

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        $class = $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
        
        $controllerClassName = $this->argument('name');
        $modelVarName = camel_case(str_replace('ControllerTest', '',$controllerClassName));
        $modelClassName = ucfirst($modelVarName);
        $resourceName = str_replace('_','-',
                str_plural(strtolower(snake_case($modelVarName)))
            );
        $database_name = str_plural(snake_case($modelClassName));

        $class = str_replace('ControllerClassName', $controllerClassName, $class);
        $class = str_replace('modelVarName', $modelVarName, $class);
        $class = str_replace('ModelClassName', $modelClassName, $class);
        $class = str_replace('resourceName', $resourceName, $class);
        $class = str_replace('database_name', $database_name, $class);

        return $class;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {   
        return __DIR__.'/stubs/test.stub';
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = str_replace($this->laravel->getNamespace(), '', $name);

        return $this->laravel['path.base'].'/tests/'.str_replace('\\', '/', $name).'.php';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace;
    }

}
