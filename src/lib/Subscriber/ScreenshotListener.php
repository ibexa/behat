<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Subscriber;

use Behat\Behat\EventDispatcher\Event\StepTested;
use Behat\Mink\Mink;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ScreenshotListener implements EventSubscriberInterface
{
private $mink;

    public function __construct(Mink $mink)
    {
        $this->mink = $mink;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            StepTested::AFTER => ['takeScreenshotOnFailure', -10],
        ];
    }

    public function takeScreenshotOnFailure(StepTested $event): void
    {
        if ($event->getTestResult()->isPassed()) {
            return;
        }

        $session = $this->mink->getSession('panther');
        if ($session->isStarted()) {
            $screenshot = $session->getScreenshot();
            $dir = '/app/tests/_output';
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            $filename = $dir . '/screenshot_' . uniqid() . '.png';
            file_put_contents($filename, $screenshot);
        }
    }
}
