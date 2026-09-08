<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Behat\Cli;

use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\OutlineNode;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Testwork\Cli\Controller;
use Behat\Testwork\Specification\Locator\SpecificationLocator;
use Behat\Testwork\Suite\SuiteRepository;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Provides the --list-scenarios option consumed by bin/ibexabehat parallel mode, which feeds
 * the printed "file.feature:line" list into the fastest runner. Behat itself has no listing
 * option; up to Behat 3 it was provided by the liuggio/fastest ListFeaturesExtension, whose
 * untyped extension class cannot load on Behat 4.
 */
final class ListScenariosController implements Controller
{
    /**
     * @param SpecificationLocator<FeatureNode> $specificationLocator
     */
    public function __construct(
        private readonly SpecificationLocator $specificationLocator,
        private readonly SuiteRepository $suiteRepository
    ) {}

    public function configure(SymfonyCommand $command): void
    {
        $command->addOption(
            'list-scenarios',
            null,
            InputOption::VALUE_NONE,
            'Output a list of individual scenarios that would be executed, as one "file.feature:line" per line'
        );
    }

    public function execute(
        InputInterface $input,
        OutputInterface $output
    ): ?int {
        if (!$input->getOption('list-scenarios')) {
            return null;
        }

        foreach ($this->suiteRepository->getSuites() as $suite) {
            foreach ($this->specificationLocator->locateSpecifications($suite, '') as $feature) {
                if (!$feature instanceof FeatureNode || $feature->getFile() === null) {
                    continue;
                }

                foreach ($feature->getScenarios() as $scenario) {
                    foreach ($this->getScenarioLines($scenario) as $line) {
                        $output->writeln(sprintf('%s:%d', $feature->getFile(), $line));
                    }
                }
            }
        }

        return 0;
    }

    /**
     * An outline is listed once per example row, so the rows run in parallel like plain scenarios.
     *
     * @return int[]
     */
    private function getScenarioLines(ScenarioInterface $scenario): array
    {
        if (!$scenario instanceof OutlineNode) {
            return [$scenario->getLine()];
        }

        $lines = [];
        foreach ($scenario->getExampleTables() as $exampleTable) {
            $tableLines = $exampleTable->getLines();
            // The first line of an example table is its header row, not a runnable example.
            array_shift($tableLines);
            $lines = array_merge($lines, $tableLines);
        }

        return $lines;
    }
}
