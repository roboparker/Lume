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
        $isPublished = true;
        $client = static::createClient();
        $client->loginUser(UserFactory::new()->create()->object());

        $client->request('POST', '/decks', [
            'json' => [
                'title' => $title,
                'description' => $description,
                'isPublished' => $isPublished,
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
            'isPublished' => $isPublished,
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
            'isPublished' => $deck->getIsPublished(),
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
                'isPublished' => $isPublished = !$deck->getIsPublished(),
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
            'isPublished' => $isPublished,
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

    public function testIsPublishedFilter(): void
    {
        DeckFactory::new()->create(['isPublished' => true]);
        DeckFactory::new()->create(['isPublished' => false]);
        $client = static::createClient();
        $client->loginUser(UserFactory::new()->create()->object());

        $client->request('GET', '/decks', [
            'query' => [
                'isPublished' => true,
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '@context' => '/contexts/Deck',
            '@id' => '/decks',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 1,
        ]);
    }

    public function testTitleFilter(): void
    {
        $title = Factory::create()->text(255);
        DeckFactory::new()->create(['title' => $title]);
        DeckFactory::new()->create(['title' => Factory::create()->text(255)]);
        $client = static::createClient();
        $client->loginUser(UserFactory::new()->create()->object());

        $client->request('GET', '/decks', [
            'query' => [
                'title' => $title,
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '@context' => '/contexts/Deck',
            '@id' => '/decks',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 1,
        ]);
    }

    public function testDescriptionFilter(): void
    {
        $description = Factory::create()->text();
        DeckFactory::new()->create(['description' => $description]);
        DeckFactory::new()->create(['description' => Factory::create()->text()]);
        $client = static::createClient();
        $client->loginUser(UserFactory::new()->create()->object());

        $client->request('GET', '/decks', [
            'query' => [
                'description' => $description,
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '@context' => '/contexts/Deck',
            '@id' => '/decks',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 1,
        ]);
    }

    public function testPropertiesFilter(): void
    {
        $title = Factory::create()->text(255);
        $description = Factory::create()->text();
        $isPublished = true;
        DeckFactory::new()->create([
            'title' => $title,
            'description' => $description,
            'isPublished' => $isPublished,
        ]);
        $client = static::createClient();
        $client->loginUser(UserFactory::new()->create()->object());

        $client->request('GET', '/decks', [
            'query' => [
                'properties' => ['title'],
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '@context' => '/contexts/Deck',
            '@id' => '/decks',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 1,
        ]);


        $this->assertArrayNotHasKey('title', $client->getResponse()->toArray(false));
    }
}
