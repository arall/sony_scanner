<?php

namespace App\Libs\Executors\Discovery;

use App\Libs\Contracts\Executor;

/**
 * Port Discovery Executor.
 */
class Port extends Executor
{
    /**
     * {@inheritdoc}
     */
    protected $modules = [
        \App\Libs\Modules\Discovery\Ports\Websites::class,
    ];
}
