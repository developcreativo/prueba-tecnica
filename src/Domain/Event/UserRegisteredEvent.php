<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Domain\Entity\User;

final class UserRegisteredEvent implements DomainEvent
{
    private User $user;
    private \DateTimeImmutable $occurredOn;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function user(): User
    {
        return $this->user;
    }

    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
