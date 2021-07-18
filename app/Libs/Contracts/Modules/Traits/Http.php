<?php

namespace App\Libs\Contracts\Modules\Traits;

use Illuminate\Support\Facades\Http as HttpClient;
use \Exception;

/**
 * Http Trait for Modules.
 */
trait Http
{
    /**
     * Perform a HTTP request.
     *
     * @param string $method
     * @param string $url
     * @param array $data
     * @param array $headers
     * @param array $options
     * @throws Exception
     * @return \Illuminate\Http\Client\Response
     */
    private function request($method, $url, $data = [], $headers = [], $options = [])
    {
        $method = strtolower($method);

        // Avoid injection
        if (!in_array($method, ['get', 'head', 'post', 'put', 'delete', 'options', 'patch'])) {
            throw new Exception('Invalid HTTP method: ' . $method);
        }

        // $this->output('HTTP: ' . $method . ' - ' . $url . ' - ' . json_encode($data) . ' - ' . json_encode($options));

        $options = array_merge([
            'verify' => config('scanner.http.verify'),
            'timeout' => config('scanner.http.timeout'),
        ], $options);

        $client = HttpClient::withOptions($options);

        if ($method == 'post' && !empty($data)) {
            $client->asForm();
        }

        if (!empty($headers)) {
            $client->withHeaders($headers);
        }

        return $client->$method($url, $data);
    }
}
