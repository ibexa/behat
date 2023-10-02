<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Context;

use Behat\MinkExtension\Context\RawMinkContext;
use Ibexa\Behat\API\ContentData\FieldTypeData\PasswordProvider;
use Ibexa\Behat\Browser\Page\LoginPage;
use Ibexa\Behat\Browser\Page\RedirectLoginPage;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;

class AuthenticationContext extends RawMinkContext
{
    private LoginPage $loginPage;

    private RedirectLoginPage $redirectLoginPage;

    public function __construct(LoginPage $loginPage, RedirectLoginPage $redirectLoginPage)
    {
        $this->loginPage = $loginPage;
        $this->redirectLoginPage = $redirectLoginPage;
    }

    /**
     * @Given I log in as :username
     * @Given I log in as :username with password :password
     */
    public function iLogInIn(string $username, string $password = null)
    {
        $password = $password ?? PasswordProvider::DEFAUlT_PASSWORD;
        $this->loginPage->loginSuccessfully($username, $password);
    }

    /**
     * @Given I am logged as admin
     */
    public function loggedAsAdmin()
    {
        $store = new FlockStore(sys_get_temp_dir());
        $factory = new LockFactory($store);

        $lock = $factory->createLock('admin-login');

        if ($lock->acquire(true)) {
            $this->redirectLoginPage->open('admin');
            $this->redirectLoginPage->logout();
            $this->redirectLoginPage->open('admin');
            $this->redirectLoginPage->loginSuccessfully('admin', 'publish');
            $lock->release();
        }
    }

    /**
     * @Given I am viewing the pages on siteaccess :siteaccess as :username
     * @Given I am viewing the pages on siteaccess :siteaccess as :username :password
     * @Given I am viewing the pages on siteaccess :siteaccess as :username with password :password
     */
    public function iAmViewingThePagesAsUserOnSiteaccess(string $siteaccess, string $username, string $password = null)
    {
        $this->loginPage->open($siteaccess);
        $this->loginPage->logout($siteaccess);

        if (!$this->shouldPerformLoginAction($username)) {
            return;
        }

        $password = $password ?? PasswordProvider::DEFAUlT_PASSWORD;
        $this->loginPage->open($siteaccess);
        $this->loginPage->loginSuccessfully($username, $password);
    }

    private function shouldPerformLoginAction(string $username)
    {
        return 'anonymous' !== strtolower($username);
    }
}
