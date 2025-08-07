<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Page;

use Exception;
use Ibexa\Behat\Browser\Exception\ElementNotFoundException;
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
        for ($attempt = 0; $attempt < 3; ++$attempt) {
            try {
                parent::loginSuccessfully($username, $password);
                $this->getHTMLPage()
                    ->findAll(new CSSLocator('loginSuccess', '#login-success'))
                    ->assert()->hasElements();

                return;
            } catch (Exception $e) {
                // Retry on failure
            }
        }
        throw new ElementNotFoundException('Login failed after multiple attempts.');
    }

    protected function getRoute(): string
    {
        return '/unauthenticated/login_redirect';
    }
}
