<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Behat\Form;

use ErrorException;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use Symfony\Component\Form\ChoiceList\View\ChoiceListView;

final class RetryChoiceListFactory implements ChoiceListFactoryInterface
{
    private const RETRY_LIMIT = 2;

    private ChoiceListFactoryInterface $choiceListFactory;

    public function __construct(ChoiceListFactoryInterface $choiceListFactory)
    {
        $this->choiceListFactory = $choiceListFactory;
    }

    /**
     * @param iterable<string, mixed> $choices
     *
     * @throws \ErrorException
     */
    public function createListFromChoices(
        iterable $choices,
        ?callable $value = null,
        ?callable $filter = null
    ): ChoiceListInterface {
        return $this->executeWithRetry(function () use ($choices, $value, $filter) {
            return $this->choiceListFactory->createListFromChoices($choices, $value, $filter);
        });
    }

    /**
     * @throws \ErrorException
     */
    public function createListFromLoader(
        ChoiceLoaderInterface $loader,
        ?callable $value = null,
        ?callable $filter = null
    ): ChoiceListInterface {
        return $this->executeWithRetry(function () use ($loader, $value, $filter) {
            return $this->choiceListFactory->createListFromLoader($loader, $value, $filter);
        });
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
        array|callable $labelTranslationParameters = []
        /* , bool $duplicatePreferredChoices = true */
    ): ChoiceListView {
        return $this->executeWithRetry(
            function () use ($list, $preferredChoices, $label, $index, $groupBy, $attr, $labelTranslationParameters) {
                return $this->choiceListFactory->createView(
                    $list,
                    $preferredChoices,
                    $label,
                    $index,
                    $groupBy,
                    $attr,
                    $labelTranslationParameters
                );
            }
        );
    }

    /**
     * @template T
     *
     * @param callable(mixed ...$args): T $fn
     *
     * @return T
     *
     * @throws \ErrorException
     */
    private function executeWithRetry(callable $fn)
    {
        $counter = 0;
        while (true) {
            try {
                return $fn();
            } catch (ErrorException $e) {
                if ($counter > self::RETRY_LIMIT) {
                    throw $e;
                }
                ++$counter;
                usleep(100000 * 2 ** $counter);
            }
        }
    }
}
