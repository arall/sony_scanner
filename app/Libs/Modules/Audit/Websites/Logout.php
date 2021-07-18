<?php

namespace App\Libs\Modules\Audit\Websites;

use App\Libs\Contracts\Modules\Audit;
use App\Libs\Contracts\Modules\Traits\Http;
use Exception;


/**
 * Websites Logout Audit Module.
 *
 * Checks if the cookies are invalidated after logout.
 */
class Logout extends Audit
{
    use Http;

    protected $vulnerabilityCode = 'SESSION_FIXATION';

    /**
     * Website auth settings.
     *
     * @var array
     */
    protected $auth;

    /**
     * Auth cookies.
     *
     * @var string
     */
    protected $cookie;

    /**
     * {@inheritdoc}
     */
    protected function init()
    {
        $this->auth = $this->model->auth;
    }

    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if (empty($this->model->auth)) {
            throw new Exception('Website auth not configured');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->auth();

        $this->loggout();

        if ($this->checkAuth()) {
            $this->addFinding();
        }
    }

    /**
     * Perform authentication.
     *
     * @throws Exception
     */
    private function auth()
    {
        $response = $this->request(
            $this->auth['method'],
            $this->model->url . '/' . $this->auth['uri'],
            $this->auth['data'],
            null,
            ['allow_redirects' => false]
        );

        if (!$response->hasHeader('Set-Cookie')) {
            throw new Exception('Authentication request did not return any cookie');
        }

        // @todo this can be improved for websites returning multiple set-cookie headers
        $cookies = $response->getHeader('Set-Cookie');
        $this->cookie = implode('; ', $cookies);
    }

    /**
     * Checks if an userId cookie is valid.
     *
     * @return bool
     */
    private function checkAuth()
    {
        $response = $this->request(
            'GET',
            $this->model->url . '/' . $this->auth['check'],
            null,
            ['Cookie' => $this->cookie]
        );

        return $response->successful();
    }

    /**
     * Perform loggout.
     *
     * @return bool
     */
    private function loggout()
    {
        $response = $this->request('GET', $this->model->url . '/' . $this->auth['loggout']);

        return $response->successful();
    }
}
