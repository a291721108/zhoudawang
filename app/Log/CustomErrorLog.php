<?php

namespace App\Log;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class CustomErrorLog
{
    protected $logger;

    public function __construct($logFilePath, $logLevel = Logger::DEBUG)
    {
        $this->logger = new Logger('custom');

        $handler = new StreamHandler($logFilePath, $logLevel);
        $handler->setFormatter(new LineFormatter("[%datetime%] [%level_name%] %message%\n"));

        $this->logger->pushHandler($handler);
    }

    public function error($message, $context = [])
    {
        $this->logger->error($message, $context);
    }

    public function info($message, $context = [])
    {
        $this->logger->info($message, $context);
    }

    public function debug($message, $context = [])
    {
        $this->logger->debug($message, $context);
    }

}
