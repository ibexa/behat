<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Driver;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Mink\WebdriverClassicDriver\WebdriverClassicDriver as BaseWebdriverClassicDriver;

/**
 * Exposes the underlying php-webdriver instance, which the upstream driver keeps protected.
 * Needed for Chrome DevTools access and browser console log retrieval.
 */
final class WebdriverClassicDriver extends BaseWebdriverClassicDriver
{
    public function getRemoteWebDriver(): RemoteWebDriver
    {
        return $this->getWebDriver();
    }
}
