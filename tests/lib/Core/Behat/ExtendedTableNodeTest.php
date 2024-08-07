<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Behat\Core\Behat;

use Behat\Gherkin\Node\TableNode;
use Ibexa\Behat\Core\Behat\TableNodeExtension;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @internal
 *
 * @coversNothing
 */
class ExtendedTableNodeTest extends TestCase
{
    private const MULTIPLE_ROW_DATA = [['Header1', 'Header2', 'Header3'],
        ['Value11', 'Value12', 'Value13'],
        ['Value21', 'Value22', 'Value23'], ];

    private const SINGLE_ROW_DATA = [['Header1', 'Header2', 'Header3'],
        ['Value11', 'Value12', 'Value13'], ];

    public function testCanAddNewColumnWithSingleValue()
    {
        $beforeTable = new TableNode(self::SINGLE_ROW_DATA);
        $columnToAdd = ['Header4' => 'Value14'];

        $afterTable = TableNodeExtension::addColumn($beforeTable, $columnToAdd);

        Assert::assertEquals([['Header1', 'Header2', 'Header3', 'Header4'],
            ['Value11', 'Value12', 'Value13', 'Value14'], ], $afterTable->getTable());
    }

    public function testCanAddNewColumnWithMultipleValues()
    {
        $beforeTable = new TableNode(self::MULTIPLE_ROW_DATA);
        $columnToAdd = ['Header4' => ['Value14', 'Value24']];

        $afterTable = TableNodeExtension::addColumn($beforeTable, $columnToAdd);

        Assert::assertEquals([['Header1', 'Header2', 'Header3', 'Header4'],
            ['Value11', 'Value12', 'Value13', 'Value14'],
            ['Value21', 'Value22', 'Value23', 'Value24'], ], $afterTable->getTable());
    }

    public function testCanAddNewColumnsWithSingleValues()
    {
        $beforeTable = new TableNode(self::SINGLE_ROW_DATA);
        $columnToAdd = ['Header4' => 'Value14', 'Header5' => 'Value15'];

        $afterTable = TableNodeExtension::addColumn($beforeTable, $columnToAdd);

        Assert::assertEquals([['Header1', 'Header2', 'Header3', 'Header4', 'Header5'],
            ['Value11', 'Value12', 'Value13', 'Value14', 'Value15'], ], $afterTable->getTable());
    }

    public function testCanAddNewColumnsWithMultipleValues()
    {
        $beforeTable = new TableNode(self::MULTIPLE_ROW_DATA);
        $columnToAdd = ['Header4' => ['Value14', 'Value24'], 'Header5' => ['Value15', 'Value25']];

        $afterTable = TableNodeExtension::addColumn($beforeTable, $columnToAdd);

        Assert::assertEquals([['Header1', 'Header2', 'Header3', 'Header4', 'Header5'],
            ['Value11', 'Value12', 'Value13', 'Value14', 'Value15'],
            ['Value21', 'Value22', 'Value23', 'Value24', 'Value25'], ], $afterTable->getTable());
    }

    public function testCanRemoveColumn()
    {
        $beforeTable = new TableNode(self::MULTIPLE_ROW_DATA);
        $columnToRemove = 'Header2';

        $afterTable = TableNodeExtension::removeColumn($beforeTable, $columnToRemove);

        Assert::assertEquals([['Header1', 'Header3'],
            ['Value11', 'Value13'],
            ['Value21', 'Value23'], ], $afterTable->getTable());
    }
}
