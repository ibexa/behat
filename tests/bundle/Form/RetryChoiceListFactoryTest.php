<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Behat\Form;

use ErrorException;
use Ibexa\Bundle\Behat\Form\RetryChoiceListFactory;
use Ibexa\Tests\Bundle\Behat\Form\Stub\UnstableChoiceListFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

final class RetryChoiceListFactoryTest extends TestCase
{
    public function setUp(): void
    {
        ClockMock::register(__CLASS__);
        ClockMock::withClockMock(true);
    }

    public function tearDown(): void
    {
        ClockMock::withClockMock(false);
    }

    /**
     * @dataProvider provider
     */
    public function testCreateListFromChoicesSuccess(int $numberOfFails): void
    {
        $retryChoiceListFactory = new RetryChoiceListFactory(new UnstableChoiceListFactory($numberOfFails));
        self::assertEquals([], $retryChoiceListFactory->createListFromChoices([], null)->getChoices());
    }

    /**
     * @dataProvider provider
     */
    public function testCreateListFromLoaderSuccess(int $numberOfFails): void
    {
        $retryChoiceListFactory = new RetryChoiceListFactory(new UnstableChoiceListFactory($numberOfFails));
        self::assertEquals([], $retryChoiceListFactory->createListFromLoader($this->createMock(ChoiceLoaderInterface::class))->getChoices());
    }

    /**
     * @dataProvider provider
     */
    public function testCreateViewSuccess(int $numberOfFails): void
    {
        $retryChoiceListFactory = new RetryChoiceListFactory(new UnstableChoiceListFactory($numberOfFails));
        self::assertEquals([], $retryChoiceListFactory->createView($this->createMock(ChoiceListInterface::class), null)->choices);
    }

    public function testCreateListFromChoicesFail(): void
    {
        $retryChoiceListFactory = new RetryChoiceListFactory(new UnstableChoiceListFactory(4));
        $this->expectException(ErrorException::class);
        $retryChoiceListFactory->createListFromChoices([], null);
    }

    public function testCreateListFromLoaderFail(): void
    {
        $retryChoiceListFactory = new RetryChoiceListFactory(new UnstableChoiceListFactory(4));
        $this->expectException(ErrorException::class);
        $retryChoiceListFactory->createListFromLoader($this->createMock(ChoiceLoaderInterface::class));
    }

    public function testCreateViewFail(): void
    {
        $retryChoiceListFactory = new RetryChoiceListFactory(new UnstableChoiceListFactory(4));
        $this->expectException(ErrorException::class);
        $retryChoiceListFactory->createView($this->createMock(ChoiceListInterface::class), null);
    }

    /**
     * @return iterable<string, array{int}>
     */
    public static function provider(): iterable
    {
        yield 'No failures' => [0];
        yield 'One failure' => [1];
        yield 'Two failures' => [2];
        yield 'Three failures' => [3];
    }
}
