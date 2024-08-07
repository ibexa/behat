<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\API\Context;

use Behat\Behat\Context\Context;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;

class TestContext implements Context
{
    private $permissionResolver;

    private $userService;

    public function __construct(UserService $userService, PermissionResolver $permissionResolver)
    {
        $this->userService = $userService;
        $this->permissionResolver = $permissionResolver;
    }

    /**
     * @Given I am using the API as :username
     */
    public function iAmLoggedAsApiUser(string $username)
    {
        $user = $this->userService->loadUserByLogin($username);
        $this->permissionResolver->setCurrentUserReference($user);
    }

    /**
     * @BeforeScenario
     */
    public function loginAdminBeforeScenarioHook()
    {
        $this->iAmLoggedAsApiUser('admin');
    }
}
