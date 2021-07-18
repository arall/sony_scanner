<?php

namespace App\Libs\Modules\Audit\Docker;

use App\Libs\Contracts\Modules\Audit;
use App\Libs\Contracts\Modules\Traits\Process;
use Exception;

/**
 * Docker Private Key World Readable Audit Module.
 *
 * Looks for private key files with world readable permissions.
 */
class PrivateKeyWorldReadable extends Audit
{
    use Process;

    /**
     * {@inheritdoc}
     */
    protected $vulnerabilityCode = 'PRIVATE_KEY_WORLD_READABLE';

    /**
     * Command to execute in the docker container
     */
    const COMMAND = 'find / -type f -perm -444 -exec grep "BEGIN PRIVATE KEY" {} -s -l -I \; 2>/dev/null';

    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        $output = $this->runProcess(['docker', 'exec', $this->model->name, 'sh', '-c', 'echo "hello"']);
        if ($output != "hello\n") {
            throw new Exception('Docker connection failed');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $output = $this->runProcess(['docker', 'exec', $this->model->name, 'sh', '-c', self::COMMAND], true);
        $files = explode(PHP_EOL, $output);
        if (empty($files)) {
            return;
        }

        foreach (array_filter($files) as $file) {
            $this->addFinding($file);
        }
    }
}
