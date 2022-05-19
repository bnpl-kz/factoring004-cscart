<?php

namespace BnplPartners\Factoring004Payment;

use BnplPartners\Factoring004\Api;
use BnplPartners\Factoring004\Auth\BearerTokenAuth;
use BnplPartners\Factoring004\Transport\GuzzleTransport;
use BnplPartners\Factoring004\Transport\TransportInterface;
use Psr\Log\LoggerInterface;

trait ApiManager
{
    protected LoggerInterface $logger;

    /**
     * @var \BnplPartners\Factoring004\Api
     */
    public $api;

    public function createApi(string $baseUrl, string $token, bool $debug = false)
    {
        $this->logger = (new LoggerFactory())->setDebug($debug)->createLogger();
        $this->api = Api::create($baseUrl, new BearerTokenAuth($token), $this->createTransport());
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
