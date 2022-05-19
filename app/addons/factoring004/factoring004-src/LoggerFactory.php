<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004Payment;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;
use Wa72\SimpleLogger\FileLogger;

class LoggerFactory
{
    private const DIR_LOGS = DIR_ROOT . '/app/addons/factoring004/logs/';
    private const LOG_EXTENSION = '.log';

    private string $logFile;
    private bool $debug = false;

    public function __construct()
    {
        $this->logFile = static::DIR_LOGS . date('Y-m-d') . static::LOG_EXTENSION;
    }

    public static function create(): LoggerFactory
    {
        return new static();
    }

    public function setDebug(bool $debug): LoggerFactory
    {
        $this->debug = $debug;
        return $this;
    }

    public function createLogger(): LoggerInterface
    {
        if (!is_writable(static::DIR_LOGS)) {
            return new NullLogger();
        }

        return new FileLogger($this->logFile, $this->debug ? LogLevel::DEBUG : LogLevel::INFO);
    }
}
