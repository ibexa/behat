<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Behat\Subscriber;

use Behat\Behat\EventDispatcher\Event\BeforeScenarioTested;
use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Facebook\WebDriver\Exception\UnknownErrorException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class StartScenarioSubscriber implements EventSubscriberInterface
{
    private const RETRY_LIMIT = 2;

    public const PRIORITY = -1000;

    private KernelInterface $kernel;

    private int $width;

    private int $height;

    public function __construct(KernelInterface $kernel, int $width, int $height)
    {
        $this->kernel = $kernel;
        $this->width = $width;
        $this->height = $height;
    }

    public static function getSubscribedEvents()
    {
        return [
            ScenarioTested::BEFORE => ['resizeWindow', self::PRIORITY],
            ExampleTested::BEFORE => ['resizeWindow', self::PRIORITY],
        ];
    }

    public function resizeWindow(BeforeScenarioTested $event): void
    {
        if (!$event->getScenario()->hasTag('javascript') && !$event->getFeature()->hasTag('javascript')) {
            return;
        }

        $session = $this->kernel->getContainer()->get('behat.mink.default_session');
        if (!$session->isStarted()) {
            $session->start();
        }

        $this->executeWithRetry(function () use ($session): void {
            $session->resizeWindow($this->width, $this->height);
        });
    }

    /**
     * @template T
     *
     * @param callable(mixed ...$args): T $fn
     *
     * @return T
     */
    private function executeWithRetry(callable $fn)
    {
        $counter = 0;
        while (true) {
            try {
                return $fn();
            } catch (UnknownErrorException $e) {
                if ($counter > self::RETRY_LIMIT) {
                    throw $e;
                }
                ++$counter;
                usleep(100000 * 2 ** $counter);
            }
        }
    }
}
