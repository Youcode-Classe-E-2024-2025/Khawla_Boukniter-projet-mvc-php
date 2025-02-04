<?php

namespace App\Core;

class Logger
{
    private $logFile;

    public function __construct()
    {
        $this->logFile = __DIR__ . '/../../logs/app.log';

        // Create logs directory if it doesn't exist
        if (!is_dir(__DIR__ . '/../../logs')) {
            mkdir(__DIR__ . '/../../logs', 0777, true);
        }
    }

    public function info(string $message): void
    {
        $this->log('INFO', $message);
    }

    public function error(string $message): void
    {
        $this->log('ERROR', $message);
    }

    public function debug(string $message): void
    {
        $this->log('DEBUG', $message);
    }

    private function log(string $level, string $message): void
    {
        $date = date('Y-m-d H:i:s');
        $logMessage = "[$date] [$level] $message" . PHP_EOL;
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }
}
