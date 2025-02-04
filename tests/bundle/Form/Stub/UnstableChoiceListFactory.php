<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Behat\Form\Stub;

use ErrorException;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use Symfony\Component\Form\ChoiceList\View\ChoiceListView;

final class UnstableChoiceListFactory implements ChoiceListFactoryInterface
{
    private int $successfulCallAfterNthTry;

    private int $createListFromChoicesCounter = 0;

    private int $createListFromLoaderCounter = 0;

    private int $createViewCounter = 0;

    public function __construct(int $successfulCallAfterNthTry)
    {
        $this->successfulCallAfterNthTry = $successfulCallAfterNthTry;
    }

    /**
     * @param iterable<string, mixed> $choices
     *
     * @throws \ErrorException
     */
    public function createListFromChoices(iterable $choices, callable $value = null, ?callable $filter = null): ChoiceListInterface
    {
        ++$this->createListFromChoicesCounter;
        $this->failIfNeeded($this->createListFromChoicesCounter);

        return new ArrayChoiceList([]);
    }

    /**
     * @throws \ErrorException
     */
    public function createListFromLoader(
        ChoiceLoaderInterface $loader,
        callable $value = null,
        ?callable $filter = null
    ): ChoiceListInterface {
        ++$this->createListFromLoaderCounter;
        $this->failIfNeeded($this->createListFromLoaderCounter);

        return new ArrayChoiceList([]);
    }

    /**
     * @param array<string, mixed>|callable|null $preferredChoices
     * @param array<string, mixed>|callable|null $attr
     * @param array<string, mixed>|callable $labelTranslationParameters
     *
     * @throws \ErrorException
     */
    public function createView(
        ChoiceListInterface $list,
        array|callable|null $preferredChoices = null,
        callable|false|null $label = null,
        ?callable $index = null,
        ?callable $groupBy = null,
        array|callable|null $attr = null,
        array|callable $labelTranslationParameters = []/* , bool $duplicatePreferredChoices = true */
    ): ChoiceListView {
        ++$this->createViewCounter;
        $this->failIfNeeded($this->createViewCounter);

        return new ChoiceListView([]);
    }

    /**
     * @throws \ErrorException
     */
    private function failIfNeeded(int $callNumber): void
    {
        if ($callNumber <= $this->successfulCallAfterNthTry) {
            throw new ErrorException(sprintf('Failing call: %d', $callNumber));
        }
    }
}
