<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Component;

use Behat\Mink\Session;
use Facebook\WebDriver\Chrome\ChromeDevToolsDriver;
use Ibexa\Behat\Browser\Element\ElementCollectionInterface;
use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Element\Factory\Debug\Highlighting\ElementFactory as HighlightingDebugElementFactory;
use Ibexa\Behat\Browser\Element\Factory\Debug\Interactive\ElementFactory as InteractiveDebugElementFactory;
use Ibexa\Behat\Browser\Element\Factory\ElementFactory;
use Ibexa\Behat\Browser\Element\Factory\ElementFactoryInterface;
use Ibexa\Behat\Browser\Element\RootElementInterface;
use Ibexa\Behat\Browser\Locator\LocatorCollection;
use Ibexa\Behat\Browser\Locator\LocatorInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use OAndreyev\Mink\Driver\WebDriver;
use RuntimeException;

abstract class Component implements ComponentInterface
{
    /** @var \Ibexa\Behat\Browser\Locator\LocatorCollection */
    protected $locators;

    /** @var \Behat\Mink\Session */
    private $session;

    /** @var \Ibexa\Behat\Browser\Element\Factory\ElementFactoryInterface */
    private $elementFactory;

    public function __construct(Session $session)
    {
        $this->session = $session;
        $this->locators = new LocatorCollection($this->specifyLocators());
        $this->elementFactory = new ElementFactory();
    }

    abstract public function verifyIsLoaded(): void;

    final protected function getHTMLPage(): RootElementInterface
    {
        return $this->elementFactory->createRootElement($this->getSession(), $this->elementFactory);
    }

    public function find(LocatorInterface $locator): ElementInterface
    {
        return $this->getHTMLPage()->find($locator);
    }

    /** @return ElementCollectionInterface<ElementInterface> */
    public function findAll(LocatorInterface $locator): ElementCollectionInterface
    {
        return $this->getHTMLPage()->findAll($locator);
    }

    public function setElementFactory(ElementFactoryInterface $elementFactory): void
    {
        $this->elementFactory = $elementFactory;
    }

    protected function getSession(): Session
    {
        return $this->session;
    }

    protected function getDevToolsDriver(): ChromeDevToolsDriver
    {
        $driver = $this->session->getDriver();

        if (!($driver instanceof WebDriver)) {
            throw new NotImplementedException('Chrome DevTools driver is not available for this driver');
        }

        $webDriver = $driver->getWebDriver();

        if (null === $webDriver) {
            throw new RuntimeException('Error happened when accessing the WebDriver');
        }

        return new ChromeDevToolsDriver($webDriver);
    }

    /**
     * @return \Ibexa\Behat\Browser\Locator\LocatorInterface[]
     */
    abstract protected function specifyLocators(): array;

    final protected function getLocator(string $identifier): LocatorInterface
    {
        return $this->locators->get($identifier);
    }

    public function enableDebugging(bool $interactive = true, bool $highlighting = true): ElementFactoryInterface
    {
        $oldFactory = $this->elementFactory;

        $factory = new ElementFactory();

        if ($highlighting) {
            $factory = new HighlightingDebugElementFactory($this->session, $factory);
        }

        if ($interactive) {
            $factory = new InteractiveDebugElementFactory($factory);
        }
        $this->setElementFactory($factory);

        return $oldFactory;
    }

    public function disableDebugging(): ElementFactoryInterface
    {
        $oldFactory = $this->elementFactory;
        $this->setElementFactory(new ElementFactory());

        return $oldFactory;
    }
}
