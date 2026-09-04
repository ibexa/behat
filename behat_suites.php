<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

use Behat\Config\Config;
use Behat\Config\Profile;
use Behat\Config\Suite;
use Ibexa\Behat\API\Context\ContentContext;
use Ibexa\Behat\API\Context\ContentTypeContext;
use Ibexa\Behat\API\Context\LanguageContext;
use Ibexa\Behat\API\Context\ObjectStateContext;
use Ibexa\Behat\API\Context\RoleContext;
use Ibexa\Behat\API\Context\TestContext;
use Ibexa\Behat\API\Context\UserContext;
use Ibexa\Behat\Core\Context\ConfigurationContext;
use Ibexa\Behat\Core\Context\FileContext;

$setupContexts = [
    LanguageContext::class,
    TestContext::class,
    ConfigurationContext::class,
];

return (new Config())
    ->withProfile((new Profile('service'))
        ->withSuite((new Suite('examples'))
            ->withContexts(
                ContentContext::class,
                ContentTypeContext::class,
                LanguageContext::class,
                ObjectStateContext::class,
                RoleContext::class,
                TestContext::class,
                UserContext::class,
                ConfigurationContext::class
            )
            ->withPaths('%paths.base%/vendor/ibexa/behat/features/examples')))
    ->withProfile((new Profile('setup'))
        ->withSuite((new Suite('volume-testing'))
            ->withContexts(
                ContentContext::class,
                ContentTypeContext::class,
                LanguageContext::class,
                RoleContext::class,
                TestContext::class,
                UserContext::class
            )
            ->withPaths('vendor/ibexa/behat/features/setup/volume/'))
        ->withSuite((new Suite('richtext-configuration'))
            ->withContexts(
                ConfigurationContext::class,
                FileContext::class
            )
            ->withPaths(
                'vendor/ibexa/behat/features/setup/richtextConfiguration/custom_tags.feature',
                'vendor/ibexa/behat/features/setup/richtextConfiguration/custom_styles.feature'
            ))
        ->withSuite((new Suite('personas'))
            ->withContexts(
                ContentContext::class,
                ContentTypeContext::class,
                RoleContext::class,
                TestContext::class,
                UserContext::class
            )
            ->withPaths('%paths.base%/vendor/ibexa/behat/features/personas'))
        ->withSuite((new Suite('URIElement'))
            ->withContexts(...$setupContexts)
            ->withPaths('%paths.base%/vendor/ibexa/behat/features/setup/siteaccessMatcher/URIElement.feature'))
        ->withSuite((new Suite('MapHost'))
            ->withContexts(...$setupContexts)
            ->withPaths('%paths.base%/vendor/ibexa/behat/features/setup/siteaccessMatcher/MapHost.feature'))
        ->withSuite((new Suite('MapURI'))
            ->withContexts(...$setupContexts)
            ->withPaths('%paths.base%/vendor/ibexa/behat/features/setup/siteaccessMatcher/MapURI.feature'))
        ->withSuite((new Suite('CompoundMapURIMapHost'))
            ->withContexts(...$setupContexts)
            ->withPaths('%paths.base%/vendor/ibexa/behat/features/setup/siteaccessMatcher/CompoundMapURIMapHost.feature'))
        ->withSuite((new Suite('multirepository'))
            ->withContexts(ConfigurationContext::class)
            ->withPaths('%paths.base%/vendor/ibexa/behat/features/setup/multirepository'))
        ->withSuite((new Suite('login-methods'))
            ->withContexts(ConfigurationContext::class)
            ->withPaths('%paths.base%/vendor/ibexa/behat/features/setup/loginMethods'))
        ->withSuite((new Suite('content-translation'))
            ->withContexts(
                ConfigurationContext::class,
                LanguageContext::class,
                TestContext::class
            )
            ->withPaths('%paths.base%/vendor/ibexa/behat/features/setup/contentTranslation')));
