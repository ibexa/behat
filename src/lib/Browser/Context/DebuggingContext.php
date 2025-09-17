<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Context;

use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\BeforeStepScope;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Testwork\Tester\Result\TestResult;
use Ibexa\Behat\Core\Log\Failure\TestFailureData;
use Ibexa\Behat\Core\Log\KnownIssuesRegistry;
use Ibexa\Behat\Core\Log\TestLogProvider;
use Psr\Log\LoggerInterface;

class DebuggingContext extends RawMinkContext
{
    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var string */
    private $logDir;

    /** @var \Ibexa\Behat\Core\Log\KnownIssuesRegistry */
    private $knownIssuesRegistry;

    /** @var \Behat\Testwork\Tester\Result\TestResult */
    private $failedStepResult;

    public function __construct(
        LoggerInterface $logger,
        string $logDir,
        KnownIssuesRegistry $knownIssuesRegistry
    ) {
        $this->logger = $logger;
        $this->logDir = $logDir;
        $this->knownIssuesRegistry = $knownIssuesRegistry;
    }

    /** @BeforeScenario
     */
    public function logStartingScenario(BeforeScenarioScope $scope)
    {
        $this->logger->error(sprintf('Behat: Starting Scenario "%s"', $scope->getScenario()->getTitle()));
    }

    /** @BeforeStep
     */
    public function logStartingStep(BeforeStepScope $scope)
    {
        $this->logger->error(sprintf('Behat: Starting Step "%s"', $scope->getStep()->getText()));
    }

    /** @AfterScenario
     */
    public function logEndingScenario(AfterScenarioScope $scope)
    {
        $this->logger->error(sprintf('Behat: Ending Scenario "%s"', $scope->getScenario()->getTitle()));
    }

    /** @AfterStep
     */
    public function logEndingStep(AfterStepScope $scope)
    {
        $this->logger->error(sprintf('Behat: Ending Step "%s"', $scope->getStep()->getText()));

        if ($scope->getTestResult()->isPassed()) {
            return;
        }

        $this->failedStepResult = $scope->getTestResult();
    }



    /** @AfterStep */
    public function getLogsAfterFailedStep(AfterStepScope $scope)
    {
        if ($scope->getTestResult()->getResultCode() !== TestResult::FAILED) {
            return;
        }

        $filename = $this->takeScreenshot($scope);
        $testLogProvider = new TestLogProvider($this->getSession(), $this->logDir);
        $applicationsLogs = $testLogProvider->getApplicationLogs();
        $browserLogs = $testLogProvider->getBrowserLogs();

        $failureData = new TestFailureData(
            $this->failedStepResult,
            $applicationsLogs,
            $browserLogs,
            $filename
        );

        $failureAnalysisResult = $this->knownIssuesRegistry->isKnown($failureData);
        if ($failureAnalysisResult->isKnownFailure()) {
            $this->display(sprintf("Known failure detected! JIRA: %s\n\n", $failureAnalysisResult->getJiraReference()));
        }


        $this->display($this->formatForDisplay($browserLogs, 'JS Console errors:'));
        $this->display($this->formatForDisplay($applicationsLogs, 'Application logs:'));
        $this->display($this->formatForDisplay($filename ? [$filename] : [], 'Screenshot:'));
        $this->display($this->formatForDisplay($filename ? ['file://' . realpath($filename)] : [], 'Screenshot:'));
    }

    private function takeScreenshot(AfterStepScope $scope): string
    {
        $screenshotDir = getenv('GITHUB_WORKSPACE') ? getenv('GITHUB_WORKSPACE') . '/behat-output' : 'behat-output';
        if (!is_dir($screenshotDir)) {
            mkdir($screenshotDir, 0777, true);
        }
        $scenarioTitle = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $scope->getFeature()->getTitle() . '_' . $scope->getStep()->getText());
        $filename = sprintf('%s/%s_%s.png', $screenshotDir, date('Ymd_His'), $scenarioTitle);
        file_put_contents($filename, $this->getSession()->getScreenshot());
        $this->logger->error(sprintf('Screenshot saved at: %s', realpath($filename)));
        return $filename;
    }

    private function formatForDisplay(array $logEntries, string $sectionName)
    {
        $output = sprintf('%s' . PHP_EOL, $sectionName);

        if (empty($logEntries)) {
            $output .= sprintf("\t No logs for this section.") . PHP_EOL;
        }

        foreach ($logEntries as $logEntry) {
            $output .= sprintf("\t%s" . PHP_EOL, $logEntry);
        }

        return $output;
    }

    private function display(string $message): void
    {
        echo $message;
    }
}

