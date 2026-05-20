<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\API\Context\LimitationParser;

class LimitationParsersCollector
{
    /** @var LimitationParserInterface[] */
    private $limitationParsers;

    /**
     * @param LimitationParserInterface[] $limitationParsers
     */
    public function __construct(array $limitationParsers = [])
    {
        $this->limitationParsers = $limitationParsers;
    }

    public function addLimitationParser(LimitationParserInterface $limitationParser): void
    {
        $this->limitationParsers[] = $limitationParser;
    }

    /**
     * @return LimitationParserInterface[]
     */
    public function getLimitationParsers(): array
    {
        return $this->limitationParsers;
    }
}
