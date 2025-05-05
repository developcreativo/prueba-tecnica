<?php

declare(strict_types=1);

namespace App\Domain\Event;

interface EventDispatcherInterface
{
    public function dispatch(DomainEvent $event): void;
    
    public function register(string $eventClassName, callable $eventHandler): void;
}
