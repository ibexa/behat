<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

use Behat\Config\Config;
use Behat\Config\Extension;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Formatter\PrettyFormatter;
use Behat\Config\GherkinOptions;
use Behat\Config\Profile;
use Behat\Config\Suite;
use Behat\Config\TesterOptions;
use Behat\MinkExtension\Context\MinkContext;
use Behat\MinkExtension\ServiceContainer\MinkExtension;
use DMore\ChromeExtension\Behat\ServiceContainer\ChromeExtension;
use Facebook\WebDriver\Remote\WebDriverBrowserType;
use FriendsOfBehat\SymfonyExtension\ServiceContainer\SymfonyExtension;
use Ibexa\AdminUi\Behat\BrowserContext\AdminUpdateContext;
use Ibexa\AdminUi\Behat\BrowserContext\BookmarkContext;
use Ibexa\AdminUi\Behat\BrowserContext\ContentActionsMenuContext;
use Ibexa\AdminUi\Behat\BrowserContext\ContentPreviewContext;
use Ibexa\AdminUi\Behat\BrowserContext\ContentTreeContext;
use Ibexa\AdminUi\Behat\BrowserContext\ContentTypeContext;
use Ibexa\AdminUi\Behat\BrowserContext\ContentUpdateContext;
use Ibexa\AdminUi\Behat\BrowserContext\ContentViewContext;
use Ibexa\AdminUi\Behat\BrowserContext\DashboardContext;
use Ibexa\AdminUi\Behat\BrowserContext\LanguageContext;
use Ibexa\AdminUi\Behat\BrowserContext\MyDraftsContext;
use Ibexa\AdminUi\Behat\BrowserContext\NavigationContext;
use Ibexa\AdminUi\Behat\BrowserContext\NotificationContext;
use Ibexa\AdminUi\Behat\BrowserContext\ObjectStatesContext;
use Ibexa\AdminUi\Behat\BrowserContext\RolesContext;
use Ibexa\AdminUi\Behat\BrowserContext\SearchContext;
use Ibexa\AdminUi\Behat\BrowserContext\SectionsContext;
use Ibexa\AdminUi\Behat\BrowserContext\SystemInfoContext;
use Ibexa\AdminUi\Behat\BrowserContext\TrashContext;
use Ibexa\AdminUi\Behat\BrowserContext\UDWContext;
use Ibexa\AdminUi\Behat\BrowserContext\UserPreferencesContext;
use Ibexa\Behat\API\Context\ContentContext;
use Ibexa\Behat\API\Context\ContentTypeContext as ApiContentTypeContext;
use Ibexa\Behat\API\Context\LanguageContext as ApiLanguageContext;
use Ibexa\Behat\API\Context\RoleContext;
use Ibexa\Behat\API\Context\TestContext;
use Ibexa\Behat\API\Context\TrashContext as ApiTrashContext;
use Ibexa\Behat\API\Context\UserContext;
use Ibexa\Behat\Browser\Context\AuthenticationContext;
use Ibexa\Behat\Browser\Context\DebuggingContext;
use Ibexa\Behat\Core\Context\ConfigurationContext;
use Ibexa\Behat\Core\Context\FileContext;
use Ibexa\Bundle\Behat\IbexaExtension;
use Ibexa\User\Behat\Context\UserSettingsContext;
use Ibexa\User\Behat\Context\UserSetupContext;
use Liuggio\Fastest\Behat\ListFeaturesExtension\Extension as ListFeaturesExtension;

