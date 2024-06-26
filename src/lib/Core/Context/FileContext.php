<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Core\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class FileContext implements Context
{
    private const SOURCE_FILE_DIRECTORY = 'vendor/ibexa/behat/src/lib/Data';

    /** @var string */
    private $projectDirectory;

    public function __construct($projectDirectory)
    {
        $this->projectDirectory = $projectDirectory;
    }

    /**
     * @Given I create a file :path with content from :sourceFile
     *
     * @param mixed $path
     * @param mixed $sourceFile
     */
    public function createFileFromSourceFile($path, $sourceFile): void
    {
        $content = file_get_contents(sprintf('%s/%s/%s', $this->projectDirectory, self::SOURCE_FILE_DIRECTORY, $sourceFile));
        $destinationPath = sprintf('%s/%s', $this->projectDirectory, $path);
        $this->createDirectoryStructure($destinationPath);
        file_put_contents($destinationPath, $content);
    }

    /**
     * @Given I append to :file file :sourcePath
     *
     * @param mixed $file
     * @param mixed $sourceFile
     */
    public function appendToFile($file, $sourceFile): void
    {
        $content = file_get_contents(sprintf('%s/%s/%s', $this->projectDirectory, self::SOURCE_FILE_DIRECTORY, $sourceFile));
        file_put_contents($file, $content, FILE_APPEND | LOCK_EX);
    }

    /**
     * @Given I create a file :path with contents
     */
    public function createFileFromContent(string $path, PyStringNode $fileContent): void
    {
        $destinationPath = sprintf('%s/%s', $this->projectDirectory, $path);
        $this->createDirectoryStructure($destinationPath);
        file_put_contents($destinationPath, $fileContent->getRaw());
    }

    /**
     * @Given I apply the patch
     */
    public function patchFile(PyStringNode $patchContent)
    {
        $this->createFileFromContent('patch.patch', $patchContent);
        $process = new Process(['patch', '-d', $this->projectDirectory, '-i', 'patch.patch', '-Np1']);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    private function createDirectoryStructure($destinationPath): void
    {
        $directoryStructure = \dirname($destinationPath);
        if (!file_exists($directoryStructure)) {
            mkdir($directoryStructure, 0777, true);
        }
    }
}
