<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\UserId;
use App\Domain\ValueObject\Email;
use App\Domain\ValueObject\Name;
use App\Domain\ValueObject\Password;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
final class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private UserId $id;

    #[ORM\Column(type: 'string')]
    private Name $name;

    #[ORM\Column(type: 'string', unique: true)]
    private Email $email;

    #[ORM\Column(type: 'string')]
    private Password $password;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    private function __construct(
        UserId $id,
        Name $name,
        Email $email,
        Password $password,
        DateTimeImmutable $createdAt
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->createdAt = $createdAt;
    }

    public static function create(
        UserId $id,
        Name $name,
        Email $email,
        Password $password
    ): self {
        return new self(
            $id,
            $name,
            $email,
            $password,
            new DateTimeImmutable()
        );
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function password(): Password
    {
        return $this->password;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
