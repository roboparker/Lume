<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGetId(): void
    {
        $user = new User();
        $this->assertNull($user->getId());
    }

    public function testGetEmail(): void
    {
        $user = new User();
        $email = 'lume@test.com';
        $user->setEmail($email);
        $this->assertSame($email, $user->getEmail());
    }

    public function testGetPassword(): void
    {
        $user = new User();
        $password = 'password';
        $user->setPassword($password);
        $this->assertSame($password, $user->getPassword());
    }

    public function testGetPlainPassword(): void
    {
        $user = new User();
        $plainPassword = 'password';
        $user->setPlainPassword($plainPassword);
        $this->assertSame($plainPassword, $user->getPlainPassword());
    }

    public function testGetRoles(): void
    {
        $user = new User();
        $roles = ['ROLE_USER'];
        $user->setRoles($roles);
        $this->assertSame($roles, $user->getRoles());
    }

    public function testGetUserIdentifier(): void
    {
        $user = new User();
        $email = 'lume@test.com';
        $user->setEmail($email);
        $this->assertSame($email, $user->getUserIdentifier());
    }

    public function testEraseCredentials(): void
    {
        $user = new User();
        $plainPassword = 'password';
        $user->setPlainPassword($plainPassword);
        $user->eraseCredentials();
        $this->assertNull($user->getPlainPassword());
        $user->eraseCredentials();
        $this->assertNull($user->getPlainPassword());
    }
}
