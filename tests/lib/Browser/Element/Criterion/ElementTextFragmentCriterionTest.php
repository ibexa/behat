<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Behat\Browser\Element\Criterion;

use Ibexa\Behat\Browser\Element\Criterion\ElementTextFragmentCriterion;
use Ibexa\Behat\Browser\Locator\CSSLocator;
use Ibexa\Tests\Behat\Browser\Element\BaseTestCase;
use PHPUnit\Framework\Assert;

class ElementTextFragmentCriterionTest extends BaseTestCase
{
    /**
     * @dataProvider dataProviderTestMatches
     */
    public function testMatches(string $elementText, bool $shouldMatch): void
    {
        $criterion = new ElementTextFragmentCriterion('text');
        $element = $this->createElement($elementText);
        Assert::assertEquals($shouldMatch, $criterion->matches($element));
    }

    public static function dataProviderTestMatches(): array
    {
        return [
            ['text', true],
            ['thisIsALongertext', true],
            ['thisStringDoesNotContainText', false],
        ];
    }

    public function testGetErrorMessageWhenOtherElementFound(): void
    {
        $criterion = new ElementTextFragmentCriterion('expectedText');
        $nonMatchingElement = $this->createElement('actualText');
        $criterion->matches($nonMatchingElement);

        Assert::assertEquals(
            "Could not find element with text containing: 'expectedText'. Found texts: actualText instead. CSS locator 'id': 'selector'.",
            $criterion->getErrorMessage(new CSSLocator('id', 'selector'))
        );
    }

    public function testGetErrorMessageWhenNoElementFound(): void
    {
        $criterion = new ElementTextFragmentCriterion('expectedText');

        Assert::assertEquals(
            "Could not find element with text containing: 'expectedText'. Collection is empty. CSS locator 'id': 'selector'.",
            $criterion->getErrorMessage(new CSSLocator('id', 'selector'))
        );
    }
}

class_alias(ElementTextFragmentCriterionTest::class, 'EzSystems\Behat\Test\Browser\Element\Criterion\ElementTextFragmentCriterionTest');
