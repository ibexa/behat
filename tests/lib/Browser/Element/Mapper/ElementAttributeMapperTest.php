<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Behat\Browser\Element\Mapper;

use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Element\Mapper\ElementAttributeMapper;
use Ibexa\Tests\Behat\Browser\Element\BaseTestCase;
use PHPUnit\Framework\Assert;

class ElementAttributeMapperTest extends BaseTestCase
{
    private ElementAttributeMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new ElementAttributeMapper('data-id');
    }

    public function testMapsSingleElement(): void
    {
        $element = $this->createStub(ElementInterface::class);
        $element->method('getAttribute')->willReturn('AttributeValue');

        Assert::assertEquals('AttributeValue', $this->mapper->map($element));
    }
}
