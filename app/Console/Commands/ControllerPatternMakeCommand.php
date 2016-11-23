<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class ControllerPatternMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:controller:pattern';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new controller class for schools project';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/controller.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Controllers';
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in base namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {   

        $namespace = $this->getNamespace($name);
        $class = str_replace("use $namespace\Controller;\n", '', parent::buildClass($name));
        
        $name = $this->argument('name');

        $ModelClassName = str_replace('Controller', '',ucfirst($name));
        $modelVarName = camel_case($name);
        $resourceName = str_plural(strtolower($name));

        $class = str_replace(
            "ModelClassName", 
            $ModelClassName, 
            $class);

        $class = str_replace(
            "modelVarName", 
            $modelVarName, 
            $class);

        $class = str_replace(
            "resourceName", 
            $resourceName, 
            $class);

        $class = str_replace(
            "resourceName", 
            $resourceName, 
            $class);

        return $class;
    }
}
