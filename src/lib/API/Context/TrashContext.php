<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\API\Context;

use Behat\Behat\Context\Context;
use Ibexa\Behat\API\Facade\TrashFacade;
use Ibexa\Behat\Core\Behat\ArgumentParser;

class TrashContext implements Context
{
    /** @var \Ibexa\Behat\API\Facade\TrashFacade */
    private $trashFacade;

    /** @var \Ibexa\Behat\Core\Behat\ArgumentParser */
    private $argumentParser;

    public function __construct(TrashFacade $trashFacade, ArgumentParser $argumentParser)
    {
        $this->trashFacade = $trashFacade;
        $this->argumentParser = $argumentParser;
    }

    /**
     * @Then I send :locationURL to the Trash
     *
     * @param mixed $locationURL
     */
    public function iSendToTheTrash($locationURL)
    {
        $locationURL = $this->argumentParser->parseUrl($locationURL);
        $this->trashFacade->trash($locationURL);
    }
}
