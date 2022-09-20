<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element\Condition;

use Ibexa\Behat\Browser\Element\BaseElementInterface;
use Ibexa\Behat\Browser\Element\Mapper\ElementTextMapper;
use Ibexa\Behat\Browser\Locator\LocatorInterface;

class ElementHasTextCondition implements ConditionInterface
{
    /**
     * @var \Ibexa\Behat\Browser\Element\BaseElementInterface
     */
    private $searchedNode;

    /**
     * @var \Ibexa\Behat\Browser\Locator\LocatorInterface
     */
    private $elementLocator;

    /**
     * @var string
     */
    private $expectedText;

    /**
     * @var string[]
     */
    private $foundElementsText;

    public function __construct(BaseElementInterface $searchedNode, LocatorInterface $elementLocator, string $expectedText)
    {
        $this->searchedNode = $searchedNode;
        $this->elementLocator = $elementLocator;
        $this->expectedText = $expectedText;
    }

    public function isMet(): bool
    {
        $currentTimeout = $this->searchedNode->getTimeout();
        $this->searchedNode->setTimeout(0);
        $elements = $this->searchedNode->findAll($this->elementLocator);
        $this->searchedNode->setTimeout($currentTimeout);
        $this->$foundElementsText = $elements->mapBy(new ElementTextMapper());

        return in_array($this->expectedText, $this->$foundElementsText);
    }

    public function getErrorMessage(BaseElementInterface $invokingElement): string
    {
        return sprintf(
            "The expected text '%s' matching %s locator '%s': '%s' was not found. Timeout value: %d seconds. Found values: %s",
            $this->expectedText,
            strtoupper($this->elementLocator->getType()),
            $this->elementLocator->getIdentifier(),
            $this->elementLocator->getSelector(),
            $invokingElement->getTimeout(),
            implode(',', $this->$foundElementsText)
        );
    }
}
