<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\ValueObject;

use App\Domain\ValueObject\Name;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    public function testFromStringWithValidNameReturnsName(): void
    {
        $validName = 'John Doe';
        $name = Name::fromString($validName);
        
        $this->assertInstanceOf(Name::class, $name);
        $this->assertEquals($validName, $name->value());
    }
    
    public function testFromStringWithTooShortNameThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Name must be at least 2 characters long');
        
        Name::fromString('A');
    }
    
    public function testFromStringWithTooLongNameThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Name cannot exceed 50 characters');
        
        $tooLongName = str_repeat('A', 51);
        Name::fromString($tooLongName);
    }
    
    public function testFromStringWithInvalidCharactersThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Name can only contain letters and spaces');
        
        Name::fromString('John123');
    }
    
    public function testEqualsReturnsTrueForSameValues(): void
    {
        $nameString = 'John Doe';
        $name1 = Name::fromString($nameString);
        $name2 = Name::fromString($nameString);
        
        $this->assertTrue($name1->equals($name2));
    }
    
    public function testEqualsReturnsFalseForDifferentValues(): void
    {
        $name1 = Name::fromString('John Doe');
        $name2 = Name::fromString('Jane Doe');
        
        $this->assertFalse($name1->equals($name2));
    }
    
    public function testToStringReturnsValue(): void
    {
        $nameString = 'John Doe';
        $name = Name::fromString($nameString);
        
        $this->assertEquals($nameString, (string)$name);
    }
}
