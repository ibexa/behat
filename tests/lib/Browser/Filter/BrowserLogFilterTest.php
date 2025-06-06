<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Behat\Browser\Filter;

use Ibexa\Behat\Browser\Filter\BrowserLogFilter;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class BrowserLogFilterTest extends TestCase
{
    public function testFiltersOutExcludedPatterns()
    {
        $initialLogEntries = [
            'http://web/api/ibexa/v2/bookmark/43 - Failed to load resource: the server responded with a status of 404 (Not Found)',
            'https://varnish/api/ibexa/v2/bookmark/1 - Failed to load resource: the server responded with a status of 404 (Not Found)',
            'Real JS Error',
            'http://varnish/bundles/netgentags/admin/jstree/js/jstree.min.js 5 document.registerElement is deprecated and will be removed in M73, around March 2019. Please use window.customElements.define instead. See https://www.chromestatus.com/features/4642138092470272 for more details.',
            'https://web/bundles/netgentags/admin/jstree/js/jstree.min.js 5 document.registerElement is deprecated and will be removed in M73, around March 2019. Please use window.customElements.define instead. See https://www.chromestatus.com/features/4642138092470272 for more details.',
            'Another real JS error',
            'http://web.prod/admin/version-draft/has-no-conflict/124/eng-GB - Failed to load resource: the server responded with a status of 409 (Conflict)',
            'http://varnish.prod/admin/version-draft/has-no-conflict/1/pol-PL - Failed to load resource: the server responded with a status of 409 (Conflict)',
            'webpack:///./vendor/ezsystems/ezplatform-admin-ui/src/bundle/Resources/public/js/scripts/fieldType/ibexa_object_relation_list.js? 91:12 "EzObjectRelation fieldtype is deprecated. Please, use EzObjectRelationList fieldtype instead."',
        ];

        $filter = new BrowserLogFilter();
        $actualResult = $filter->filter($initialLogEntries);

        Assert::assertEquals(['Real JS Error', 'Another real JS error'], $actualResult);
    }
}
