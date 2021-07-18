<?php

namespace App\Libs\Executors\Audit;

use App\Libs\Contracts\Executor;

/**
 * Repository Audit Executor.
 */
class Repository extends Executor
{
    /**
     * {@inheritdoc}
     */
    protected $modules = [
        \App\Libs\Modules\Audit\Repositories\Semgrep::class,
    ];
}
