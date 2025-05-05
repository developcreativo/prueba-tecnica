<?php

declare(strict_types=1);

namespace Tests\Unit\Application\UseCase;

use App\Application\DTO\RegisterUserRequest;
use App\Application\DTO\UserResponseDTO;
use App\Application\UseCase\RegisterUserUseCase;
use App\Domain\Entity\User;
use App\Domain\Event\EventDispatcherInterface;
use App\Domain\Event\UserRegisteredEvent;
use App\Domain\Exception\UserAlreadyExistsException;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\ValueObject\Email;
use PHPUnit\Framework\TestCase;
use Mockery;
use Mockery\MockInterface;

class RegisterUserUseCaseTest extends TestCase
{
    private UserRepositoryInterface|MockInterface $userRepository;
    private EventDispatcherInterface|MockInterface $eventDispatcher;
    private RegisterUserUseCase $useCase;

    protected function setUp(): void
    {
        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->eventDispatcher = Mockery::mock(EventDispatcherInterface::class);
        $this->useCase = new RegisterUserUseCase(
            $this->userRepository,
            $this->eventDispatcher
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testExecuteCreatesAndSavesUser(): void
    {
        // Arrange
        $request = new RegisterUserRequest(
            'John Doe',
            'john.doe@example.com',
            'StrongP@ss123'
        );

        // Mock repository to return null for findByEmail (user doesn't exist)
        $this->userRepository
            ->shouldReceive('findByEmail')
            ->once()
            ->with(Mockery::type(Email::class))
            ->andReturnNull();

        // Expect save to be called once with User entity
        $this->userRepository
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::type(User::class));

        // Expect event dispatcher to be called once with UserRegisteredEvent
        $this->eventDispatcher
            ->shouldReceive('dispatch')
            ->once()
            ->with(Mockery::type(UserRegisteredEvent::class));

        // Act
        $result = $this->useCase->execute($request);

        // Assert
        $this->assertInstanceOf(UserResponseDTO::class, $result);
        
        $userData = $result->toArray();
        $this->assertArrayHasKey('id', $userData);
        $this->assertArrayHasKey('name', $userData);
        $this->assertArrayHasKey('email', $userData);
        $this->assertArrayHasKey('created_at', $userData);
        
        $this->assertEquals('John Doe', $userData['name']);
        $this->assertEquals('john.doe@example.com', $userData['email']);
    }

    public function testExecuteThrowsExceptionWhenUserAlreadyExists(): void
    {
        // Arrange
        $request = new RegisterUserRequest(
            'John Doe',
            'john.doe@example.com',
            'StrongP@ss123'
        );

        // Instead of creating a mock User object, simulate a non-null return
        // This is needed because User is marked as final and can't be mocked directly
        $this->userRepository
            ->shouldReceive('findByEmail')
            ->once()
            ->with(Mockery::type(Email::class))
            ->andReturn(User::create(
                \App\Domain\ValueObject\UserId::generate(),
                \App\Domain\ValueObject\Name::fromString('Existing User'),
                Email::fromString('john.doe@example.com'),
                \App\Domain\ValueObject\Password::fromPlainPassword('StrongP@ss123')
            ));

        // Expect exception
        $this->expectException(UserAlreadyExistsException::class);
        $this->expectExceptionMessage("Email already in use: {$request->email()}");

        // Act
        $this->useCase->execute($request);
    }
}
