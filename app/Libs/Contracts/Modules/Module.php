<?php

namespace App\Libs\Contracts\Modules;

use Exception;
use Symfony\Component\Console\Output\OutputInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Module Abstract class.
 */
abstract class Module
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
     * @param  Model|null  $model
     * @param  OutputInterface|null  $output
     * @throws Exception
     */
    public function __construct(Model $model = null, OutputInterface $output = null)
    {
        $this->model = $model;
        $this->output = $output;
    }

    /**
     * Performs the module initialization.
     *
     * Useful for performing actions before the module is executed.
     */
    protected function init()
    {
    }

    /**
     * Checks if the the module can run.
     *
     * @throws Exception
     */
    protected function canRun()
    {
    }

    /**
     * Performs the main module action.
     *
     * @throws Exception
     */
    protected function run()
    {
    }

    /**
     * Performs the after-run actions.
     *
     * Useful to perform actions once the module was executed.
     */
    protected function finish()
    {
    }

    /**
     * Gets the module code.
     *
     * @return string
     */
    public function getCode()
    {
        return str_replace('App\\Libs\\Modules\\', '', get_class($this));
    }

    /**
     * Executes the module
     *
     * @return bool
     */
    public function execute()
    {
        $this->output(' [+] Executing <info>' . $this->getCode() . '</info> module...');

        try {
            $this->init();
        } catch (Exception $e) {
            $this->output('  - <comment>Init error:</comment> ' . $e->getMessage());
            return false;
        }

        try {
            $this->canRun();
        } catch (Exception $e) {
            $this->output('  - <comment>Can\'t run:</comment> ' . $e->getMessage());
            return false;
        }

        try {
            $this->run();
        } catch (Exception $e) {
            $this->output('<error>' . $e->getMessage() . '</error>');
            return false;
        }

        $this->finish();

        return true;
    }

    /**
     * Writes an output line.
     *
     * @param string $text
     */
    protected function output(string $text)
    {
        if ($this->output) {
            $this->output->writeln($text);
        }
    }

    /**
     * Writes an output for a detail.
     *
     * @param string $title
     * @param string $value
     */
    protected function outputDetail(string $title, string $value)
    {
        $value = str_replace(PHP_EOL, ' | ', $value);

        $this->output('  - <comment>' . $title . ':</comment> ' . $value);
    }
}
