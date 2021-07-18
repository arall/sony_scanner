<?php

namespace App\Libs\Contracts;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Executor
{
    /**
     * Target model.
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * The output interface implementation.
     *
     * @var OutputInterface
     */
    protected $output;

    /**
     * List of modules.
     *
     * @var array
     */
    protected $modules = [];

    /**
     * @param  Model  $model
     * @param  OutputInterface|null $output
     */
    public function __construct(Model $model, OutputInterface $output = null)
    {
        $this->model = $model;
        $this->output = $output;
    }

    /**
     * Run the executor.
     */
    public function run()
    {
        foreach ($this->modules as $moduleClass) {
            $this->executeModule($moduleClass);
        }
    }

    /**
     * Execute the module.
     *
     * @param string $moduleClass
     */
    private function executeModule(string $moduleClass)
    {
        $module = new $moduleClass($this->model, $this->output);
        $module->execute();
    }
}
