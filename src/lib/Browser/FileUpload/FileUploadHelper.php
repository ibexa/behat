<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\FileUpload;

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Session;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;

class FileUploadHelper
{
    /** @var \Behat\Mink\Session */
    private $session;

    /** @var \FriendsOfBehat\SymfonyExtension\Mink\MinkParameters */
    private $minkParameters;

    public function __construct(Session $session, MinkParameters $minkParameters)
    {
        $this->session = $session;
        $this->minkParameters = $minkParameters;
    }

    public function getRemoteFileUploadPath($filename)
    {
        $localFile = sprintf('%s%s', $this->minkParameters['files_path'], $filename);
        $driver = $this->session->getDriver();

        if ($driver instanceof Selenium2Driver) {
            if (!preg_match('#[\w\\\/\.]*\.zip$#', $filename)) {
                throw new \InvalidArgumentException('Zip archive required to upload to remote browser machine.');
            }

            return $driver->getWebDriverSession()->file([
                'file' => base64_encode(file_get_contents($localFile)),
            ]);
        }

        return $localFile;
    }
}
