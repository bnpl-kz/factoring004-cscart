<?php

namespace BnplPartners\Factoring004Payment;

use BnplPartners\Factoring004\Api;
use BnplPartners\Factoring004\Auth\BearerTokenAuth;
use BnplPartners\Factoring004\OAuth\CacheOAuthTokenManager;
use BnplPartners\Factoring004\OAuth\OAuthTokenManager;
use BnplPartners\Factoring004\Transport\GuzzleTransport;
use BnplPartners\Factoring004\Transport\TransportInterface;
use Psr\Log\LoggerInterface;
use Desarrolla2\Cache\File as FileCache;

trait ApiManager
{

    static string $auth_path = '/users/api/v1';

    static string $cache_key = 'factoring004';

    protected LoggerInterface $logger;

    /**
     * @var \BnplPartners\Factoring004\Api
     */
    public Api $api;

    public function createApi(string $baseUrl, string $login, $password, bool $debug = false)
    {
        $this->logger = (new LoggerFactory())->setDebug($debug)->createLogger();
        $tokenManager = new OAuthTokenManager($baseUrl.static::$auth_path, $login, $password);
        $cache = new FileCache(DIR_ROOT . "/app/addons/factoring004/");
        $cacheTokenManager = new CacheOAuthTokenManager($tokenManager, $cache, static::$cache_key);
        $this->api = Api::create($baseUrl, new BearerTokenAuth($cacheTokenManager->getAccessToken()->getAccess()), $this->createTransport());
    }

    public function log($log)
    {
        $this->logger->error($log);
    }

    private function createTransport(): TransportInterface
    {
        $transport = new GuzzleTransport();
        $transport->setLogger($this->logger);

        return $transport;
    }
}
