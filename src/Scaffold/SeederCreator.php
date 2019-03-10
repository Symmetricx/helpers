<?php

namespace Encore\Admin\Helpers\Scaffold;

class SeederCreator
{
    /**
     * Seeder full name.
     *
     * @var string
     */
    protected $name;

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    protected $table_name;
    
    /**
     * SeederCreator constructor.
     *
     * @param string $name
     * @param null   $files
     */
    public function __construct($name, $table_name, $files = null)
    {

        $this->table_name = $table_name;

        $this->name = $name;

        $this->files = $files ?: app('files');
    }

    /**
     * Create a seeder.
     *
     * @param string $model
     *
     * @throws \Exception
     *
     * @return string
     */
    public function create($model)
    {
        $path = $this->getpath($this->name);

        if ($this->files->exists($path)) {
            throw new \Exception("Seeder [$this->name] already exists!");
        }

        $stub = $this->files->get($this->getStub());

        $this->files->put($path, $this->replace($stub, $this->name, $model));

        return $path;
    }

    /**
     * @param string $stub
     * @param string $name
     * @param string $model
     *
     * @return string
     */
    protected function replace($stub, $name, $model)
    {
        $stub = $this->replaceClass($stub, $name, $this->table_name);

        return str_replace(
            ['DummyModelNamespace', 'DummyModel'],
            [$model, class_basename($model)],
            $stub
        );
    }

    /**
     * Get seeder namespace from giving name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getNamespace($name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param string $stub
     * @param string $name
     *
     * @return string
     */
    protected function replaceClass($stub, $name, $table_name)
    {
        $class = str_replace($this->getNamespace($name).'\\', '', $name);

        return str_replace(['DummyClass', 'DummyNamespace', 'TableName'], [$class, $this->getNamespace($name), $table_name], $stub);
    }

    /**
     * Get file path from giving seeder name.
     *
     * @param $name
     *
     * @return string
     */
    public function getPath($name)
    {
        $segments = explode('\\', $name);

        // array_shift($segments);

        return base_path(implode('/', $segments)).'.php';
    }

    /**
     * Get stub file path.
     *
     * @return string
     */
    public function getStub()
    {
        return __DIR__.'/stubs/seed.stub';
    }
}
