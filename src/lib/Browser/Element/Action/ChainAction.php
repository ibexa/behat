<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element\Action;

use Ibexa\Behat\Browser\Element\ElementInterface;

class ChainAction implements ActionInterface
{
    /** @var ActionInterface[] */
    private array $actions;

    public function __construct(array $actions)
    {
        $this->actions = $actions;
    }

    public function execute(ElementInterface $element): void
    {
        foreach ($this->actions as $action) {
            $action->execute($element);
        }
    }
}
