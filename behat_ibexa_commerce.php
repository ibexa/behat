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
use Ibexa\ActivityLog\Behat\Context\ActivityLogContext;
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
use Ibexa\CorporateAccount\Behat\Context\CompaniesContext;
use Ibexa\Discounts\Behat\Context\DiscountsContext;
use Ibexa\Discounts\Behat\Context\DiscountsInStorefrontContext;
use Ibexa\DiscountsCodes\Behat\Context\DiscountsCodesContext;
use Ibexa\DiscountsCodes\Behat\Context\DiscountsCodesInStorefrontContext;
use Ibexa\FieldTypePage\Behat\Context\BlockHideEventsSourceContext;
use Ibexa\FieldTypePage\Behat\Context\BlockRevealEventsSourceContext;
use Ibexa\FormBuilder\Behat\BrowserContext\FormAdministrationContext;
use Ibexa\FormBuilder\Behat\BrowserContext\FormBuilderContext;
use Ibexa\FormBuilder\Behat\BrowserContext\FormFieldConfigurationContext;
use Ibexa\FormBuilder\Behat\BrowserContext\FormFrontContext;
use Ibexa\IntegratedHelp\Behat\BrowserContext\IntegratedHelpContext;
use Ibexa\Migration\Behat\Context\MigrationContext;
use Ibexa\PageBuilder\Behat\Context\PageBuilderContext;
use Ibexa\Payment\Behat\Context\PaymentMethodsContext;
use Ibexa\ProductCatalog\Behat\Context\AttributeGroupsContext;
use Ibexa\ProductCatalog\Behat\Context\AttributesContext;
use Ibexa\ProductCatalog\Behat\Context\CatalogsContext;
use Ibexa\ProductCatalog\Behat\Context\CurrenciesContext;
use Ibexa\ProductCatalog\Behat\Context\CustomerGroupsContext;
use Ibexa\ProductCatalog\Behat\Context\ProductsContext;
use Ibexa\ProductCatalog\Behat\Context\ProductTypesContext;
use Ibexa\ProductCatalogDateTimeAttribute\Behat\Context\DateTimeAttributeContext;
use Ibexa\Scheduler\Behat\BrowserContext\DateBasedPublisherContext;
use Ibexa\Segmentation\Behat\Context\SegmentationContext;
use Ibexa\Shipping\Behat\Context\ShippingMethodsContext;
use Ibexa\SiteFactory\Behat\Context\SiteFactoryFrontendContext;
use Ibexa\Storefront\Behat\Context\StorefrontContext;
use Ibexa\Taxonomy\Behat\Context\Browser\TaxonomyContext;
use Ibexa\User\Behat\Context\UserSettingsContext;
use Ibexa\User\Behat\Context\UserSetupContext;
use Ibexa\VersionComparison\Behat\Context\VersionComparisonContext;
use Ibexa\Workflow\Behat\Context\WorkflowAdminContext;
use Ibexa\Workflow\Behat\Context\WorkflowContext;

