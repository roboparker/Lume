<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\UserFactory;
use Faker\Factory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function testCreateUser(): void
    {
        static::createClient()->request('POST', '/users', [
            'json' => [
                'email' => 'user@test.com',
                'plainPassword' => 'password',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            '@context' => '/contexts/User',
            '@type' => 'User',
            'email' => 'user@test.com',
        ]);
    }

    public function testReadUser(): void
    {
        $user = UserFactory::new()->create();

        static::createClient()->request('GET', '/users/'.$user->getId());

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '@context' => '/contexts/User',
            '@type' => 'User',
            'email' => $user->getEmail(),
        ]);
    }

    public function testUpdateUser(): void
    {
        $user = UserFactory::new()->create();
        $updatedEmail = Factory::create()->email();
        $updatedPassword = Factory::create()->password();

        $this->assertFalse($user->getEmail() === $updatedEmail);

        static::createClient()->request('PUT', '/users/'.$user->getId(), [
            'json' => [
                'email' => $updatedEmail,
                'plainPassword' => $updatedPassword,
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '@context' => '/contexts/User',
            '@type' => 'User',
            'email' => $updatedEmail,
        ]);
    }

    public function testUpdateUserPartially(): void
    {
        $user = UserFactory::new()->create();
        $updatedEmail = Factory::create()->email();

        $this->assertFalse($user->getEmail() === $updatedEmail);

        static::createClient()->request('PATCH', '/users/'.$user->getId(), [
            'json' => [
                'email' => $updatedEmail,
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '@context' => '/contexts/User',
            '@type' => 'User',
            'email' => $updatedEmail,
        ]);
    }

    public function testDeleteUser(): void
    {
        $user = UserFactory::new()->create();

        static::createClient()->request('DELETE', '/users/'.$user->getId());

        $this->assertResponseStatusCodeSame(204);
    }
}
