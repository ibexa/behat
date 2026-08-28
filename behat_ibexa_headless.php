<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;
use Behat\MinkExtension\Context\MinkContext;
use Ibexa\AdminUi\Behat\BrowserContext\AdminUpdateContext;
use Ibexa\AdminUi\Behat\BrowserContext\BookmarkContext;
use Ibexa\AdminUi\Behat\BrowserContext\ContentActionsMenuContext;
use Ibexa\AdminUi\Behat\BrowserContext\ContentPreviewContext;
use Ibexa\AdminUi\Behat\BrowserContext\ContentTreeContext;
use Ibexa\AdminUi\Behat\BrowserContext\ContentUpdateContext;
use Ibexa\AdminUi\Behat\BrowserContext\ContentViewContext;
use Ibexa\AdminUi\Behat\BrowserContext\DashboardContext;
use Ibexa\AdminUi\Behat\BrowserContext\MyDraftsContext;
use Ibexa\AdminUi\Behat\BrowserContext\NavigationContext;
use Ibexa\AdminUi\Behat\BrowserContext\NotificationContext;
use Ibexa\AdminUi\Behat\BrowserContext\ObjectStatesContext;
use Ibexa\AdminUi\Behat\BrowserContext\RolesContext;
use Ibexa\AdminUi\Behat\BrowserContext\SearchContext;
use Ibexa\AdminUi\Behat\BrowserContext\SectionsContext;
use Ibexa\AdminUi\Behat\BrowserContext\SystemInfoContext;
use Ibexa\AdminUi\Behat\BrowserContext\UDWContext;
use Ibexa\AdminUi\Behat\BrowserContext\UserNotificationContext;
use Ibexa\AdminUi\Behat\BrowserContext\UserPreferencesContext;
use Ibexa\AdminUi\Behat\BrowserContext\UserProfileContext;
use Ibexa\Behat\API\Context\ContentContext;
use Ibexa\Behat\API\Context\ContentTypeContext;
use Ibexa\Behat\API\Context\LanguageContext;
use Ibexa\Behat\API\Context\RoleContext;
use Ibexa\Behat\API\Context\TestContext;
use Ibexa\Behat\API\Context\TrashContext;
use Ibexa\Behat\API\Context\UserContext;
use Ibexa\Behat\Browser\Context\AuthenticationContext;
use Ibexa\Behat\Browser\Context\DebuggingContext;
use Ibexa\Behat\Core\Context\ConfigurationContext;
use Ibexa\Behat\Core\Context\FileContext;
use Ibexa\ConnectorAi\Behat\Context\AIActionsContext;
use Ibexa\ConnectorOpenAi\Behat\Context\AIAssistantContext;
use Ibexa\ConnectorOpenAi\Behat\Context\OpenAIContext;
use Ibexa\IntegratedHelp\Behat\BrowserContext\IntegratedHelpContext;
use Ibexa\Migration\Behat\Context\MigrationContext;
use Ibexa\ProductCatalog\Behat\Context\AttributeGroupsContext;
use Ibexa\ProductCatalog\Behat\Context\AttributesContext;
use Ibexa\ProductCatalog\Behat\Context\CatalogsContext;
use Ibexa\ProductCatalog\Behat\Context\CurrenciesContext;
use Ibexa\ProductCatalog\Behat\Context\CustomerGroupsContext;
use Ibexa\ProductCatalog\Behat\Context\ProductsContext;
use Ibexa\ProductCatalog\Behat\Context\ProductTypesContext;
use Ibexa\ProductCatalogDateTimeAttribute\Behat\Context\DateTimeAttributeContext;
use Ibexa\Scheduler\Behat\BrowserContext\DateBasedPublisherContext;
use Ibexa\Taxonomy\Behat\Context\Browser\TaxonomyContext;
use Ibexa\User\Behat\Context\UserSettingsContext;
use Ibexa\User\Behat\Context\UserSetupContext;
use Ibexa\VersionComparison\Behat\Context\VersionComparisonContext;
use Ibexa\Workflow\Behat\Context\WorkflowAdminContext;
use Ibexa\Workflow\Behat\Context\WorkflowContext;

