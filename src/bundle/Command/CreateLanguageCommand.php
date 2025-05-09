<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Behat\Command;

use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'ibexa:behat:create-language', description: 'Create a Language')]
class CreateLanguageCommand extends Command
{
    /** @var \Ibexa\Contracts\Core\Repository\LanguageService */
    private $languageService;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    public function __construct(
        LanguageService $languageService,
        UserService $userService,
        PermissionResolver $permissionResolver
    ) {
        $this->languageService = $languageService;
        $this->userService = $userService;
        $this->permissionResolver = $permissionResolver;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('language-code', InputArgument::REQUIRED)
            ->addArgument('language-name', InputArgument::OPTIONAL, 'Language name', '')
            ->addArgument(
                'user',
                InputArgument::OPTIONAL,
                'eZ Platform User with access to content / translations',
                'admin'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // set user with proper permissions to create language (content / translations)
        $this->permissionResolver->setCurrentUserReference(
            $this->userService->loadUserByLogin(
                $input->getArgument('user')
            )
        );

        $languageCreateStruct = $this->languageService->newLanguageCreateStruct();
        $languageCreateStruct->languageCode = $input->getArgument('language-code');
        $languageCreateStruct->name = $input->getArgument('language-name');

        $this->languageService->createLanguage($languageCreateStruct);

        return self::SUCCESS;
    }
}