return (new Config())
    ->import('behat_ibexa_experience.php')
    ->import('vendor/ibexa/payment/behat_suites.php')
    ->import('vendor/ibexa/shipping/behat_suites.php')
    ->import('vendor/ibexa/storefront/behat_suites.php')
    ->withProfile((new Profile('regression'))
        ->withSuite((new Suite('setup-commerce'))
            ->withContexts(
                ContentContext::class,
                ContentTypeContext::class,
                LanguageContext::class,
                RoleContext::class,
                TestContext::class,
                UserContext::class,
                ConfigurationContext::class,
                FileContext::class,
                MigrationContext::class,
                AuthenticationContext::class,
                CatalogsContext::class,
                ContentActionsMenuContext::class,
                ContentUpdateContext::class,
                AdminUpdateContext::class,
                NotificationContext::class,
                MinkContext::class,
                WorkflowContext::class,
                UserNotificationContext::class,
                ContentViewContext::class,
                NavigationContext::class
            )
            ->withPaths(
                '%paths.base%/vendor/ibexa/behat/features/personas',
                '%paths.base%/vendor/ibexa/behat/features/setup/richtextConfiguration/custom_styles.feature',
                '%paths.base%/vendor/ibexa/behat/features/setup/richtextConfiguration/custom_tags.feature',
                '%paths.base%/vendor/ibexa/workflow/features/setup/setup_experience.feature',
                '%paths.base%/vendor/ibexa/behat/features/setup/loginMethods',
                '%paths.base%/vendor/ibexa/behat/features/setup/contentTranslation',
                '%paths.base%/vendor/ibexa/page-builder/features/setup/setup.feature',
                '%paths.base%/vendor/ibexa/product-catalog/features/setup/setup.feature',
                '%paths.base%/vendor/ibexa/corporate-account/features/setup/setup.feature',
                '%paths.base%/vendor/ibexa/shipping/features/setup/setup.feature',
                '%paths.base%/vendor/ibexa/storefront/features/setup/setup.feature',
                '%paths.base%/vendor/ibexa/dashboard/features/setup/setup.feature',
                '%paths.base%/vendor/ibexa/connector-openai/features/setup/setup.feature',
                '%paths.base%/vendor/ibexa/discounts/features/setup/setup.feature',
                '%paths.base%/vendor/ibexa/discounts-codes/features/setup/setup.feature',
                '%paths.base%/vendor/ibexa/activity-log/features/setup/setup.feature',
                '%paths.base%/vendor/ibexa/product-catalog-date-time-attribute/features/setup/setup.feature',
                '%paths.base%/vendor/ibexa/site-factory/features/setup.feature'
            ))
        ->withSuite((new Suite('commerce'))
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
                CompaniesContext::class,
                BlockHideEventsSourceContext::class,
                BlockRevealEventsSourceContext::class,
                FormAdministrationContext::class,
                FormBuilderContext::class,
                FormFieldConfigurationContext::class,
                FormFrontContext::class,
                MigrationContext::class,
                PageBuilderContext::class,
                PaymentMethodsContext::class,
                AttributeGroupsContext::class,
                AttributesContext::class,
                CatalogsContext::class,
                CurrenciesContext::class,
                CustomerGroupsContext::class,
                ProductsContext::class,
                ProductTypesContext::class,
                DateBasedPublisherContext::class,
                SegmentationContext::class,
                StorefrontContext::class,
                TaxonomyContext::class,
                'Ibexa\Taxonomy\Behat\Context\Service\TaxonomyContext',
                UserSettingsContext::class,
                UserSetupContext::class,
                VersionComparisonContext::class,
                WorkflowAdminContext::class,
                WorkflowContext::class,
                ShippingMethodsContext::class,
                OpenAIContext::class,
                AIActionsContext::class,
                AIAssistantContext::class,
                DiscountsContext::class,
                DiscountsCodesContext::class,
                DiscountsInStorefrontContext::class,
                DiscountsCodesInStorefrontContext::class,
                ActivityLogContext::class,
                IntegratedHelpContext::class,
                DateTimeAttributeContext::class,
                SiteFactoryFrontendContext::class
            )
            ->withPaths(
                '%paths.base%/vendor/ibexa/admin-ui/features/personas',
                '%paths.base%/vendor/ibexa/admin-ui/features/standard',
                '%paths.base%/vendor/ibexa/corporate-account/features/browser',
                '%paths.base%/vendor/ibexa/fieldtype-page/features/eventSource',
                '%paths.base%/vendor/ibexa/form-builder/features',
                '%paths.base%/vendor/ibexa/page-builder/features/DynamicLandingPage',
                '%paths.base%/vendor/ibexa/page-builder/features/personas',
                '%paths.base%/vendor/ibexa/payment/features',
                '%paths.base%/vendor/ibexa/product-catalog/features/browser',
                '%paths.base%/vendor/ibexa/scheduler/features',
                '%paths.base%/vendor/ibexa/segmentation/features',
                '%paths.base%/vendor/ibexa/storefront/features/browser',
                '%paths.base%/vendor/ibexa/taxonomy/features',
                '%paths.base%/vendor/ibexa/user/features/browser',
                '%paths.base%/vendor/ibexa/version-comparison/features',
                '%paths.base%/vendor/ibexa/workflow/features/browser',
                '%paths.base%/vendor/ibexa/shipping/features/browser',
                '%paths.base%/vendor/ibexa/connector-ai/features/browser',
                '%paths.base%/vendor/ibexa/connector-openai/features/browser',
                '%paths.base%/vendor/ibexa/discounts/features/browser',
                '%paths.base%/vendor/ibexa/discounts-codes/features/browser',
                '%paths.base%/vendor/ibexa/activity-log/features/browser',
                '%paths.base%/vendor/ibexa/integrated-help/features/browser',
                '%paths.base%/vendor/ibexa/product-catalog-date-time-attribute/features/browser',
                '%paths.base%/vendor/ibexa/site-factory/features/SiteFactoryFrontend.feature'
            )
            ->withFilter(new TagFilter('~@broken&&@IbexaCommerce'))));
