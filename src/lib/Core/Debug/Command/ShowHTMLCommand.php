<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Core\Debug\Command;

use Behat\Mink\Session;
use Psy\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[\Symfony\Component\Console\Attribute\AsCommand(name: 'html', description: 'Displays the HTML of the currently opened website')]
class ShowHTMLCommand extends Command
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
        $output->writeln($this->session->getPage()->getOuterHtml());

        return self::SUCCESS;
    }
}
