<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Behat\Core\Configuration;

use Ibexa\Behat\API\Facade\ContentFacade;
use Ibexa\Behat\Core\Configuration\ConfigurationEditor;
use Ibexa\Behat\Core\Configuration\LocationAwareConfigurationEditor;
use Ibexa\Core\Repository\Values\Content\Location;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class LocationAwareConfigurationEditorTest extends TestCase
{
    private $locationAwareConfigurationEditor;

    public function setUp(): void
    {
        $contentFacadeStub = $this->createStub(ContentFacade::class);
        $contentFacadeStub
            ->method('getLocationByLocationURL')
            ->willReturnMap(
                [
                    ['Home', new Location(['id' => 2])],
                    ['Users', new Location(['id' => 3])],
                    ['Home/Folder', new Location(['id' => 4])],
                    ['Home/Folder1/Folder2', new Location(['id' => 5])],
                ]
            )
        ;

        $this->locationAwareConfigurationEditor = new LocationAwareConfigurationEditor(
            new ConfigurationEditor(),
            $contentFacadeStub
        );
    }

    public function testDoesNotFailOnNonStrings()
    {
        $initialConfig = ['testKey' => [1, null]];

        $value = $this->locationAwareConfigurationEditor->get($initialConfig, 'testKey');

        Assert::assertEquals([1, null], $value);
    }

    public function testReplacesSingleValue()
    {
        $initialConfig = ['testKey' => '%location_id(Home)%'];

        $value = $this->locationAwareConfigurationEditor->get($initialConfig, 'testKey');

        Assert::assertEquals(2, $value);
    }

    public function testReplacesMultipleValues()
    {
        $initialConfig = ['testKey' => ['%location_id(Home)%', '%location_id(Users)%']];

        $value = $this->locationAwareConfigurationEditor->get($initialConfig, 'testKey');

        Assert::assertEquals([2, 3], $value);
    }

    public function testReplacesValueWhenSetIsUsed()
    {
        $initialConfig = ['testKey' => ['%location_id(Home)%']];

        $config = $this->locationAwareConfigurationEditor->set($initialConfig, 'testKey.testSection', ['testValue1', '%location_id(Users)%']);

        Assert::assertEquals(['testKey' => [2, 'testSection' => ['testValue1', 3]]], $config);
    }

    public function testReplacesValueWhenAppendIsUsed()
    {
        $initialConfig = ['testKey' => '%location_id(Home)%'];

        $config = $this->locationAwareConfigurationEditor->append($initialConfig, 'testKey', '%location_id(Home/Folder1/Folder2)%');

        Assert::assertEquals(['testKey' => [2, 5]], $config);
    }
}
