<?php

namespace App\Libs\Modules\Audit\Websites;

use App\Libs\Contracts\Modules\Audit;
use Illuminate\Http\Client\Response;
use App\Libs\Contracts\Modules\Traits\Http;

/**
 * Website Exposed VCS Audit Module.
 *
 * Checks if the website is exposing GIT or SVN directories over HTTP.
 */
class ExposedVCS extends Audit
{
    use Http;

    /**
     * {@inheritdoc}
     */
    protected $vulnerabilityCode = 'EXPOSED_VCS';

    /**
     * Vulnerable patterns.
     *
     * @var array
     */
    protected $patterns = [
        '.git/' => ['Index of'],
        '.git/HEAD' => ['refs/heads/'],
        '.git/config' => ['[core]', '[remote', '[branch'],
        '.git/objects' => ['Index of'],
        '.git/logs/HEAD' => ['clone: ', 'checkout: ', 'commit: ', 'pull: '],
        '.svn/' => ['Index of'],
        '.svn/entries' => ['/^\d{1,2}\s?\ndir\n/'],
        '.svn/all-wcprops' => ['/^K\s\d{1,2}\s?\nsvn:/'],
    ];

    /**
     * Results.
     *
     * @var array
     */
    protected $results = [];

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        foreach (array_keys($this->patterns) as $path) {
            $response = $this->request(
                'GET',
                $this->model->url . '/' . $path,
                null,
                null,
                ['allow_redirects' => false]
            );

            if ($this->responseIsVulnerable($response, $path)) {
                $this->addFinding($path);
            }
        }
    }

    /**
     * Check if the response is vulnerable.
     *
     * @param \Illuminate\Http\Client\Response $response
     * @param string $path
     * @return bool
     */
    private function responseIsVulnerable(Response $response, string $path)
    {
        if ($response->failed()) {
            return false;
        }

        foreach ($this->patterns[$path] as $pattern) {
            // Regex pattern
            if ($pattern[0] == '/' && substr($pattern, -1) == '/' && preg_match($pattern, $response->body())) {
                return true;
            } elseif (strstr($response->getBody(), $pattern)) {
                return true;
            }
        }
    }
}
