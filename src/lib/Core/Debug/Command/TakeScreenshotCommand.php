<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Core\Debug\Command;

use Behat\Mink\Session;
use Cloudinary;
use Cloudinary\Uploader;
use Exception;
use Psy\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'screenshot', description: 'Takes a screenshot of the currently opened website')]
class TakeScreenshotCommand extends Command
{
    private const CLOUD_NAME_KEY = 'cloud_name';
    private const PRESET_KEY = 'preset';
    private const CLOUD_NAME = 'ezplatformtravis';
    private const PRESET = 'ezplatform';

    /** @var \Behat\Mink\Session */
    private $session;

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
        $image = $this->session->getScreenshot();
        $filePath = sys_get_temp_dir() . \DIRECTORY_SEPARATOR . uniqid('debug') . '.png';

        file_put_contents($filePath, $image);

        Cloudinary::config([self::CLOUD_NAME_KEY => self::CLOUD_NAME, self::PRESET_KEY => self::PRESET]);

        try {
            $response = Uploader::unsigned_upload($filePath, self::PRESET);
            $output->writeln(sprintf('Open image at %s', $response['secure_url']));

            return self::SUCCESS;
        } catch (Exception $e) {
            $output->writeln(sprintf('Error while uploading image. %s', $e->getMessage()));

            return $e->getCode();
        }
    }
}
