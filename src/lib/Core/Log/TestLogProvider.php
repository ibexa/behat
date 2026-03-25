<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Core\Log;

use Behat\Mink\Session;
use Ibexa\Behat\Browser\Filter\BrowserLogFilter;
use OAndreyev\Mink\Driver\WebDriver;

final class TestLogProvider
{
    private const CONSOLE_LOGS_LIMIT = 10;
    private const APPLICATION_LOGS_LIMIT = 25;
    private const LOG_FILE_NAME = 'behat.log';

    private static $LOGS;

    /**
     * @var \Behat\Mink\Session
     */
    private $session;

    /**
     * @var string
     */
    private $logDirectory;

    public function __construct(Session $session, string $logDirectory)
    {
        $this->session = $session;
        $this->logDirectory = $logDirectory;
    }

    public function getBrowserLogs(): array
    {
        $driver = $this->session->getDriver();

        if (!($driver instanceof WebDriver) || !$this->session->isStarted()) {
            return [];
        }

        if ($this->hasCachedLogs()) {
            return $this->getCachedLogs();
        }

        $parsedLogs = $this->parseBrowserLogs($this->getSeleniumLog($driver));
        $this->cacheLogs($parsedLogs);

        return $parsedLogs;
    }

    private function getSeleniumLog(WebDriver $driver): array
    {
        $webDriver = $driver->getWebDriver();
        if (is_object($webDriver) && method_exists($webDriver, 'getLog')) {
            return $webDriver->getLog('browser') ?? [];
        }

        // Log a warning or handle gracefully if getLog is not available
        // error_log('WebDriver does not support getLog method.');
        return [];
    }

    public function getApplicationLogs(): array
    {
        $logReader = new LogFileReader();
        $lines = $logReader->getLastLines(sprintf('%s/%s', $this->logDirectory, self::LOG_FILE_NAME), self::APPLICATION_LOGS_LIMIT);

        $parsedLines = [];
        foreach ($lines as $line) {
            $parsedLine = str_replace([' app.ERROR: Behat:', '[] []'], '', $line);
            $parsedLines[] = $parsedLine;
        }

        return $parsedLines;
    }

    private function parseBrowserLogs(array $logEntries): array
    {
        $filter = new BrowserLogFilter();

        if (empty($logEntries)) {
            return [];
        }

        $errorMessages = array_column($logEntries, 'message');
        $errorMessages = $filter->filter($errorMessages);

        return \array_slice($errorMessages, 0, self::CONSOLE_LOGS_LIMIT);
    }

    private function hasCachedLogs(): bool
    {
        return !empty(self::$LOGS);
    }

    private function getCachedLogs(): array
    {
        return self::$LOGS;
    }

    public static function reset(): void
    {
        self::$LOGS = [];
    }

    private function cacheLogs(array $logs): void
    {
        self::$LOGS = $logs;
    }
}

class_alias(TestLogProvider::class, 'EzSystems\Behat\Core\Log\TestLogProvider');
