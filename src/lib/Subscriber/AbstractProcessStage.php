<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Subscriber;

use Ibexa\Behat\API\ContentData\RandomDataGenerator;
use Ibexa\Behat\Event\TransitionEvent;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use PHPUnit\Framework\Assert;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

abstract class AbstractProcessStage
{
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Symfony\Component\EventDispatcher\EventDispatcher */
    private $eventDispatcher;

    /** @var \Ibexa\Behat\API\ContentData\RandomDataGenerator */
    protected $randomDataGenerator;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        UserService $userService,
        PermissionResolver $permissionResolver,
        LoggerInterface $logger,
        RandomDataGenerator $randomDataGenerator
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->userService = $userService;
        $this->permissionResolver = $permissionResolver;
        $this->logger = $logger;
        $this->validateTransitions();
        $this->randomDataGenerator = $randomDataGenerator;
    }

    abstract protected function getTransitions(): array;

    protected function transitionToNextStage($event)
    {
        $threshold = 0;
        $randomValue = $this->randomDataGenerator->getRandomProbability();

        $chosenEvent = null;

        foreach ($this->getTransitions() as $eventName => $probability) {
            if ($threshold <= $randomValue && $randomValue < $threshold + $probability) {
                $chosenEvent = $eventName;
            }

            $threshold += $probability;
        }

        if (!$chosenEvent) {
            Assert::fail('No event was chosen, possible error in transition logic.');
        }

        $this->eventDispatcher->dispatch($event, $chosenEvent);
    }

    protected function setCurrentUser(string $userLogin): void
    {
        $user = $this->userService->loadUserByLogin($userLogin);
        $this->permissionResolver->setCurrentUserReference($user);
    }

    protected function getCurrentUser(): User
    {
        $userRef = $this->permissionResolver->getCurrentUserReference();

        return $this->userService->loadUser($userRef->getUserId());
    }

    protected function validateTransitions(): void
    {
        $sum = 0;
        foreach ($this->getTransitions() as $event => $probability) {
            $sum += $probability;
        }

        Assert::assertEquals(1, $sum, 'Sum of all probabilities must be equal to 1.');
    }

    protected function getRandomValue(array $values): string
    {
        return $values[array_rand($values, 1)];
    }

    public function execute(Event $event): void
    {
        try {
            $this->doExecute($event);
        } catch (\Exception $ex) {
            $this->logger->log(LogLevel::ERROR, sprintf('Error occured during %s Stage: %s', static::class, $ex->getMessage()));

            return;
        }
        $this->transitionToNextStage($event);
    }

    abstract protected function doExecute(TransitionEvent $event): void;
}
