<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Behat\Browser\Element\Condition;

use Ibexa\Behat\Browser\Element\Condition\ElementHasTextCondition;
use Ibexa\Behat\Browser\Locator\CSSLocator;
use Ibexa\Tests\Behat\Browser\Element\BaseTestCase;
use PHPUnit\Framework\Assert;

class ElementHasTextConditionTest extends BaseTestCase
{
    public function testElementDoesHaveText(): void
    {
        $searchedElementLocator = new CSSLocator('have-text-id', 'have-text-selector');
        $condition = new ElementHasTextCondition(
            $this->createElementWithChildElement(
                'root',
                $searchedElementLocator,
                $this->createElement('DummyText')
            ),
            $searchedElementLocator,
            'DummyText'
        );

        Assert::assertTrue($condition->isMet());
    }

    public function testElemetDoesNotHaveText(): void
    {
        $searchedElementLocator = new CSSLocator('not-have-text-id', 'not-have-text-selector');
        $condition = new ElementHasTextCondition(
            $this->createElementWithChildElement(
                'root',
                new CSSLocator('dummy-id', 'dummy-selector'),
                $this->createElement('DummyText')
            ),
            $searchedElementLocator,
            'Dummy1Text2'
        );

        Assert::assertFalse($condition->isMet());
    }
}
