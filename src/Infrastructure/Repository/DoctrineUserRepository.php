<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\ValueObject\Email;
use App\Domain\ValueObject\UserId;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineUserRepository implements UserRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function findById(UserId $id): ?User
    {
        return $this->entityManager->getRepository(User::class)->find($id->value());
    }

    public function delete(UserId $id): void
    {
        $user = $this->findById($id);
        
        if ($user !== null) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        }
    }

    public function findByEmail(Email $email): ?User
    {
        // Since we're using a value object for email, we need to use a custom query
        // to fetch a user by the email value
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.email = :email')
            ->setParameter('email', (string) $email);
        
        $query = $queryBuilder->getQuery();
        
        return $query->getOneOrNullResult();
    }
}
