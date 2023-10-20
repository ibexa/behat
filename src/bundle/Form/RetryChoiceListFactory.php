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

    /** {@inheritDoc} */
    public function createListFromChoices(iterable $choices, callable $value = null): ChoiceListInterface
    {
        $filter = \func_num_args() > 2 ? func_get_arg(2) : null;

        return $this->executeWithRetry(function () use ($choices, $value, $filter) {
            return $this->choiceListFactory->createListFromChoices($choices, $value, $filter);
        });
    }

    /** {@inheritDoc} */
    public function createListFromLoader(ChoiceLoaderInterface $loader, callable $value = null): ChoiceListInterface
    {
        $filter = \func_num_args() > 2 ? func_get_arg(2) : null;

        return $this->executeWithRetry(function () use ($loader, $value, $filter) {
            return $this->choiceListFactory->createListFromLoader($loader, $value, $filter);
        });
    }

    /** {@inheritDoc} */
    public function createView(
        ChoiceListInterface $list,
        $preferredChoices = null,
        $label = null,
        callable $index = null,
        callable $groupBy = null,
        $attr = null
    ): ChoiceListView {
        $labelTranslationParameters = \func_num_args() > 6 ? func_get_arg(6) : [];

        return $this->executeWithRetry(function () use ($list, $preferredChoices, $label, $index, $groupBy, $attr, $labelTranslationParameters) {
            return $this->choiceListFactory->createView(
                $list,
                $preferredChoices,
                $label,
                $index,
                $groupBy,
                $attr,
                $labelTranslationParameters
            );
        });
    }

    /**
     * @template T
     *
     * @param callable(mixed ...$args): T $fn
     *
     * @return T
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
            }
        }
    }
}
