<?php

namespace App\Tests\Api;

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
        $email = Factory::create()->email();

        $browser = $this->browser()
            ->post('/users', [
                'json' => [
                    'email' => $email,
                    'plainPassword' => Factory::create()->password(),
                ],
            ]);

        $browser->assertStatus(201);
        $browser->json()->hasSubset([
            '@context' => '/contexts/User',
            '@type' => 'User',
            'email' => $email,
        ]);
    }

    public function testReadUser(): void
    {
        $user = UserFactory::new()->create();

        $browser = $this->browser()
            ->get('/users/'.$user->getId());

        $browser->assertStatus(200);
        $browser->json()->hasSubset([
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

        $browser = $this->browser()
            ->put('/users/'.$user->getId(), [
                'json' => [
                    'email' => $updatedEmail,
                    'plainPassword' => $updatedPassword,
                ],
            ]);

        $browser->assertStatus(200);
        $browser->json()->hasSubset([
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

        $browser = $this->browser()
            ->patch('/users/'.$user->getId(), [
                'json' => [
                    'email' => $updatedEmail,
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                ],
            ]);

        $browser->assertStatus(200);
        $browser->json()->hasSubset([
            '@context' => '/contexts/User',
            '@type' => 'User',
            'email' => $updatedEmail,
        ]);
    }

    public function testDeleteUser(): void
    {
        $user = UserFactory::new()->create();
        $id = $user->getId();

        $browser = $this->browser()
            ->delete('/users/'. $id);
        $browser->assertStatus(204);

        $browser = $this->browser()
            ->get('/users/'. $id);
        $browser->assertStatus(404);
    }

    public function testNotFound(): void
    {
        $browser = $this->browser()
            ->get('/users/1');
        $browser->assertStatus(404);
    }
}
