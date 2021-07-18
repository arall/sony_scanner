<?php

namespace App\Libs\Executors\Audit;

use App\Libs\Contracts\Executor;

/**
 * Docker Audit Executor.
 */
class Docker extends Executor
{
    /**
     * {@inheritdoc}
     */
    protected $modules = [
        \App\Libs\Modules\Audit\Docker\PrivateKeyWorldReadable::class,
    ];
}
