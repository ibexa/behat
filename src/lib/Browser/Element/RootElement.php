<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element;

use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Session;
use Ibexa\Behat\Browser\Element\Factory\ElementFactoryInterface;
use RuntimeException;

final class RootElement extends BaseElement implements RootElementInterface
{
    private Session $session;

    private DocumentElement $decoratedElement;

    public function __construct(ElementFactoryInterface $elementFactory, Session $session, DocumentElement $baseElement)
    {
        parent::__construct($elementFactory);
        $this->session = $session;
        $this->decoratedElement = $baseElement;
    }

    public function dragAndDrop(string $from, string $hover, string $to): void
    {
        if (!$this->isDraggingLibraryLoaded()) {
            throw new RuntimeException('drag-mock library has to be added to the page in order to use this method. Refer to README in BehatBundle for more information.');
        }

        $movingScript = sprintf('{ const dragMockTmp = dragMock.dragStart(%s).delay(50).dragOver(%s).delay(50); setTimeout(() => dragMockTmp.drop(%s), 100); }', $from, $hover, $to);
        $this->session->getDriver()->executeScript($movingScript);
    }

    private function isDraggingLibraryLoaded(): bool
    {
        return $this->session->getDriver()->evaluateScript("typeof(dragMock) !== 'undefined'");
    }

    public function executeJavaScript(string $script): string
    {
        return $this->session->evaluateScript($script) ?? '';
    }

    protected function getDecoratedElement(): DocumentElement
    {
        return $this->decoratedElement;
    }
}
