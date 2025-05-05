<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\ValueObject;

use App\Domain\Exception\InvalidEmailException;
use App\Domain\ValueObject\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function testFromStringWithValidEmailReturnsEmail(): void
    {
        $validEmail = 'test@example.com';
        $email = Email::fromString($validEmail);
        
        $this->assertInstanceOf(Email::class, $email);
        $this->assertEquals($validEmail, $email->value());
    }
    
    public function testFromStringWithEmptyEmailThrowsException(): void
    {
        $this->expectException(InvalidEmailException::class);
        $this->expectExceptionMessage('Invalid email format');
        
        Email::fromString('');
    }
    
    public function testFromStringWithInvalidEmailThrowsException(): void
    {
        $this->expectException(InvalidEmailException::class);
        $this->expectExceptionMessage('Invalid email format');
        
        Email::fromString('invalid-email');
    }
    
    public function testEqualsReturnsTrueForSameValues(): void
    {
        $emailString = 'test@example.com';
        $email1 = Email::fromString($emailString);
        $email2 = Email::fromString($emailString);
        
        $this->assertTrue($email1->equals($email2));
    }
    
    public function testEqualsReturnsFalseForDifferentValues(): void
    {
        $email1 = Email::fromString('test1@example.com');
        $email2 = Email::fromString('test2@example.com');
        
        $this->assertFalse($email1->equals($email2));
    }
    
    public function testToStringReturnsValue(): void
    {
        $emailString = 'test@example.com';
        $email = Email::fromString($emailString);
        
        $this->assertEquals($emailString, (string)$email);
    }
}
