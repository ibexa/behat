<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace  Ibexa\Tests\Behat\Browser\Locator\Validator;

use Ibexa\Behat\Browser\Locator\CSSLocator;
use Ibexa\Behat\Browser\Locator\Validator\CssLocatorValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\CssSelector\Exception\ParseException;

class CssLocatorValidatorTest extends TestCase
{
    private CssLocatorValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new CssLocatorValidator();
    }

    /**
     * @dataProvider provideValidSelectors
     */
    public function testValidationPasses(string $selector): void
    {
        $this->validator->validate(new CSSLocator('test', $selector));
        $this->expectNotToPerformAssertions();
    }

    /**
     * @dataProvider provideInvalidSelectors
     */
    public function testValidationFails(string $selector): void
    {
        $this->expectException(ParseException::class);
        $this->expectExceptionMessage(
            sprintf(
                "Locator '%s' with ID 'test' cannot be used because of limitations of the CssSelector component. See more: https://symfony.com/doc/current/components/css_selector.html#limitations-of-the-cssselector-component",
                $selector
            )
        );
        $this->validator->validate(new CSSLocator('test', $selector));
    }

    public static function provideValidSelectors(): iterable
    {
        return [
            ['div'],
            ['div:nth-of-type(1)'],
            ['div.ibexa-content-field:nth-of-type(3)'],
            ['selector1 section:nth-of-type(2)'],
            ['tr td:nth-of-type(2)'],
            ['[data-id="test"] div.ibexa-field-edit:nth-of-type(3)'],
            ['div.ibexa-details__item:nth-of-type(2) .ibexa-details__item-content'],
            ['.ibexa-version-compare__field-wrapper div.ibexa-content-field:nth-of-type(1) .ibexa-content-field__name'],
            ['div.ibexa-dropdown__wrapper > ul.ibexa-dropdown__selection-info > li:nth-child(1)'],
            ['.ibexa-dropdown__item:nth-child(3)'],
            ['.nav-item:nth-child(2) .nav-link'],
        ];
    }

    public static function provideInvalidSelectors(): iterable
    {
        return [
            ['.ibexa:nth-of-type(1)'],
            ['.ibexa-table__cell:nth-of-type(5),td:nth-of-type(5)'],
            ['.ibexa-available-field-types__list > li:not(.ibexa-available-field-type--hidden) .ibexa-available-field-type__content:nth-of-type(5)'],
            ['.c-finder-branch:nth-of-type(3) .c-finder-leaf'],
            ['.selector .ibexa:nth-of-type(2)'],
            ['.tab-pane.active .ibexa-fieldgroup:nth-of-type(2)'],
            ['div.ibexa-ca-company-tab-company-profile__top-wrapper .ibexa-details__items .ibexa-details__item:nth-of-type(3) .ibexa-details__item-content'],
        ];
    }
}
