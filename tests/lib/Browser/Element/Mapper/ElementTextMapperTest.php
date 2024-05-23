<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Behat\Browser\Element\Mapper;

use Ibexa\Behat\Browser\Element\Mapper\ElementTextMapper;
use Ibexa\Tests\Behat\Browser\Element\BaseTestCase;
use PHPUnit\Framework\Assert;

class ElementTextMapperTest extends BaseTestCase
{
    /** @var \Ibexa\Behat\Browser\Element\Mapper\ElementTextMapper */
    private $mapper;

    protected function setUp(): void
    {
        $this->mapper = new ElementTextMapper();
    }

    public function testMapsSingleElement(): void
    {
        $element = $this->createElement('Element1');

        Assert::assertEquals('Element1', $this->mapper->map($element));
    }
}
