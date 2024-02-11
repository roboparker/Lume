<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\DeckFactory;
use App\Factory\UserFactory;
use Faker\Factory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class DeckTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function testCreateDeck(): void
    {
        $title = Factory::create()->text(255);
        $description = Factory::create()->text();
        $client = static::createClient();
        $client->loginUser(UserFactory::new()->create()->object());

        $client->request('POST', '/decks', [
            'json' => [
                'title' => $title,
                'description' => $description,
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            '@context' => '/contexts/Deck',
            '@type' => 'Deck',
            'title' => $title,
            'description' => $description,
        ]);
    }

    public function testReadDeck(): void
    {
        $deck = DeckFactory::new()->create();
        $client = static::createClient();
        $client->loginUser(UserFactory::new()->create()->object());

        $client->request('GET', '/decks/'.$deck->getId());

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '@context' => '/contexts/Deck',
            '@type' => 'Deck',
            'title' => $deck->getTitle(),
            'description' => $deck->getDescription(),
        ]);
    }

    public function testUpdateDeck(): void
    {
        $deck = DeckFactory::new()->create();
        $client = static::createClient();
        $client->loginUser(UserFactory::new()->create()->object());

        $client->request('PUT', '/decks/'.$deck->getId(), [
            'json' => [
                'title' => $title = Factory::create()->text(255),
                'description' => $description = Factory::create()->text(),
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '@context' => '/contexts/Deck',
            '@type' => 'Deck',
            'title' => $title,
            'description' => $description,
        ]);
    }

    public function testUpdateDeckPartially(): void
    {
        $deck = DeckFactory::new()->create();
        $client = static::createClient();
        $client->loginUser(UserFactory::new()->create()->object());

        $client->request('PATCH', '/decks/'.$deck->getId(), [
            'json' => [
                'title' => $title = Factory::create()->text(255),
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '@context' => '/contexts/Deck',
            '@type' => 'Deck',
            'title' => $title,
            'description' => $deck->getDescription(),
        ]);
    }

    public function testDeleteDeck(): void
    {
        $deck = DeckFactory::new()->create();
        $client = static::createClient();
        $client->loginUser(UserFactory::new()->create()->object());

        $client->request('DELETE', '/decks/'.$deck->getId());

        $this->assertResponseStatusCodeSame(204);
    }
}
