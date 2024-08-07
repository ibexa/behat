<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Core\Configuration;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Yaml\Yaml;

class ConfigurationEditor implements ConfigurationEditorInterface
{
    /**
     * Appends given value under key, extending existing settings.
     *
     * @param $config YAML config
     * @param string       $key   'key' or 'nested.key'
     * @param array|string $value
     *
     * @return mixed YAML config
     */
    public function append($config, string $key, $value)
    {
        $this->modifyValue($config, $key, $value, true);

        return $config;
    }

    /**
     * Sets given value under key. Existing settings are overwritten.
     *
     * @param $config YAML config
     * @param string       $key   'key' or 'nested.key'
     * @param array|string $value
     *
     * @return mixed YAML config
     */
    public function set($config, string $key, $value)
    {
        $this->modifyValue($config, $key, $value, false);

        return $config;
    }

    public function get($config, string $key)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $key = $this->parseKey($key);

        return $propertyAccessor->getValue($config, $key);
    }

    /**
     * @return mixed YAML config
     */
    public function getConfigFromFile(string $filePath)
    {
        return Yaml::parse(file_get_contents($filePath));
    }

    /**
     * @param $filePath
     * @param $config YAML config
     */
    public function saveConfigToFile($filePath, $config): void
    {
        file_put_contents($filePath, Yaml::dump($config, 8, 5));
    }

    private function modifyValue(&$config, string $key, $value, bool $appendToExisting): void
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        $key = $this->parseKey($key);
        $currentValue = $propertyAccessor->getValue($config, $key);

        $newValue = $this->getNewValue($currentValue, $value, $appendToExisting);
        $propertyAccessor->setValue($config, $key, $newValue);
    }

    private function parseKey(string $key): string
    {
        $keys = explode('.', $key);
        $parsed = '';
        foreach ($keys as $keyPart) {
            $parsed .= sprintf('[%s]', $keyPart);
        }

        return $parsed;
    }

    private function getNewValue($currentValue, $value, bool $appendToExisting)
    {
        if (!$appendToExisting) {
            return $value;
        }

        if (null === $currentValue) {
            return \is_array($value) ? $value : [$value];
        }

        if (!\is_array($currentValue)) {
            $currentValue = [$currentValue];
        }

        if (!\is_array($value)) {
            $value = [$value];
        }

        return array_merge($currentValue, $value);
    }

    public function copyKey($config, string $keyName, string $newKeyName)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        $key = $this->parseKey($keyName);
        $newKey = $this->parseKey($newKeyName);
        $currentValue = $propertyAccessor->getValue($config, $key);
        $propertyAccessor->setValue($config, $newKey, $currentValue);

        return $config;
    }
}
