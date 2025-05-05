<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\ValueObject;

use App\Domain\ValueObject\UserId;
use PHPUnit\Framework\TestCase;

class UserIdTest extends TestCase
{
    public function testGenerateReturnsUserId(): void
    {
        $userId = UserId::generate();
        
        $this->assertInstanceOf(UserId::class, $userId);
        $this->assertNotEmpty($userId->value());
    }
    
    public function testFromStringWithValidUuidReturnsUserId(): void
    {
        $validUuid = '550e8400-e29b-41d4-a716-446655440000';
        $userId = UserId::fromString($validUuid);
        
        $this->assertInstanceOf(UserId::class, $userId);
        $this->assertEquals($validUuid, $userId->value());
    }
    
    public function testFromStringWithInvalidUuidThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid user ID format');
        
        UserId::fromString('invalid-uuid');
    }
    
    public function testEqualsReturnsTrueForSameValues(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $userId1 = UserId::fromString($uuid);
        $userId2 = UserId::fromString($uuid);
        
        $this->assertTrue($userId1->equals($userId2));
    }
    
    public function testEqualsReturnsFalseForDifferentValues(): void
    {
        $userId1 = UserId::fromString('550e8400-e29b-41d4-a716-446655440000');
        $userId2 = UserId::fromString('650e8400-e29b-41d4-a716-446655440000');
        
        $this->assertFalse($userId1->equals($userId2));
    }
    
    public function testToStringReturnsValue(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $userId = UserId::fromString($uuid);
        
        $this->assertEquals($uuid, (string)$userId);
    }
}
