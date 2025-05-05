<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\ValueObject;

use App\Domain\Exception\WeakPasswordException;
use App\Domain\ValueObject\Password;
use PHPUnit\Framework\TestCase;

class PasswordTest extends TestCase
{
    public function testFromPlainPasswordWithValidPasswordReturnsPassword(): void
    {
        $validPassword = 'StrongP@ss123';
        $password = Password::fromPlainPassword($validPassword);
        
        $this->assertInstanceOf(Password::class, $password);
        $this->assertNotEmpty($password->value());
    }
    
    public function testFromPlainPasswordHashesThePassword(): void
    {
        $plainPassword = 'StrongP@ss123';
        $password = Password::fromPlainPassword($plainPassword);
        
        // Verify the value is not the same as the plain password
        $this->assertNotEquals($plainPassword, $password->value());
        
        // Verify that the password is correctly hashed by checking it
        $this->assertTrue($password->verify($plainPassword));
    }
    
    public function testFromPlainPasswordWithTooShortPasswordThrowsException(): void
    {
        $this->expectException(WeakPasswordException::class);
        $this->expectExceptionMessage('Password must be at least 8 characters long');
        
        Password::fromPlainPassword('Short1!');
    }
    
    public function testFromPlainPasswordWithNoUppercaseThrowsException(): void
    {
        $this->expectException(WeakPasswordException::class);
        $this->expectExceptionMessage('Password must contain at least one uppercase letter, one number, and one special character');
        
        Password::fromPlainPassword('password123!');
    }
    
    public function testFromPlainPasswordWithNoNumberThrowsException(): void
    {
        $this->expectException(WeakPasswordException::class);
        $this->expectExceptionMessage('Password must contain at least one uppercase letter, one number, and one special character');
        
        Password::fromPlainPassword('Password!');
    }
    
    public function testFromPlainPasswordWithNoSpecialCharThrowsException(): void
    {
        $this->expectException(WeakPasswordException::class);
        $this->expectExceptionMessage('Password must contain at least one uppercase letter, one number, and one special character');
        
        Password::fromPlainPassword('Password123');
    }
    
    public function testFromHashReturnsPassword(): void
    {
        $hash = password_hash('StrongP@ss123', PASSWORD_BCRYPT);
        $password = Password::fromHash($hash);
        
        $this->assertInstanceOf(Password::class, $password);
        $this->assertEquals($hash, $password->value());
    }
    
    public function testVerifyReturnsTrueForCorrectPassword(): void
    {
        $plainPassword = 'StrongP@ss123';
        $password = Password::fromPlainPassword($plainPassword);
        
        $this->assertTrue($password->verify($plainPassword));
    }
    
    public function testVerifyReturnsFalseForIncorrectPassword(): void
    {
        $password = Password::fromPlainPassword('StrongP@ss123');
        
        $this->assertFalse($password->verify('WrongP@ss123'));
    }
    
    public function testToStringReturnsValue(): void
    {
        $hash = password_hash('StrongP@ss123', PASSWORD_BCRYPT);
        $password = Password::fromHash($hash);
        
        $this->assertEquals($hash, (string)$password);
    }
}
