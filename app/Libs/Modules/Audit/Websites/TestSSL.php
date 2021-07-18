<?php

namespace App\Libs\Modules\Audit\Websites;

use App\Libs\Contracts\Modules\Audit;
use Illuminate\Support\Facades\Storage;
use \App\Libs\Contracts\Modules\Traits\Process;
use Exception;

/**
 * Websites TestSSL Audit Module.
 *
 * Audits SSL/TLS Vulnerabilities by using TestSSL.sh.
 * https://github.com/drwetter/testssl.sh
 */
class TestSSL extends Audit
{
    use Process;

    /**
     * {@inheritdoc}
     */
    protected $vulnerabilityCode = 'SSL_ISSUES';

    /**
     * Path to the temporary output file.
     *
     * @var string
     */
    protected $tmp;

    /**
     * {@inheritdoc}
     */
    protected function init()
    {
        $this->tmp = 'outputs/testssl_' . $this->model->id . '.json';
    }

    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if (!strstr($this->model->url, 'https://')) {
            throw new Exception('Website is not using HTTPS');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        Storage::delete($this->tmp);

        $parse = parse_url($this->model->url);
        $hostname = $parse['host'];
        $port = isset($parse['port']) ? $parse['port'] : 443;
        $target = $hostname . ':' . $port;

        $this->runProcess([
            env('TOOLS_TESTSSL'),
            '-p', '-U', '--mode', 'parallel',
            '--jsonfile', storage_path('app/' . $this->tmp),
            $target
        ]);

        $content = Storage::get($this->tmp);
        Storage::delete($this->tmp);
        $this->store($content);
    }

    /**
     * Store the obtained data.
     *
     * @param string $content
     * @throws \Exception
     */
    private function store(string $content)
    {
        $content = json_decode($content);
        foreach ($content as $entry) {
            if (!isset($entry->severity)) {
                continue;
            }

            if ($entry->severity == 'OK' || $entry->severity == 'INFO') {
                continue;
            }

            $description = $entry->id . ': ' . $entry->finding;

            $this->addFinding($description);
        }
    }
}
