<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Behat\Cli;

use Behat\Gherkin\Node\ExampleTableNode;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\OutlineNode;
use Behat\Gherkin\Node\ScenarioNode;
use Behat\Testwork\Specification\Locator\SpecificationLocator;
use Behat\Testwork\Specification\SpecificationArrayIterator;
use Behat\Testwork\Suite\GenericSuite;
use Behat\Testwork\Suite\SuiteRepository;
use Ibexa\Bundle\Behat\Cli\ListScenariosController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

final class ListScenariosControllerTest extends TestCase
{
    public function testDoesNothingWithoutTheOption(): void
    {
        $controller = new ListScenariosController(
            self::createStub(SpecificationLocator::class),
            self::createStub(SuiteRepository::class)
        );

        $output = new BufferedOutput();

        self::assertNull($controller->execute($this->createInput($controller, false), $output));
        self::assertSame('', $output->fetch());
    }

    public function testListsScenariosAndOutlineExampleRows(): void
    {
        $scenario = new ScenarioNode('Plain scenario', [], [], 'Scenario', 10);
        $outline = new OutlineNode(
            'Outline',
            [],
            [],
            [
                new ExampleTableNode(
                    [
                        20 => ['header'],
                        21 => ['first'],
                        22 => ['second'],
                    ],
                    'Examples'
                ),
            ],
            'Scenario Outline',
            15
        );
        $feature = new FeatureNode(
            'Feature',
            null,
            [],
            null,
            [$scenario, $outline],
            'Feature',
            'en',
            '/project/features/example.feature',
            1
        );

        $suite = new GenericSuite('examples', []);

        $suiteRepository = self::createStub(SuiteRepository::class);
        $suiteRepository->method('getSuites')->willReturn([$suite]);

        $specificationLocator = self::createStub(SpecificationLocator::class);
        $specificationLocator
            ->method('locateSpecifications')
            ->willReturn(new SpecificationArrayIterator($suite, [$feature]));

        $controller = new ListScenariosController($specificationLocator, $suiteRepository);
        $output = new BufferedOutput();

        self::assertSame(0, $controller->execute($this->createInput($controller, true), $output));
        self::assertSame(
            [
                '/project/features/example.feature:10',
                '/project/features/example.feature:21',
                '/project/features/example.feature:22',
            ],
            array_values(array_filter(explode(PHP_EOL, $output->fetch())))
        );
    }

    private function createInput(
        ListScenariosController $controller,
        bool $listScenarios
    ): ArrayInput {
        $command = new Command('behat');
        $controller->configure($command);

        return new ArrayInput(
            $listScenarios ? ['--list-scenarios' => true] : [],
            $command->getDefinition()
        );
    }
}
