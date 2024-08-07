<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Behat\Browser\Element\Condition;

use Ibexa\Behat\Browser\Element\Condition\ElementsCountGreaterThanCondition;
use Ibexa\Behat\Browser\Locator\CSSLocator;
use Ibexa\Tests\Behat\Browser\Element\BaseTestCase;
use PHPUnit\Framework\Assert;

class ElementsCountGreaterThanConditionTest extends BaseTestCase
{
    public function testExpectedCountPresent(): void
    {
        $searchedElementLocator = new CSSLocator('searched-id', 'searched-test');
        $condition = new ElementsCountGreaterThanCondition(
            $this->createElementWithChildElement('root', $searchedElementLocator, $this->createElement('ChildText')),
            $searchedElementLocator,
            0
        );

        Assert::assertTrue($condition->isMet());
    }

    public function testExpectedCountNotPresent(): void
    {
        $searchedElementLocator = new CSSLocator('searched-id', 'searched-test');
        $condition = new ElementsCountGreaterThanCondition(
            $this->createElementWithChildElement('root', $searchedElementLocator, $this->createElement('ChildText')),
            $searchedElementLocator,
            1
        );
        $invokingElement = $this->createElement('Test');
        $invokingElement->method('getTimeout')->willReturn(5);

        Assert::assertFalse($condition->isMet());
        Assert::assertEquals(
            "The found number of items (1) matching CSS locator 'searched-id': 'searched-test' was not greater than expected value (1). Timeout value: 5 seconds.",
            $condition->getErrorMessage($invokingElement)
        );
    }
}
