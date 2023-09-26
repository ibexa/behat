<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Page;

use Ibexa\Behat\Browser\Locator\CSSLocator;
use PHPUnit\Framework\Assert;

class RedirectLoginPage extends LoginPage
{
    public function getName(): string
    {
        return 'Login page with redirect';
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertStringContainsString('/login', $this->getSession()->getCurrentUrl());
    }

    public function loginSuccessfully($username, $password): void
    {
        parent::loginSuccessfully($username, $password);
        $this->getHTMLPage()
            ->findAll(new CSSLocator('loginSuccess', '#login-success'))
            ->assert()->hasElements();
    }

    protected function getRoute(): string
    {
        return '/unauthenticated_login_redirect';
    }
}
