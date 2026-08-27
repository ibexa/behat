<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Behat\Mink\Driver;

use Behat\MinkExtension\ServiceContainer\Driver\DriverFactory;
use Ibexa\Behat\Browser\Driver\WebdriverClassicDriver;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Mirrors the `webdriver_classic` factory shipped with friends-of-behat/mink-extension 3.x
 * (same driver name and config shape), but builds the Ibexa driver subclass so that the
 * underlying RemoteWebDriver stays reachable. Registered from IbexaExtension::initialize(),
 * which runs after MinkExtension registers its own factories, so this one takes precedence.
 */
final class WebdriverClassicFactory implements DriverFactory
{
    public const DRIVER_NAME = 'webdriver_classic';

    public function getDriverName(): string
    {
        return self::DRIVER_NAME;
    }

    public function supportsJavascript(): bool
    {
        return true;
    }

    public function configure(ArrayNodeDefinition $builder): void
    {
        $builder
            ->children()
                ->scalarNode('browser')->defaultValue('%mink.browser_name%')->end()
                ->scalarNode('wd_host')->defaultValue('http://localhost:4444/wd/hub')->end()
                ->arrayNode('capabilities')
                    ->normalizeKeys(false)
                    ->useAttributeAsKey('name')
                    ->prototype('variable')->end()
                ->end()
            ->end();
    }

    /**
     * @param array<string, mixed> $config
     */
    public function buildDriver(array $config): Definition
    {
        return new Definition(WebdriverClassicDriver::class, [
            $config['browser'],
            is_array($config['capabilities'] ?? null) ? $config['capabilities'] : [],
            $config['wd_host'],
        ]);
    }
}
