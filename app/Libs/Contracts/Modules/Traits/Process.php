<?php

namespace App\Libs\Contracts\Modules\Traits;

use Symfony\Component\Process\Process as ProcessClass;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Process Trait for Modules.
 */
trait Process
{
    /**
     * Run a command in a process.
     *
     * @param  string|array $command        The command to run and its arguments listed as separate entries
     * @param  bool         $ignoreFailure  Ignore exit codes
     * @param  null|array   $env            Environment variables
     *
     * @throws ProcessFailedException
     *
     * @return string|null Output
     */
    protected function runProcess($command, $ignoreFailure = false, $env = null)
    {
        $process = new ProcessClass($command, null, $env);
        $process->setTimeout($this->getProcessTimeout());
        $process->run();

        if (!$process->isSuccessful() && !$ignoreFailure) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    /**
     * Gets the defined process timeout.
     *
     * Default
     *
     * @return int
     */
    protected function getProcessTimeout()
    {
        if (isset($this->timeout)) {
            return $this->timeout;
        }

        return config('scanner.process.timeout');
    }
}
