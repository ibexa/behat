<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Locator;

use Ibexa\Behat\Browser\Locator\Validator\CssLocatorValidator;

class CSSLocator extends BaseLocator
{
    public function __construct(string $identifier, string $selector)
    {
        parent::__construct($identifier, $selector);
        $validator = new CssLocatorValidator();
        if (str_contains($selector, '%d') || str_contains($selector, '%s')) {
            $validator->validate(new self($identifier, str_replace(['%d', '%s'], '1', $selector)));
        } else {
            $validator->validate($this);
        }
    }

    public function getType(): string
    {
        return 'css';
    }

    public static function empty(): CSSLocator
    {
        return new CSSLocator('empty', 'html');
    }
}
