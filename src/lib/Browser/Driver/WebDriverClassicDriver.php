<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Driver;

use Behat\Mink\Exception\DriverException;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Remote\LocalFileDetector;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use Mink\WebdriverClassicDriver\WebdriverClassicDriver as BaseWebdriverClassicDriver;

/**
 * Ibexa flavour of the Mink webdriver-classic driver.
 *
 * Exposes the underlying php-webdriver instance (needed for Chrome DevTools and browser console logs)
 * and keeps the behaviour the test suites were written against with oleg-andreyev/mink-phpwebdriver:
 * - getText() returns the WebDriver visible text (upstream uses innerText, which separates table cells with tabs),
 * - text inputs are filled by typing over the existing value and dispatching a "change" event instead of
 *   WebDriver clear() + sendKeys(), which React-driven form controls in the Back Office do not react to,
 * - files are uploaded through LocalFileDetector so they reach a remote Selenium node.
 */
final class WebDriverClassicDriver extends BaseWebdriverClassicDriver
{
    private const TEXT_INPUT_TYPES = ['', 'text', 'password', 'email', 'search', 'tel', 'url', 'number'];

    public function getRemoteWebDriver(): RemoteWebDriver
    {
        return $this->getWebDriver();
    }

    public function getText(string $xpath): string
    {
        $text = $this->findRemoteElement($xpath)->getText();

        return str_replace(["\r\n", "\r", "\n"], ' ', $text);
    }

    /**
     * @param string|bool|list<string> $value
     */
    public function setValue(
        string $xpath,
        $value
    ): void {
        $element = $this->findRemoteElement($xpath);
        $tagName = strtolower($element->getTagName() ?? '');
        $inputType = $tagName === 'input' ? strtolower((string)$element->getAttribute('type')) : null;

        if (is_string($value) && $inputType === 'file') {
            $this->attachFile($xpath, $value);

            return;
        }

        if (is_string($value) && ($tagName === 'textarea' || ($tagName === 'input' && in_array($inputType, self::TEXT_INPUT_TYPES, true)))) {
            $this->typeValue($element, $value);

            return;
        }

        parent::setValue($xpath, $value);
    }

    public function attachFile(
        string $xpath,
        string $path
    ): void {
        $element = $this->findRemoteElement($xpath);
        if (strtolower($element->getTagName() ?? '') !== 'input' || strtolower((string)$element->getAttribute('type')) !== 'file') {
            throw new DriverException(sprintf('Impossible to attach a file on element with XPath "%s" as it is not a file input', $xpath));
        }

        try {
            $element->setFileDetector(new LocalFileDetector());
            $element->sendKeys($path);
        } catch (\Throwable $e) {
            throw new DriverException(sprintf('Cannot attach file "%s": %s', $path, $e->getMessage()), 0, $e);
        }
    }

    private function typeValue(
        RemoteWebElement $element,
        string $value
    ): void {
        try {
            $existingValueLength = strlen((string)$element->getAttribute('value'));
            // Browsers only fire "change" after leaving the field, so mimic a user replacing the value and then trigger it explicitly.
            $element->sendKeys(str_repeat(WebDriverKeys::BACKSPACE . WebDriverKeys::DELETE, $existingValueLength) . $value);
            $this->getWebDriver()->executeScript(
                'arguments[0].dispatchEvent(new Event("change", {bubbles: true, cancelable: false}));',
                [$element]
            );
        } catch (\Throwable $e) {
            throw new DriverException(sprintf('Cannot set text value: %s', $e->getMessage()), 0, $e);
        }
    }

    private function findRemoteElement(string $xpath): RemoteWebElement
    {
        try {
            return $this->getWebDriver()->findElement(WebDriverBy::xpath($xpath));
        } catch (NoSuchElementException $e) {
            throw new DriverException(sprintf('Element with XPath "%s" not found', $xpath), 0, $e);
        }
    }
}
