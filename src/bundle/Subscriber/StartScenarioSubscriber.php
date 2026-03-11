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

class StartScenarioSubscriber implements EventSubscriberInterface
{
    private const RETRY_LIMIT = 2;

    public const PRIORITY = -1000;

    public static function getSubscribedEvents()
    {
        return [
            ScenarioTested::BEFORE => ['resizeWindow', self::PRIORITY],
            ExampleTested::BEFORE => ['resizeWindow', self::PRIORITY],
        ];
    }

    public function resizeWindow(BeforeScenarioTested $event): void
    {
        // No usages, so simplest fix is to remove the function
    }
}
