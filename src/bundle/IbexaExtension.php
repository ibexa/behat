<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Behat;

use Behat\Behat\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\Exception\ServiceContainer\ExceptionExtension;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use FriendsOfBehat\SymfonyExtension\ServiceContainer\SymfonyExtension;
use Ibexa\Bundle\Behat\Extension\ExceptionStringer\PHPUnit10ExceptionStringer;
use Ibexa\Bundle\Behat\Initializer\BehatSiteAccessInitializer;
use Ibexa\Bundle\Behat\Subscriber\StartScenarioSubscriber;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class IbexaExtension implements Extension
{
    private const MINK_DEFAULT_JAVASCRIPT_SESSION_PARAMETER = 'ibexa.platform.behat.mink.default_javascript_session';

    private const WIDTH_PARAMETER = 'ibexa.behat.browser.width';

    private const HEIGHT_PARAMETER = 'ibexa.behat.browser.height';

    public function getConfigKey()
    {
        return 'ibexabehatextension';
    }

    public function process(ContainerBuilder $container)
    {
        if ($container->hasParameter(self::MINK_DEFAULT_JAVASCRIPT_SESSION_PARAMETER)) {
            $defaultJavascriptSession = $container->getParameter(self::MINK_DEFAULT_JAVASCRIPT_SESSION_PARAMETER);
            $this->setDefaultJavascriptSession($container, $defaultJavascriptSession);
        }
    }

    public function initialize(ExtensionManager $extensionManager)
    {
    }

    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->arrayNode('mink')
                    ->children()
                        ->scalarNode('default_javascript_session')->defaultNull()->end()
                        ->scalarNode('width')->defaultValue(1440)->end()
                        ->scalarNode('height')->defaultValue(1080)->end()
                    ->end()
                ->end()
            ->end();
    }

    public function load(ContainerBuilder $container, array $config)
    {
        $this->loadExceptionStringer($container);
        $this->loadSiteAccessInitializer($container);
        $this->loadStartScenarioSubscriber($container);
        $this->setMinkParameters($container, $config);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/Resources/config'));
        $loader->load('extension.yaml');
    }

    private function loadSiteAccessInitializer(ContainerBuilder $container): void
    {
        $definition = new Definition(BehatSiteAccessInitializer::class);
        $definition->setArguments([
            new Reference(SymfonyExtension::KERNEL_ID),
        ]);
        $definition->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, ['priority' => 0]);
        $container->setDefinition(BehatSiteAccessInitializer::class, $definition);
    }

    private function loadExceptionStringer(ContainerBuilder $container): void
    {
        $definition = new Definition(PHPUnit10ExceptionStringer::class);
        $definition->addTag(ExceptionExtension::STRINGER_TAG, ['priority' => 1000]);
        $container->setDefinition(PHPUnit10ExceptionStringer::class, $definition);
    }

    private function setMinkParameters(ContainerBuilder $container, array $config): void
    {
        if (!array_key_exists('mink', $config)) {
            return;
        }

        $keyParameterMap = [
            'default_javascript_session' => self::MINK_DEFAULT_JAVASCRIPT_SESSION_PARAMETER,
        ];

        foreach ($keyParameterMap as $key => $parameter) {
            $value = $container->resolveEnvPlaceholders($config['mink'][$key], true);
            if ($value) {
                $container->setParameter($parameter, $value);
            }
        }

        $container->setParameter(self::WIDTH_PARAMETER, (int) $config['mink']['width']);
        $container->setParameter(self::HEIGHT_PARAMETER, (int) $config['mink']['height']);
    }

    private function setDefaultJavascriptSession(ContainerBuilder $container, string $defaultJavascriptSession): void
    {
        $container->setParameter('mink.javascript_session', $defaultJavascriptSession);
    }

    private function loadStartScenarioSubscriber(ContainerBuilder $container)
    {
        $definition = new Definition(StartScenarioSubscriber::class);
        $definition->setArguments([
            new Reference(SymfonyExtension::KERNEL_ID),
            $this->getParameterReference(self::WIDTH_PARAMETER),
            $this->getParameterReference(self::HEIGHT_PARAMETER),
        ]);
        $definition->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, ['priority' => StartScenarioSubscriber::PRIORITY]);
        $container->setDefinition(StartScenarioSubscriber::class, $definition);
    }

    private function getParameterReference(string $name): string
    {
        return '%' . $name . '%';
    }
}

class_alias(IbexaExtension::class, 'EzSystems\BehatBundle\BehatExtension');
