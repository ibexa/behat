<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Behat\DependencyInjection\Compiler;

use Ibexa\Behat\API\Context\LimitationParser\LimitationParsersCollector;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class LimitationParserPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $parserCollector = $container->findDefinition(LimitationParsersCollector::class);
        $strategyServiceIds = array_keys($container->findTaggedServiceIds('ibexa.behat.limitation_parser'));

        foreach ($strategyServiceIds as $strategyServiceId) {
            $parserCollector->addMethodCall(
                'addLimitationParser',
                [new Reference($strategyServiceId)]
            );
        }
    }
}
