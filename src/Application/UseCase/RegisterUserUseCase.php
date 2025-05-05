<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\RegisterUserRequest;
use App\Application\DTO\UserResponseDTO;
use App\Domain\Entity\User;
use App\Domain\Event\EventDispatcherInterface;
use App\Domain\Event\UserRegisteredEvent;
use App\Domain\Exception\UserAlreadyExistsException;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\ValueObject\Email;
use App\Domain\ValueObject\Name;
use App\Domain\ValueObject\Password;
use App\Domain\ValueObject\UserId;

final class RegisterUserUseCase
{
    private UserRepositoryInterface $userRepository;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        UserRepositoryInterface $userRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->userRepository = $userRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function execute(RegisterUserRequest $request): UserResponseDTO
    {
        $email = Email::fromString($request->email());
        
        // Check if the email is already in use
        $existingUser = $this->userRepository->findByEmail($email);
        if ($existingUser !== null) {
            throw new UserAlreadyExistsException("Email already in use: {$request->email()}");
        }
        
        // Create user entity
        $user = User::create(
            UserId::generate(),
            Name::fromString($request->name()),
            $email,
            Password::fromPlainPassword($request->password())
        );
        
        // Save user
        $this->userRepository->save($user);
        
        // Dispatch event
        $this->eventDispatcher->dispatch(new UserRegisteredEvent($user));
        
        // Return response DTO
        return UserResponseDTO::fromUser($user);
    }
}
