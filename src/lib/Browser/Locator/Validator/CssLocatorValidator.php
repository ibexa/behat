<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Locator\Validator;

use Ibexa\Behat\Browser\Locator\CSSLocator;
use Symfony\Component\CssSelector\Exception\ParseException;
use Symfony\Component\CssSelector\Node\CombinedSelectorNode;
use Symfony\Component\CssSelector\Node\ElementNode;
use Symfony\Component\CssSelector\Node\FunctionNode;
use Symfony\Component\CssSelector\Node\PseudoNode;
use Symfony\Component\CssSelector\Node\SelectorNode;
use Symfony\Component\CssSelector\Parser\Parser;

class CssLocatorValidator
{
    private Parser $parser;

    private array $partiallySupportedPseudoClasses = ['first-of-type', 'last-of-type', 'nth-of-type', 'nth-last-of-type'];

    public function __construct()
    {
        $this->parser = new Parser();
    }

    public function validate(CSSLocator $cssLocator): void
    {
        if (!$this->isValidSelector($cssLocator->getSelector())) {
            throw new ParseException(
                sprintf(
                    "Locator '%s' with ID '%s' cannot be used because of limitations of the CssSelector component. See more: https://symfony.com/doc/current/components/css_selector.html#limitations-of-the-cssselector-component",
                    $cssLocator->getSelector(),
                    $cssLocator->getIdentifier()
                )
            );
        }
    }

    private function isValidSelector(string $selector): bool
    {
        $tokens = $this->parser->parse($selector);

        while (!empty($tokens)) {
            $token = array_pop($tokens);

            if ($token instanceof SelectorNode) {
                array_push($tokens, $token->getTree());
            }

            if ($token instanceof CombinedSelectorNode) {
                array_push($tokens, $token->getSelector(), $token->getSubSelector());
            }

            if ($token instanceof FunctionNode) {
                if (!$this->isValidFunctionNode($token)) {
                    return false;
                }
            }

            if ($token instanceof PseudoNode) {
                if (!$this->isValidPseudoNode($token)) {
                    return false;
                }
            }
        }

        return true;
    }

    private function isValidFunctionNode(FunctionNode $node): bool
    {
        if (!in_array($node->getName(), $this->partiallySupportedPseudoClasses)) {
            return true;
        }

        $selector = $node->getSelector();

        if (!($selector instanceof ElementNode)) {
            $selector = $selector->getSelector();
        }

        if ($selector->getElement() === null) {
            return false;
        }

        return true;
    }

    private function isValidPseudoNode(PseudoNode $node): bool
    {
        if (!in_array($node->getIdentifier(), $this->partiallySupportedPseudoClasses)) {
            return true;
        }

        $selector = $node->getSelector();

        if (!($selector instanceof ElementNode)) {
            $selector = $selector->getSelector();
        }

        if ($selector->getElement() === null) {
            return false;
        }

        return true;
    }
}
