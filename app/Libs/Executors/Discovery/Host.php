<?php

namespace App\Libs\Executors\Discovery;

use App\Libs\Contracts\Executor;

/**
 * Host Discovery Executor.
 */
class Host extends Executor
{
    /**
     * {@inheritdoc}
     */
    protected $modules = [
        \App\Libs\Modules\Discovery\Hosts\Ports\Nmap::class,
    ];
}
