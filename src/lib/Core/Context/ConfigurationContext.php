<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Core\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Exception;
use Ibexa\Behat\Core\Configuration\ConfigurationEditorInterface;
use Symfony\Component\Yaml\Yaml;

class ConfigurationContext implements Context
{
    private const SITEACCESS_KEY_FORMAT = 'ibexa.system.%s.%s';
    private const SITEACCESS_MATCHER_KEY = 'ibexa.siteaccess.match';

    private $mainProjectConfigFilePath;

    private $projectDir;

    /**
     * @var \Ibexa\Behat\Core\Configuration\ConfigurationEditorInterface
     */
    private $configurationEditor;

    public function __construct(string $projectDir, ConfigurationEditorInterface $configurationEditor)
    {
        $this->projectDir = $projectDir;
        $this->mainProjectConfigFilePath = sprintf('%s/config/packages/ibexa.yaml', $projectDir);
        $this->configurationEditor = $configurationEditor;
    }

    /**
     * @Given I add a siteaccess :siteaccessName to :siteaccessGroup with settings
     *
     * @param mixed $siteaccessName
     * @param mixed $siteaccessGroup
     */
    public function iAddSiteaccessWithSettings($siteaccessName, $siteaccessGroup, TableNode $settings)
    {
        $config = $this->configurationEditor->getConfigFromFile($this->mainProjectConfigFilePath);

        $config = $this->configurationEditor->append($config, 'ibexa.siteaccess.list', $siteaccessName);
        $config = $this->configurationEditor->append($config, sprintf('ibexa.siteaccess.groups.%s', $siteaccessGroup), $siteaccessName);

        foreach ($settings->getHash() as $setting) {
            $key = $setting['key'];
            $value = $this->parseSetting($setting['value']);
            $config = $this->configurationEditor->set($config, sprintf(self::SITEACCESS_KEY_FORMAT, $siteaccessName, $key), $value);
        }

        $this->configurationEditor->saveConfigToFile($this->mainProjectConfigFilePath, $config);
    }

    /**
     * @Given I :mode configuration to :siteaccessName siteaccess
     * @Given I :mode configuration to :siteaccessName siteaccess in :configFilePath
     *
     * @param mixed $siteaccessName
     */
    public function iAppendOrSetConfigurationToSiteaccess(string $mode, $siteaccessName, TableNode $settings, string $configFilePath = null)
    {
        $appendToExisting = $this->shouldAppendValue($mode);

        $configFilePath = $configFilePath ? sprintf('%s/%s', $this->projectDir, $configFilePath) : $this->mainProjectConfigFilePath;

        $config = $this->configurationEditor->getConfigFromFile($configFilePath);

        foreach ($settings->getHash() as $setting) {
            $key = $setting['key'];
            $value = $this->parseSetting($setting['value']);

            $config = $appendToExisting ?
                $this->configurationEditor->append($config, sprintf(self::SITEACCESS_KEY_FORMAT, $siteaccessName, $key), $value) :
                $this->configurationEditor->set($config, sprintf(self::SITEACCESS_KEY_FORMAT, $siteaccessName, $key), $value);
        }

        $this->configurationEditor->saveConfigToFile($configFilePath, $config);
    }

    /**
     * @Given I :mode configuration to :parentNode
     * @Given I :mode configuration to :parentNode in :configFilePath
     *
     * string $mode Available: append|set - whether the new config will be appended (resulting in an array) or replace the current value if it exists
     *
     * @param mixed $parentNode
     */
    public function iModifyConfigurationUnderKey(string $mode, $parentNode, PyStringNode $configFragment, string $configFilePath = null)
    {
        $appendToExisting = $this->shouldAppendValue($mode);

        $configFilePath = $configFilePath ? sprintf('%s/%s', $this->projectDir, $configFilePath) : $this->mainProjectConfigFilePath;

        $config = $this->configurationEditor->getConfigFromFile($configFilePath);
        $parsedConfig = $this->parseConfig($configFragment);

        $config = $appendToExisting ?
            $this->configurationEditor->append($config, $parentNode, $parsedConfig) :
            $this->configurationEditor->set($config, $parentNode, $parsedConfig);
        $this->configurationEditor->saveConfigToFile($configFilePath, $config);
    }

    /**
     * @Given I :mode configuration to :siteaccessName siteaccess under :keyName key
     *
     * @param mixed $siteaccessName
     * @param mixed $keyName
     */
    public function iModifyConfigurationForSiteaccessUnderKey(string $mode, $siteaccessName, $keyName, PyStringNode $configFragment)
    {
        $parentNode = sprintf(self::SITEACCESS_KEY_FORMAT, $siteaccessName, $keyName);
        $this->iModifyConfigurationUnderKey($mode, $parentNode, $configFragment);
    }

    /**
     * @Given I :mode siteaccess matcher configuration
     */
    public function iModifySiteaccessMatcherConfiguration(string $mode, PyStringNode $configFragment)
    {
        $this->iModifyConfigurationUnderKey($mode, self::SITEACCESS_MATCHER_KEY, $configFragment);
    }

    private function parseSetting($setting)
    {
        return false !== strpos($setting, ',') ? explode(',', $setting) : $setting;
    }

    private function parseConfig(PyStringNode $configFragment)
    {
        $cleanedConfig = '';

        // Remove indent from first line and adjust the rest
        $firstLine = $configFragment->getStrings()[0];
        $firstLineIndent = \strlen($firstLine) - \strlen(ltrim($firstLine));

        foreach ($configFragment->getStrings() as $line) {
            $cleanedConfig = $cleanedConfig . substr($line, $firstLineIndent) . PHP_EOL;
        }

        return Yaml::parse($cleanedConfig);
    }

    private function shouldAppendValue(string $value): bool
    {
        if (!\in_array($value, ['set', 'append'])) {
            throw new Exception('Supported modes are: set, append');
        }

        return 'append' === $value;
    }

    /**
     * @Given I copy the configuration from :keyName to :newKeyName
     * @Given I copy the configuration from :keyName to :newKeyName in :configFilePath
     */
    public function iCopyTheConfigurationFromTo(string $keyName, string $newKeyName, string $configFilePath = null)
    {
        $configFilePath = $configFilePath ? sprintf('%s/%s', $this->projectDir, $configFilePath) : $this->mainProjectConfigFilePath;
        $config = $this->configurationEditor->getConfigFromFile($configFilePath);
        $config = $this->configurationEditor->copyKey($config, $keyName, $newKeyName);
        $this->configurationEditor->saveConfigToFile($configFilePath, $config);
    }
}
