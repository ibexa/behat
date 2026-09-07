<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\FileUpload;

use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;

class FileReadException extends \RuntimeException {}

class FileUploadHelper
{
    /** @var MinkParameters */
    private $minkParameters;

    public function __construct(MinkParameters $minkParameters)
    {
        $this->minkParameters = $minkParameters;
    }

    public function getRemoteFileUploadPath($filename)
    {
        $localFile = sprintf('%s%s', $this->minkParameters['files_path'], $filename);

        return $localFile;
    }
}
