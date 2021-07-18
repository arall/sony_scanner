<?php

namespace App\Libs\Modules\Audit\Websites;

use App\Libs\Contracts\Modules\Audit;
use App\Libs\Contracts\Modules\Traits\Http;
use Exception;

/**
 * Websites Cookies Audit Module.
 *
 * Checks if the website cookies are using the security flags.
 */
class Cookies extends Audit
{
    use Http;

    /**
     * List of security cookie flags that suppose an issue if missing.
     *
     * @var array
     */
    const MISSING_COOKIE_FLAGS_DEFINITIONS = [
        'HTTPOnly' => 'COOKIES_HTTPONLY',
        'Secure'   => 'COOKIES_SECURE',
        'SameSite' => 'COOKIES_SAMESITE',
    ];

    /**
     * HTTP response
     *
     * @var \Illuminate\Http\Client\Response
     */
    private $response;

    /**
     * {@inheritdoc}
     */
    protected function init()
    {
        $auth = $this->model->auth;

        if (!empty($auth)) {
            $this->response = $this->request(
                $auth['method'],
                $this->model->url . '/' . $auth['uri'],
                $auth['data'],
                ['allow_redirects' => false]
            );
        } else {
            $this->response = $this->request('GET', $this->model->url);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if (!$this->response->hasHeader('Set-Cookie')) {
            throw new Exception('Website is not using cookies');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        foreach (self::MISSING_COOKIE_FLAGS_DEFINITIONS as $flag => $code) {
            if (!$this->hasCookieFlag($flag)) {
                $this->addFinding(null, $code);
            }
        }
    }

    /**
     * Checks if the response cookies have a flag.
     * It must be present in all cookies!
     *
     * @param  string $flag
     * @return bool
     */
    public function hasCookieFlag(string $flag)
    {
        foreach ($this->response->getHeader('Set-Cookie') as $cookie) {
            if ($flag === 'SameSite') {
                if (!preg_match('/[\s;]' . $flag . '=/i', $cookie)) {
                    return false;
                }
            } elseif (!preg_match('/[\s;]' . $flag . '($|\s|;)/i', $cookie)) {
                return false;
            }
        }

        return true;
    }
}
