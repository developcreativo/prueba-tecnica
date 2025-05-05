<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\WeakPasswordException;

final class Password
{
    private const MIN_LENGTH = 8;
    private const PATTERN = '/^(?=.*[A-Z])(?=.*[0-9])(?=.*[^a-zA-Z0-9]).+$/';

    private string $hashedValue;

    private function __construct(string $hashedValue)
    {
        $this->hashedValue = $hashedValue;
    }

    public static function fromPlainPassword(string $plainPassword): self
    {
        self::validate($plainPassword);
        
        // Hash the password using PHP's built-in password_hash function
        $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        
        if ($hashedPassword === false) {
            throw new \RuntimeException('Failed to hash password');
        }
        
        return new self($hashedPassword);
    }

    public static function fromHash(string $hashedPassword): self
    {
        return new self($hashedPassword);
    }

    private static function validate(string $plainPassword): void
    {
        if (strlen($plainPassword) < self::MIN_LENGTH) {
            throw new WeakPasswordException(
                sprintf('Password must be at least %d characters long', self::MIN_LENGTH)
            );
        }

        if (!preg_match(self::PATTERN, $plainPassword)) {
            throw new WeakPasswordException(
                'Password must contain at least one uppercase letter, one number, and one special character'
            );
        }
    }

    public function verify(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->hashedValue);
    }

    public function value(): string
    {
        return $this->hashedValue;
    }

    public function __toString(): string
    {
        return $this->hashedValue;
    }
}
