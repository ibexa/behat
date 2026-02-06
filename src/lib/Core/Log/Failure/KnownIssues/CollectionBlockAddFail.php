<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Core\Log\Failure\KnownIssues;

use Ibexa\Behat\Core\Log\Failure\TestFailureData;

class ContentTypeCreatedInTheBackground implements KnownIssueInterface
{
    public function matches(TestFailureData $testFailureData): bool
    {
        return $testFailureData->exceptionStackTraceContainsFragment('Ibexa\PageBuilder\Behat\Page\PageBuilderEditor->addBlock()') &&
           $testFailureData->exceptionMessageContainsFragment("CSS locator 'blockAttribute': '[data-ibexa-block-id]' was not found.") &&
           $testFailureData->browserLogsContainFragment("I start creating a new Landing Page \"Collection\"");
    }

    public function getJiraReference(): string
    {
        return 'https://issues.ibexa.co/browse/IBX-10631';
    }
}
