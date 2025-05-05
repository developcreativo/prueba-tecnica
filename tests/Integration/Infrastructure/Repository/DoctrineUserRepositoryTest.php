<?php

declare(strict_types=1);

namespace Tests\Integration\Infrastructure\Repository;

use App\Domain\Entity\User;
use App\Domain\ValueObject\Email;
use App\Domain\ValueObject\Name;
use App\Domain\ValueObject\Password;
use App\Domain\ValueObject\UserId;
use App\Infrastructure\Repository\DoctrineUserRepository;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\TestCase;

class DoctrineUserRepositoryTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private DoctrineUserRepository $repository;

    protected function setUp(): void
    {
        // Setup connection configuration
        $connection = DriverManager::getConnection([
            'driver' => 'pdo_mysql',
            'host' => 'mysql',  // Use Docker service name
            'port' => 3306,
            'dbname' => 'app_db',
            'user' => 'user',
            'password' => 'password',
            'charset' => 'utf8mb4'
        ]);

        // Create configuration for Doctrine
        $config = ORMSetup::createAttributeMetadataConfiguration(
            [__DIR__ . '/../../../../src'],
            true
        );

        // Get the entity manager
        $this->entityManager = new EntityManager($connection, $config);
        
        // Create the schema for the User entity
        $this->createSchema();
        
        // Create the repository
        $this->repository = new DoctrineUserRepository($this->entityManager);
        
        // Ensure clean database state
        $this->truncateUserTable();
    }

    protected function tearDown(): void
    {
        // Clean up database state
        $this->truncateUserTable();
        
        // Close the EntityManager
        $this->entityManager->close();
    }

    private function createSchema(): void
    {
        // Create a schema tool
        $schemaTool = new SchemaTool($this->entityManager);
        
        // Create the database schema for the User entity
        $classes = [
            $this->entityManager->getClassMetadata(User::class)
        ];
        
        try {
            $schemaTool->dropSchema($classes);
            $schemaTool->createSchema($classes);
        } catch (\Exception $e) {
            // Handle any exceptions that might occur
            echo "An error occurred while creating the schema: " . $e->getMessage() . "\n";
        }
    }

    private function truncateUserTable(): void
    {
        $connection = $this->entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();
        
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');
        $connection->executeStatement($platform->getTruncateTableSQL('users'));
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function testSaveAndFindById(): void
    {
        // Create a user
        $userId = UserId::generate();
        $user = User::create(
            $userId,
            Name::fromString('John Doe'),
            Email::fromString('john.doe@example.com'),
            Password::fromPlainPassword('StrongP@ss123')
        );
        
        // Save the user
        $this->repository->save($user);
        
        // Find the user by ID
        $foundUser = $this->repository->findById($userId);
        
        // Assert the user was found
        $this->assertNotNull($foundUser);
        $this->assertTrue($userId->equals($foundUser->id()));
        $this->assertEquals('John Doe', $foundUser->name()->value());
        $this->assertEquals('john.doe@example.com', $foundUser->email()->value());
    }
    
    public function testFindByEmail(): void
    {
        // Create a user
        $userId = UserId::generate();
        $email = Email::fromString('jane.doe@example.com');
        $user = User::create(
            $userId,
            Name::fromString('Jane Doe'),
            $email,
            Password::fromPlainPassword('StrongP@ss123')
        );
        
        // Save the user
        $this->repository->save($user);
        
        // Find the user by email
        $foundUser = $this->repository->findByEmail($email);
        
        // Assert the user was found
        $this->assertNotNull($foundUser);
        $this->assertEquals('Jane Doe', $foundUser->name()->value());
        $this->assertEquals('jane.doe@example.com', $foundUser->email()->value());
    }
    
    public function testDelete(): void
    {
        // Create a user
        $userId = UserId::generate();
        $user = User::create(
            $userId,
            Name::fromString('Delete Test'),
            Email::fromString('delete.test@example.com'),
            Password::fromPlainPassword('StrongP@ss123')
        );
        
        // Save the user
        $this->repository->save($user);
        
        // Verify the user exists
        $foundUser = $this->repository->findById($userId);
        $this->assertNotNull($foundUser);
        
        // Delete the user
        $this->repository->delete($userId);
        
        // Verify the user no longer exists
        $deletedUser = $this->repository->findById($userId);
        $this->assertNull($deletedUser);
    }
}