return (new Config())
    ->import('vendor/ibexa/behat/behat_suites.php')
    ->import('vendor/ibexa/admin-ui/behat_suites.php')
    ->import('vendor/ibexa/content-forms/behat_suites.php')
    ->import('vendor/ibexa/http-cache/behat_suites.php')
    ->import('vendor/ibexa/core/src/bundle/Core/behat_suites.php')
    ->import('vendor/ibexa/user/behat_suites.php')
    ->withProfile((new Profile('default', [
        'suites' => null,
    ]))
        ->withFormatter(new PrettyFormatter())
        ->withGherkinOptions(new GherkinOptions(['cache' => false]))
        ->withTesterOptions((new TesterOptions())
            ->withErrorReporting(E_ALL & ~E_DEPRECATED))
        ->withExtension(new Extension(MinkExtension::class, [
            'files_path' => '%paths.base%/vendor/ibexa/behat/src/lib/Behat/TestFiles/',
            'base_url' => '%env(string:WEB_HOST)%',
            'browserkit_http' => null,
            'javascript_session' => 'selenium',
            'sessions' => [
                'selenium' => [
                    'webdriver_classic' => [
                        'browser' => WebDriverBrowserType::CHROME,
                        'wd_host' => '%env(string:SELENIUM_HOST)%',
                        'capabilities' => [
                            'goog:chromeOptions' => [
                                'args' => [
                                    '--no-sandbox',
                                    '--disable-features=site-per-process',
                                    '--disable-renderer-backgrounding',
                                    '--disable-background-timer-throttling',
                                    '--disable-backgrounding-occluded-windows',
                                ],
                            ],
                        ],
                    ],
                ],
                'chrome' => [
                    'chrome' => [
                        'api_url' => '%env(string:CHROMIUM_HOST)%',
                    ],
                ],
            ],
        ]))
        ->withExtension(new Extension(ChromeExtension::class))
        ->withExtension(new Extension(SymfonyExtension::class, [
            'bootstrap' => 'tests/bootstrap.php',
        ]))
        ->withExtension(new Extension(IbexaExtension::class, [
            'mink' => [
                'default_javascript_session' => '%env(string:MINK_DEFAULT_SESSION)%',
                'width' => 1440,
                'height' => 1080,
            ],
        ]))
        ->withExtension(new Extension(ListFeaturesExtension::class)))
    ->withProfile((new Profile('regression'))
        ->withSuite((new Suite('setup-oss'))
            ->withContexts(
                ContentContext::class,
                ApiContentTypeContext::class,
                RoleContext::class,
                TestContext::class,
                UserContext::class,
                ConfigurationContext::class,
                FileContext::class,
                ApiLanguageContext::class
            )
            ->withPaths(
                '%paths.base%/vendor/ibexa/behat/features/personas',
                '%paths.base%/vendor/ibexa/behat/features/setup/richtextConfiguration/custom_styles.feature',
                '%paths.base%/vendor/ibexa/behat/features/setup/richtextConfiguration/custom_tags.feature',
                '%paths.base%/vendor/ibexa/behat/features/setup/loginMethods',
                '%paths.base%/vendor/ibexa/behat/features/setup/contentTranslation'
            ))
        ->withSuite((new Suite('oss'))
            ->withContexts(
                MinkContext::class,
                ContentContext::class,
                ApiContentTypeContext::class,
                RoleContext::class,
                TestContext::class,
                ApiTrashContext::class,
                UserContext::class,
                AdminUpdateContext::class,
                BookmarkContext::class,
                ContentActionsMenuContext::class,
                ContentPreviewContext::class,
                ContentTreeContext::class,
                ContentTypeContext::class,
                ContentUpdateContext::class,
                ContentViewContext::class,
                DashboardContext::class,
                LanguageContext::class,
                NavigationContext::class,
                NotificationContext::class,
                ObjectStatesContext::class,
                RolesContext::class,
                SearchContext::class,
                SectionsContext::class,
                SystemInfoContext::class,
                TrashContext::class,
                UDWContext::class,
                UserPreferencesContext::class,
                AuthenticationContext::class,
                DebuggingContext::class,
                UserSettingsContext::class,
                UserSetupContext::class,
                MyDraftsContext::class
            )
            ->withPaths(
                '%paths.base%/vendor/ibexa/admin-ui/features/personas',
                '%paths.base%/vendor/ibexa/admin-ui/features/standard',
                '%paths.base%/vendor/ibexa/user/features/browser'
            )
            ->withFilter(new TagFilter('~@broken&&@IbexaOSS'))));
