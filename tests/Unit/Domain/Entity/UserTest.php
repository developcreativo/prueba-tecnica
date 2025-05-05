<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entity;

use App\Domain\Entity\User;
use App\Domain\ValueObject\UserId;
use App\Domain\ValueObject\Email;
use App\Domain\ValueObject\Name;
use App\Domain\ValueObject\Password;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserCreation(): void
    {
        $id = UserId::generate();
        $name = Name::fromString('John Doe');
        $email = Email::fromString('john.doe@example.com');
        $password = Password::fromPlainPassword('StrongP@ss123');

        $user = User::create($id, $name, $email, $password);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($id, $user->id());
        $this->assertEquals($name, $user->name());
        $this->assertEquals($email, $user->email());
        $this->assertEquals($password, $user->password());
        $this->assertInstanceOf(DateTimeImmutable::class, $user->createdAt());
    }

    public function testUserAccessors(): void
    {
        $id = UserId::generate();
        $name = Name::fromString('John Doe');
        $email = Email::fromString('john.doe@example.com');
        $password = Password::fromPlainPassword('StrongP@ss123');

        $user = User::create($id, $name, $email, $password);

        $this->assertTrue($id->equals($user->id()));
        $this->assertTrue($name->equals($user->name()));
        $this->assertTrue($email->equals($user->email()));
        $this->assertSame($password->value(), $user->password()->value());
    }
}
