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

    public function createListFromChoices(iterable $choices, ?callable $value = null)
    {
        ++$this->createListFromChoicesCounter;
        $this->failIfNeeded($this->createListFromChoicesCounter);

        return new ArrayChoiceList([]);
    }

    public function createListFromLoader(ChoiceLoaderInterface $loader, ?callable $value = null)
    {
        ++$this->createListFromLoaderCounter;
        $this->failIfNeeded($this->createListFromLoaderCounter);

        return new ArrayChoiceList([]);
    }

    public function createView(ChoiceListInterface $list, $preferredChoices = null, $label = null, ?callable $index = null, ?callable $groupBy = null, $attr = null)
    {
        ++$this->createViewCounter;
        $this->failIfNeeded($this->createViewCounter);

        return new ChoiceListView([]);
    }

    private function failIfNeeded(int $callNumber): void
    {
        if ($callNumber <= $this->successfulCallAfterNthTry) {
            throw new ErrorException(sprintf('Failing call: %d', $callNumber));
        }
    }
}
