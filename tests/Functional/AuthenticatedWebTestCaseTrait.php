<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

trait AuthenticatedWebTestCaseTrait
{
    protected static function createAuthenticatedClient(string $email = 'test@example.com', string $password = 'password123'): KernelBrowser
    {
        $client = static::createClient();

        static::createSchema();

        $user = static::createTestUser($email, $password);

        $client->loginUser($user);

        return $client;
    }

    protected static function createSchema(): void
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $metadatas = $entityManager->getMetadataFactory()->getAllMetadata();

        if (!empty($metadatas)) {
            $schemaTool = new SchemaTool($entityManager);
            $schemaTool->dropSchema($metadatas);
            $schemaTool->createSchema($metadatas);
        }
    }

    protected static function createTestUser(string $email = 'test@example.com', string $password = 'password123'): User
    {
        $container = static::getContainer();
        $entityManager = $container->get(EntityManagerInterface::class);
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $user = new User();
        $user->setEmail($email);
        $user->setFirstName('Test');
        $user->setLastName('User');
        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }
}
