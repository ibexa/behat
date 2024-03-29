<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element\Factory;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use Ibexa\Behat\Browser\Element\Element;
use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Element\RootElement;
use Ibexa\Behat\Browser\Element\RootElementInterface;
use Ibexa\Behat\Browser\Locator\LocatorInterface;

final class ElementFactory implements ElementFactoryInterface
{
    public function createElement(ElementFactoryInterface $elementFactory, LocatorInterface $locator, NodeElement $nodeElement): ElementInterface
    {
        return new Element($elementFactory, $locator, $nodeElement);
    }

    public function createRootElement(Session $session, ElementFactoryInterface $elementFactory): RootElementInterface
    {
        return new RootElement($elementFactory, $session, $session->getPage());
    }
}
