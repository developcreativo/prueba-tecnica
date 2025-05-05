<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

final class Name
{
    private const MIN_LENGTH = 2;
    private const MAX_LENGTH = 50;
    private const PATTERN = '/^[a-zA-Z\s]+$/';

    private string $value;

    private function __construct(string $name)
    {
        $this->value = $name;
    }

    public static function fromString(string $name): self
    {
        self::validate($name);
        return new self($name);
    }

    private static function validate(string $name): void
    {
        if (strlen($name) < self::MIN_LENGTH) {
            throw new \InvalidArgumentException(
                sprintf('Name must be at least %d characters long', self::MIN_LENGTH)
            );
        }

        if (strlen($name) > self::MAX_LENGTH) {
            throw new \InvalidArgumentException(
                sprintf('Name cannot exceed %d characters', self::MAX_LENGTH)
            );
        }

        if (!preg_match(self::PATTERN, $name)) {
            throw new \InvalidArgumentException('Name can only contain letters and spaces');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(Name $other): bool
    {
        return $this->value === $other->value();
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
