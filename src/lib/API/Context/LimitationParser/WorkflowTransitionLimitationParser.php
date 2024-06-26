<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\API\Context\LimitationParser;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Workflow\Value\Limitation\WorkflowTransitionLimitation;

class WorkflowTransitionLimitationParser implements LimitationParserInterface
{
    public function supports(string $limitationType): bool
    {
        return \in_array(strtolower($limitationType), ['workflowtransition', 'workflow transition']);
    }

    public function parse(string $limitationValues): Limitation
    {
        $limitations = explode(',', $limitationValues);

        return new WorkflowTransitionLimitation(
            ['limitationValues' => $limitations]
        );
    }
}
