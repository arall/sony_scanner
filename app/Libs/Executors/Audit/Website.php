<?php

namespace App\Libs\Executors\Audit;

use App\Libs\Contracts\Executor;

/**
 * Website Audit Executor.
 */
class Website extends Executor
{
    /**
     * {@inheritdoc}
     */
    protected $modules = [
        \App\Libs\Modules\Audit\Websites\Http::class,
        \App\Libs\Modules\Audit\Websites\ExposedVCS::class,
        \App\Libs\Modules\Audit\Websites\Cookies::class,
        \App\Libs\Modules\Audit\Websites\Logout::class,
        \App\Libs\Modules\Audit\Websites\TestSSL::class,
    ];
}
