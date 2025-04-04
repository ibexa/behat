<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Behat\Command;

use Ibexa\Core\MVC\Symfony\SiteAccess;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'ibexa:behat:test-siteaccess', description: 'Outputs the name of the active siteaccess')]
class TestSiteaccessCommand extends Command
{
    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess */
    private $siteaccess;

    public function __construct(SiteAccess $siteaccess)
    {
        $this->siteaccess = $siteaccess;

        parent::__construct();
    }

    protected function configure()
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln($this->siteaccess->name);

        return self::SUCCESS;
    }
}
