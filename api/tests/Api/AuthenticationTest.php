<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class AuthenticationTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function testLogin(): void
    {
        $user = UserFactory::new()->create()->object();
        $client = static::createClient();
        $response = $client->request('POST', '/auth', [
            'json' => [
                'email' => $user->getEmail(),
                'password' => $user->getPlainPassword(),
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $json = $response->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);

        static::createClient()->request('GET', '/users/'.$user->getId());
        $this->assertResponseStatusCodeSame(401);

        static::createClient()->request('GET', '/users/'.$user->getId(), ['auth_bearer' => $json['token']]);
        $this->assertResponseIsSuccessful();
    }
}
