<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Core\Debug\Command;

use Behat\Mink\Session;
use Psy\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'back', description: "Goes back one page in browser's history")]
class GoBackCommand extends Command
{
    /** @var \Behat\Mink\Session */
    protected $session;

    public function __construct(Session $session)
    {
        parent::__construct();
        $this->session = $session;
    }

    protected function configure()
    {
        $this
            ->setDefinition([])
            ->setHelp('');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->session->back();

        $output->writeln("The last page from browser's history has been visited.");

        return self::SUCCESS;
    }
}
