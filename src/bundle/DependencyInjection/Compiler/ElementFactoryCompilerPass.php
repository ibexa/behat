<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Behat\DependencyInjection\Compiler;

use Ibexa\Behat\Browser\Element\Factory\Debug\Interactive\ElementFactory;
use Ibexa\Bundle\Behat\DependencyInjection\IbexaBehatExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ElementFactoryCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$this->shouldEnableInteractiveDebug($container)) {
            return;
        }

        $interactiveDebugElementFactory = $container->findDefinition(ElementFactory::class);

        $componentServiceIds = array_keys($container->findTaggedServiceIds('ibexa.behat.browser.component'));
        foreach ($componentServiceIds as $componentServiceId) {
            $compontentDefinition = $container->findDefinition($componentServiceId);
            $compontentDefinition->addMethodCall('setElementFactory', [$interactiveDebugElementFactory]);
        }
    }

    private function shouldEnableInteractiveDebug(ContainerBuilder $container): bool
    {
        return $container->hasParameter(IbexaBehatExtension::BROWSER_DEBUG_INTERACTIVE_ENABLED) &&
            $container->getParameter(IbexaBehatExtension::BROWSER_DEBUG_INTERACTIVE_ENABLED);
    }
}
