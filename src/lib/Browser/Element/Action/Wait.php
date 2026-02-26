<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element\Action;

use Ibexa\Behat\Browser\Element\ElementInterface;

class Wait implements ActionInterface
{
    private int $timeMilliseconds;

    public function __construct(int $timeMilliseconds)
    {
        $this->timeMilliseconds = $timeMilliseconds;
    }

    public function execute(ElementInterface $element): void
    {
        usleep(100 * $this->timeMilliseconds);
    }
}
