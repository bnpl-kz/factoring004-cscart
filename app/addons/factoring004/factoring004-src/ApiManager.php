<?php

namespace BnplPartners\Factoring004Payment;

use BnplPartners\Factoring004\Api;
use BnplPartners\Factoring004\Auth\BearerTokenAuth;

trait ApiManager
{
    public $api;

    public function createApi(string $baseUrl, string $token)
    {
        $this->api = Api::create($baseUrl, new BearerTokenAuth($token));
    }

    public function log($log)
    {
        file_put_contents(
            DIR_ROOT . '/app/addons/factoring004/logs/' . date('Y-m-d') .'.log',
            date('H:i:s') . PHP_EOL . $log,
            FILE_APPEND
        );
    }
}