<?php

namespace App\Libs\Modules\Audit\Repositories;

use App\Libs\Contracts\Modules\Audit;
use App\Libs\Contracts\Modules\Traits\Process;
use Exception;

/**
 * Repositories Semgrep Audit Module.
 *
 * Uses https://semgrep.dev/ to perform static security analysis on repositories.
 */
class Semgrep extends Audit
{
    use Process;

    /**
     * {@inheritdoc}
     */
    protected $vulnerabilityCode = 'VULNERABLE_CODE';

    /**
     * Path to rules.
     */
    const RULES = 'app/settings/semgrep/rules.yml';

    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if (!file_exists($this->model->path)) {
            throw new Exception('Path does not exist');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $output = $this->runProcess(
            [
                env('TOOLS_SEMGREP'),
                '--config', storage_path(self::RULES),
                '--json',
                $this->model->path
            ],
            true
        );

        $data = json_decode($output);

        $this->store($data);
    }

    /**
     * Store the semgrep results.
     *
     * @param object $data
     */
    private function store(object $data)
    {
        foreach ($data->results as $result) {

            $details = 'Path: ' . $result->path . PHP_EOL .
                'Line: ' . $result->start->line . PHP_EOL .
                'Message: ' . $result->extra->message;

            $this->addFinding($details);
        }
    }
}
