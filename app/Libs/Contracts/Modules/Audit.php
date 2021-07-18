<?php

namespace App\Libs\Contracts\Modules;

use App\Models\Vulnerability;
use Exception;

/**
 * Audit Module Abstract class.
 */
abstract class Audit extends Module
{
    /**
     * Vulnerability code.
     *
     * @var string
     */
    protected $vulnerabilityCode;

    /**
     * Obtained findings.
     *
     * @var App\Models\Finding[]
     */
    protected $findings = [];

    /**
     * {@inheritdoc}
     */
    protected function finish()
    {
        foreach ($this->findings as $finding) {
            $value = $finding->title;
            if ($finding->details) {
                $value .= ': ' . $finding->details;
            }
            $this->outputDetail('Finding', $value);
        }
    }

    /**
     * Add a Finding to the target model.
     *
     * @param string $details
     * @param string $vulnerabilityCode
     * @throws Exception
     */
    protected function addFinding($details = null, string $vulnerabilityCode = null)
    {
        $vulnerabilityCode = $vulnerabilityCode ?: $this->vulnerabilityCode;
        if (!$vulnerabilityCode) {
            throw new Exception('Missing vulnerability code');
        }

        $vulnerability = Vulnerability::where('code', $vulnerabilityCode)->first();

        $target = $this->model;

        $data = [
            'vulnerability_id' => $vulnerability->id,
        ];

        if ($details) {
            $data['details'] = $details;
        }

        $finding = $target->findings()->firstOrNew($data);

        if ($details) {
            $finding->details = $details;
        }

        $finding->title = $vulnerability->name;

        if ($vulnerability->severity) {
            $finding->severity()->associate($vulnerability->severity);
        }

        $finding->save();

        $this->findings[] = $finding;
    }
}