return (new Config())
    ->import('behat_ibexa_oss.php')
    ->import('vendor/ibexa/product-catalog/behat_suites.php')
    ->import('vendor/ibexa/scheduler/behat_suites_headless.php')
    ->import('vendor/ibexa/taxonomy/behat_suites_headless.php')
    ->import('vendor/ibexa/version-comparison/behat_suites.php')
    ->import('vendor/ibexa/workflow/behat_suites_headless.php')
    ->withProfile((new Profile('regression'))
        ->withSuite((new Suite('setup-headless'))
            ->withContexts(
                ContentContext::class,
                ContentTypeContext::class,
                RoleContext::class,
                TestContext::class,
                UserContext::class,
                ConfigurationContext::class,
                FileContext::class,
                LanguageContext::class,
                MigrationContext::class
            )
            ->withPaths(
                '%paths.base%/vendor/ibexa/behat/features/personas',
                '%paths.base%/vendor/ibexa/behat/features/setup/richtextConfiguration/custom_styles.feature',
                '%paths.base%/vendor/ibexa/behat/features/setup/richtextConfiguration/custom_tags.feature',
                '%paths.base%/vendor/ibexa/workflow/features/setup/setup_headless.feature',
                '%paths.base%/vendor/ibexa/behat/features/setup/loginMethods',
                '%paths.base%/vendor/ibexa/behat/features/setup/contentTranslation',
                '%paths.base%/vendor/ibexa/product-catalog/features/setup/setup.feature',
                '%paths.base%/vendor/ibexa/connector-openai/features/setup/setup.feature',
                '%paths.base%/vendor/ibexa/product-catalog-date-time-attribute/features/setup/setup.feature'
            ))
        ->withSuite((new Suite('headless'))
            ->withContexts(
                MinkContext::class,
                AdminUpdateContext::class,
                BookmarkContext::class,
                ContentActionsMenuContext::class,
                ContentPreviewContext::class,
                ContentTreeContext::class,
                'Ibexa\AdminUi\Behat\BrowserContext\ContentTypeContext',
                ContentUpdateContext::class,
                ContentViewContext::class,
                DashboardContext::class,
                'Ibexa\AdminUi\Behat\BrowserContext\LanguageContext',
                MyDraftsContext::class,
                NavigationContext::class,
                NotificationContext::class,
                ObjectStatesContext::class,
                RolesContext::class,
                SearchContext::class,
                SectionsContext::class,
                SystemInfoContext::class,
                'Ibexa\AdminUi\Behat\BrowserContext\TrashContext',
                UDWContext::class,
                UserNotificationContext::class,
                UserPreferencesContext::class,
                UserProfileContext::class,
                ContentContext::class,
                ContentTypeContext::class,
                RoleContext::class,
                TestContext::class,
                TrashContext::class,
                UserContext::class,
                AuthenticationContext::class,
                DebuggingContext::class,
                MigrationContext::class,
                AttributeGroupsContext::class,
                AttributesContext::class,
                CatalogsContext::class,
                CurrenciesContext::class,
                CustomerGroupsContext::class,
                ProductsContext::class,
                ProductTypesContext::class,
                DateBasedPublisherContext::class,
                TaxonomyContext::class,
                'Ibexa\Taxonomy\Behat\Context\Service\TaxonomyContext',
                UserSettingsContext::class,
                UserSetupContext::class,
                VersionComparisonContext::class,
                WorkflowAdminContext::class,
                WorkflowContext::class,
                OpenAIContext::class,
                AIActionsContext::class,
                IntegratedHelpContext::class,
                AIAssistantContext::class,
                DateTimeAttributeContext::class
            )
            ->withPaths(
                '%paths.base%/vendor/ibexa/admin-ui/features/personas',
                '%paths.base%/vendor/ibexa/admin-ui/features/standard',
                '%paths.base%/vendor/ibexa/product-catalog/features/browser',
                '%paths.base%/vendor/ibexa/scheduler/features',
                '%paths.base%/vendor/ibexa/taxonomy/features',
                '%paths.base%/vendor/ibexa/user/features/browser',
                '%paths.base%/vendor/ibexa/version-comparison/features',
                '%paths.base%/vendor/ibexa/workflow/features/browser',
                '%paths.base%/vendor/ibexa/connector-ai/features/browser',
                '%paths.base%/vendor/ibexa/connector-openai/features/browser',
                '%paths.base%/vendor/ibexa/integrated-help/features/browser',
                '%paths.base%/vendor/ibexa/product-catalog-date-time-attribute/features/browser'
            )
            ->withFilter(new TagFilter('~@broken&&@IbexaHeadless'))));
