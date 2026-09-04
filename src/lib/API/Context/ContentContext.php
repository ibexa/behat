<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\API\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Step\Given;
use Ibexa\Behat\API\Facade\ContentFacade;
use Ibexa\Behat\Core\Behat\ArgumentParser;

class ContentContext implements Context
{
    /** @var ContentFacade */
    private $contentFacade;

    /** @var ArgumentParser */
    private $argumentParser;

    public function __construct(
        ContentFacade $contentFacade,
        ArgumentParser $argumentParser
    ) {
        $this->contentFacade = $contentFacade;
        $this->argumentParser = $argumentParser;
    }

    #[Given('I create :number :contentTypeIdentifier Content items in :parentUrl in :language')]
    public function createMultipleContentItems(
        string $numberOfItems,
        string $contentTypeIdentifier,
        string $parentUrl,
        string $language
    ): void {
        $parentUrl = $this->argumentParser->parseUrl($parentUrl);

        for ($i = 0; $i < $numberOfItems; ++$i) {
            $this->contentFacade->createContent($contentTypeIdentifier, $parentUrl, $language);
        }
    }

    #[Given('a :contentTypeIdentifier Content item named :contentName exists in :parentUrl')]
    public function contentItemExists(
        string $contentTypeIdentifier,
        string $contentName,
        string $parentUrl,
        TableNode $contentItemData
    ): void {
        $parentUrl = $this->argumentParser->parseUrl($parentUrl);
        $contentUrl = sprintf('%s/%s', $parentUrl, $this->argumentParser->parseUrl($contentName));
        $contentData = $this->parseData($contentItemData)[0];
        $this->contentFacade->createContentIfNotExists($contentTypeIdentifier, $contentUrl, $parentUrl, $contentData);
    }

    #[Given('I create :contentTypeIdentifier Content items in :parentUrl in :language')]
    public function createContentItems(
        string $contentTypeIdentifier,
        string $parentUrl,
        string $language,
        TableNode $contentItemsData
    ): void {
        $parentUrl = $this->argumentParser->parseUrl($parentUrl);
        $parsedContentItemData = $this->parseData($contentItemsData);

        foreach ($parsedContentItemData as $contentItemData) {
            $this->contentFacade->createContent($contentTypeIdentifier, $parentUrl, $language, $contentItemData);
        }
    }

    #[Given('I create :contentTypeIdentifier Content items')]
    public function createContentItemsInDifferentLocations(
        string $contentTypeIdentifier,
        TableNode $contentItemsData
    ): void {
        $parsedContentItemData = $this->parseData($contentItemsData);

        foreach ($parsedContentItemData as $contentItemData) {
            $parentUrl = $this->argumentParser->parseUrl($contentItemData['parentPath']);
            $language = $contentItemData['language'];
            unset($contentItemData['parentPath'], $contentItemData['language']);

            $this->contentFacade->createContent($contentTypeIdentifier, $parentUrl, $language, $contentItemData);
        }
    }

    #[Given('I create :contentTypeIdentifier Content drafts')]
    public function createContentDraftsInDifferentLocations(
        string $contentTypeIdentifier,
        TableNode $contentItemsData
    ): void {
        $parsedContentItemData = $this->parseData($contentItemsData);

        foreach ($parsedContentItemData as $contentItemData) {
            $parentUrl = $this->argumentParser->parseUrl($contentItemData['parentPath']);
            $language = $contentItemData['language'];
            unset($contentItemData['parentPath'], $contentItemData['language']);

            $this->contentFacade->createContentDraft($contentTypeIdentifier, $parentUrl, $language, $contentItemData);
        }
    }

    #[Given('I edit :locationURL Content item in :language')]
    public function editContentItem(
        string $locationURL,
        string $language,
        TableNode $contentItemsData
    ): void {
        $locationURL = $this->argumentParser->parseUrl($locationURL);
        $parsedContentItemData = $this->parseData($contentItemsData);

        foreach ($parsedContentItemData as $contentItemData) {
            $this->contentFacade->editContent($locationURL, $language, $contentItemData);
        }
    }

    #[Given('I create a new Draft for :locationURL Content item in :language')]
    public function createNewDraftForExistingItem(
        string $locationURL,
        string $language,
        TableNode $contentItemsData
    ): void {
        $locationURL = $this->argumentParser->parseUrl($locationURL);
        $parsedContentItemData = $this->parseData($contentItemsData);

        foreach ($parsedContentItemData as $contentItemData) {
            $this->contentFacade->createDraftForExistingContent($locationURL, $language, $contentItemData);
        }
    }

    private function parseData(TableNode $contentItemData)
    {
        return $contentItemData->getHash();
    }
}
