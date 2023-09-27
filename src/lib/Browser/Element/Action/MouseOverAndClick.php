<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element\Action;

use Ibexa\Behat\Browser\Element\ElementInterface;

class MouseOverAndClick implements ActionInterface
{
    public function execute(ElementInterface $element): void
    {
        $action = new ChainAction([
            new MouseOver(),
            new Wait(50),
            new Click(),
        ]);
        $action->execute($element);
    }
}
