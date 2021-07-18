<?php

namespace App\Libs\Modules\Audit\Websites;

use App\Libs\Contracts\Modules\Audit;
use App\Libs\Contracts\Modules\Traits\Http as HttpTrait;

/**
 * Websites HTTP Audit Module.
 *
 * Checks if the website is served over HTTP.
 */
class HTTP extends Audit
{
    use HttpTrait;

    /**
     * {@inheritdoc}
     */
    protected $vulnerabilityCode = 'HTTP';

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        // Force HTTP on HTTPS websites
        $url = str_replace('https://', 'http://', $this->model->url);

        $response = $this->request('GET', $url);

        if ($response->failed()) {
            return;
        }

        // Check if HTTP response headers are redirecting to HTTPS
        if (!strstr($response->effectiveUri(), 'https://')) {
            $this->addFinding();
        }
    }
}
