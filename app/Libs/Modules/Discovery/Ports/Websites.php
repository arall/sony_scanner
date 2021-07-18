<?php

namespace App\Libs\Modules\Discovery\Ports;

use App\Libs\Contracts\Modules\Module;
use App\Models\Website;
use App\Libs\Contracts\Modules\Traits\Http;
use Exception;

/**
 * Ports Websites Discovery Module.
 *
 * Checks if a wesite is served on a specific port.
 */
class Websites extends Module
{
    use Http;

    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if (!strstr($this->model->service, 'http')) {
            throw new Exception('Not a http service');
        }
    }

    /**
     * Generate the URL.
     *
     * @param string $service
     * @return string
     */
    protected function generateURL($service = null)
    {
        // Add the protocol prefix
        if (!$service) {
            $service = 'http';
            if (strstr($this->model->service, 'https')) {
                $service .= 's';
            }
        }

        $url = $service . '://' . $this->model->host->name . ':' . $this->model->port;

        return  $url;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $url = $this->generateURL();
        $response = $this->request('GET', $url);

        // Bad request?
        if ($response->status() == 400 && strstr($response->body(), "HTTP request was sent to HTTPS")) {
            $url = $this->generateURL('https');
            $response = $this->request('GET', $url);
            if ($response->failed()) {
                return;
            }
        } elseif ($response->failed()) {
            return;
        }

        // Create website
        $website = Website::firstOrCreate(['url' => $url]);
        if (!$website->hosts()->where('host_id', $this->model->host->id)->wherePivot('port_id', $this->model->id)->exists()) {
            $website->hosts()->syncWithoutDetaching([
                [
                    'host_id' => $this->model->host->id,
                    'port_id' => $this->model->id
                ]
            ]);
        }

        $this->outputDetail('Website', $website->url);
        $this->items[] = $website;
    }
}
