<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element\Debug\Interactive;

use Exception;
use Ibexa\Behat\Browser\Element\RootElementInterface;
use Ibexa\Behat\Core\Debug\InteractiveDebuggerTrait;

final class RootElement extends BaseElement implements RootElementInterface
{
    use InteractiveDebuggerTrait;

    public function __construct(RootElementInterface $element)
    {
        parent::__construct($element);
    }

    public function dragAndDrop(string $from, string $hover, string $to): void
    {
        $this->element->dragAndDrop($from, $hover, $to);
    }

    public function executeJavaScript(string $script): string
    {
        try {
            return $this->element->executeJavaScript($script);
        } catch (Exception $exception) {
            $exceptionWithScript = new Exception(sprintf('Script: %s, Error: %s', $script, $exception->getMessage()));

            return $this->startInteractiveSessionOnException($exceptionWithScript, true);
        }
    }
}
