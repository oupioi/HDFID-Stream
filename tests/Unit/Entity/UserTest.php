<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public function testNewUserHasCreatedAtSet(): void
    {
        $user = new User();

        $this->assertInstanceOf(\DateTimeImmutable::class, $user->getCreatedAt());
    }

    public function testIdIsNullByDefault(): void
    {
        $this->assertNull($this->user->getId());
    }

    public function testSetAndGetEmail(): void
    {
        $email = 'test@example.com';

        $result = $this->user->setEmail($email);

        $this->assertSame($this->user, $result);
        $this->assertEquals($email, $this->user->getEmail());
    }

    public function testGetUserIdentifierReturnsEmail(): void
    {
        $email = 'user@test.com';
        $this->user->setEmail($email);

        $this->assertEquals($email, $this->user->getUserIdentifier());
    }

    public function testGetUserIdentifierReturnsEmptyStringWhenNoEmail(): void
    {
        $this->assertEquals('', $this->user->getUserIdentifier());
    }

    public function testGetRolesAlwaysIncludesRoleUser(): void
    {
        $roles = $this->user->getRoles();

        $this->assertContains('ROLE_USER', $roles);
    }

    public function testSetAndGetRoles(): void
    {
        $roles = ['ROLE_ADMIN', 'ROLE_MODERATOR'];

        $result = $this->user->setRoles($roles);

        $this->assertSame($this->user, $result);
        $this->assertContains('ROLE_ADMIN', $this->user->getRoles());
        $this->assertContains('ROLE_MODERATOR', $this->user->getRoles());
        $this->assertContains('ROLE_USER', $this->user->getRoles());
    }

    public function testRolesAreUnique(): void
    {
        $this->user->setRoles(['ROLE_USER', 'ROLE_ADMIN', 'ROLE_USER']);

        $roles = $this->user->getRoles();
        $roleUserCount = array_count_values($roles)['ROLE_USER'] ?? 0;

        $this->assertEquals(1, $roleUserCount);
    }

    public function testSetAndGetPassword(): void
    {
        $password = 'hashedpassword123';

        $result = $this->user->setPassword($password);

        $this->assertSame($this->user, $result);
        $this->assertEquals($password, $this->user->getPassword());
    }

    public function testSetAndGetFirstName(): void
    {
        $firstName = 'John';

        $result = $this->user->setFirstName($firstName);

        $this->assertSame($this->user, $result);
        $this->assertEquals($firstName, $this->user->getFirstName());
    }

    public function testSetAndGetLastName(): void
    {
        $lastName = 'Doe';

        $result = $this->user->setLastName($lastName);

        $this->assertSame($this->user, $result);
        $this->assertEquals($lastName, $this->user->getLastName());
    }

    public function testSetAndGetUsername(): void
    {
        $username = 'johndoe';

        $result = $this->user->setUsername($username);

        $this->assertSame($this->user, $result);
        $this->assertEquals($username, $this->user->getUsername());
    }

    public function testUsernameCanBeNull(): void
    {
        $this->user->setUsername(null);

        $this->assertNull($this->user->getUsername());
    }

    public function testSetAndGetCreatedAt(): void
    {
        $createdAt = new \DateTimeImmutable('2024-01-01');

        $result = $this->user->setCreatedAt($createdAt);

        $this->assertSame($this->user, $result);
        $this->assertSame($createdAt, $this->user->getCreatedAt());
    }

    public function testCreatedAtCanBeNull(): void
    {
        $this->user->setCreatedAt(null);

        $this->assertNull($this->user->getCreatedAt());
    }

    public function testGetFullName(): void
    {
        $this->user->setFirstName('John');
        $this->user->setLastName('Doe');

        $this->assertEquals('John Doe', $this->user->getFullName());
    }

    public function testGetFullNameWithNullValues(): void
    {
        // With null values, getFullName might throw an error or return unexpected results
        // This test documents the current behavior
        $user = new User();

        // We need to set names since they're required
        $user->setFirstName('');
        $user->setLastName('');

        $this->assertEquals(' ', $user->getFullName());
    }

    public function testEraseCredentialsDoesNotThrow(): void
    {
        // eraseCredentials should not throw an exception
        $this->user->eraseCredentials();

        // If we get here, the test passes
        $this->assertTrue(true);
    }

    public function testFluentInterface(): void
    {
        $user = (new User())
            ->setEmail('test@test.com')
            ->setFirstName('Jane')
            ->setLastName('Smith')
            ->setUsername('janesmith')
            ->setPassword('password123')
            ->setRoles(['ROLE_ADMIN']);

        $this->assertEquals('test@test.com', $user->getEmail());
        $this->assertEquals('Jane', $user->getFirstName());
        $this->assertEquals('Smith', $user->getLastName());
        $this->assertEquals('janesmith', $user->getUsername());
        $this->assertEquals('password123', $user->getPassword());
        $this->assertContains('ROLE_ADMIN', $user->getRoles());
    }

    public function testUserImplementsUserInterface(): void
    {
        $this->assertInstanceOf(\Symfony\Component\Security\Core\User\UserInterface::class, $this->user);
    }

    public function testUserImplementsPasswordAuthenticatedUserInterface(): void
    {
        $this->assertInstanceOf(\Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface::class, $this->user);
    }
}
