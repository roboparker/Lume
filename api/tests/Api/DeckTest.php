<?php

namespace App\Tests\Api;

use ApiPlatform\Api\IriConverterInterface;
use App\Factory\DeckFactory;
use App\Factory\NoteCardFactory;
use App\Factory\UserFactory;
use Faker\Factory;

final class DeckTest extends ApiTestCase
{
    public function testCreateDeck(): void
    {
        $title = Factory::create()->text(255);
        $description = Factory::create()->text();
        $isPublished = true;

        $browser = $this->browser(user: $user = UserFactory::new()->create()->object())
            ->post('/decks', [
                'json' => [
                    'title' => $title,
                    'description' => $description,
                    'isPublished' => $isPublished,
                    'ownedBy' => sprintf('/users/%s', $user->getId()),
                ],
            ]);

        $browser->assertStatus(201);
        $browser->json()->hasSubset([
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

        $browser = $this->browser()
            ->get('/decks/'.$deck->getId());

        $browser->assertStatus(200);
        $browser->json()->hasSubset([
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

        $browser = $this->browser(user: $user = $deck->getOwnedBy())
            ->put('/decks/'.$deck->getId(), [
                'json' => [
                    'title' => $title = Factory::create()->text(255),
                    'description' => $description = Factory::create()->text(),
                    'isPublished' => $isPublished = !$deck->getIsPublished(),
                    'ownedBy' => sprintf('/users/%s', $user->getId()),
                ],
            ]);

        $browser->assertStatus(200);
        $browser->json()->hasSubset([
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

        $browser = $this->browser(user: $user = $deck->getOwnedBy())
            ->patch('/decks/'.$deck->getId(), [
                'json' => [
                    'title' => $title = Factory::create()->text(255),
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                ],
            ]);

        $browser->assertStatus(200);
        $browser->json()->hasSubset([
            '@context' => '/contexts/Deck',
            '@type' => 'Deck',
            'title' => $title,
            'description' => $deck->getDescription(),
            'isPublished' => $deck->getIsPublished(),
            'ownedBy' => sprintf('/users/%s', $user->getId()),
        ]);
    }

    public function testDeleteDeck(): void
    {
        $deck = DeckFactory::new()->create();
        $id = $deck->getId();

        $browser = $this->browser(user:  $deck->getOwnedBy())
            ->delete('/decks/'.$id);

        $browser->assertStatus(204);

        $browser = $this->browser()
            ->get('/decks/'.$id);

        $browser->assertStatus(404);
    }

    public function testAddCardToDeck(): void
    {
        $iriConverter = self::getContainer()->get('api_platform.iri_converter');
        \assert($iriConverter instanceof IriConverterInterface);

        $deck = DeckFactory::new()->create();
        $noteCard = NoteCardFactory::new()->create()->object();
        $noteCardIRI = $iriConverter->getIriFromResource($noteCard);

        $browser = $this->browser(user: $deck->getOwnedBy())
            ->patch('/decks/'.$deck->getId(), [
                'json' => [
                    'cards' => [
                        $noteCardIRI,
                    ],
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                ],
            ]);

        $browser->assertStatus(200);
        $browser->json()->hasSubset([
            '@context' => '/contexts/Deck',
            '@type' => 'Deck',
            'cards' => [$noteCardIRI],
        ]);
    }

    public function testRemoveCardFromDeck(): void
    {
        $deck = DeckFactory::new()->create();

        $browser = $this->browser(user: $user = $deck->getOwnedBy())
            ->put('/decks/'.$deck->getId(), [
                'json' => [
                    'title' => $deck->getTitle(),
                    'description' => $deck->getDescription(),
                    'cards' => [],
                    'ownedBy' => sprintf('/users/%s', $user->getId()),
                ],
            ]);

        $browser->assertStatus(200);
        $browser->json()->hasSubset([
            '@context' => '/contexts/Deck',
            '@type' => 'Deck',
            'cards' => [],
            'title' => $deck->getTitle(),
            'description' => $deck->getDescription(),
        ]);
    }

    public function testIsPublishedFilter(): void
    {
        DeckFactory::new()->create(['isPublished' => true]);
        DeckFactory::new()->create(['isPublished' => false]);

        $browser = $this->browser()
            ->get('/decks', [
                'query' => [
                    'isPublished' => true,
                ],
            ]);

        $browser->assertStatus(200);
        $browser->json()->hasSubset([
            '@context' => '/contexts/Deck',
            '@id' => '/decks',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 1,
            'hydra:member' => [
                [
                    'isPublished' => true,
                ],
            ],
        ]);
    }

    public function testTitleFilter(): void
    {
        $title = Factory::create()->text(255);
        DeckFactory::new()->create(['title' => $title]);
        DeckFactory::new()->create(['title' => Factory::create()->text(255)]);

        $browser = $this->browser()
            ->get('/decks', [
                'query' => [
                    'title' => $title,
                ],
            ]);

        $browser->assertStatus(200);
        $browser->json()->hasSubset([
            '@context' => '/contexts/Deck',
            '@id' => '/decks',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 1,
            'hydra:member' => [
                [
                    'title' => $title,
                ],
            ],
        ]);
    }

    public function testDescriptionFilter(): void
    {
        $description = Factory::create()->text();
        DeckFactory::new()->create(['description' => $description]);
        DeckFactory::new()->create(['description' => Factory::create()->text()]);

        $browser = $this->browser()
            ->get('/decks', [
                'query' => [
                    'description' => $description,
                ],
            ]);

        $browser->assertStatus(200);
        $browser->json()->hasSubset([
            '@context' => '/contexts/Deck',
            '@id' => '/decks',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 1,
            'hydra:member' => [
                [
                    'description' => $description,
                ],
            ],
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

        $browser = $this->browser()
            ->get('/decks', [
                'query' => [
                    'properties' => ['title'],
                ],
            ]);

        $browser->assertStatus(200);
        $browser->json()
            ->assertMatches('"hydra:member"[0].title', $title)
            ->assertMissing('"hydra:member"[0].description');
    }
}
