<?php

namespace App\Tests\Api;

use ApiPlatform\Api\IriConverterInterface;
use App\Factory\DeckFactory;
use App\Factory\NoteCardFactory;
use App\Factory\UserFactory;
use Faker\Factory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class NoteCardTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function testCreateNoteCard(): void
    {
        $front = Factory::create()->sentence();
        $back = Factory::create()->sentence();
        $isPublished = true;

        $browser = $this->browser(user: $user = UserFactory::new()->create()->object())
            ->post('/note_cards', [
                'json' => [
                    'front' => $front,
                    'back' => $back,
                    'isPublished' => $isPublished,
                    'ownedBy' => sprintf('/users/%s', $user->getId()),
                ],
            ]);

        $browser->assertStatus(201);
        $browser->json()->hasSubset([
            '@context' => '/contexts/NoteCard',
            '@type' => 'NoteCard',
            'front' => $front,
            'back' => $back,
            'isPublished' => $isPublished,
            'ownedBy' => sprintf('/users/%s', $user->getId()),
        ]);
    }

    public function testReadNoteCard(): void
    {
        $noteCard = NoteCardFactory::new()->create();

        $browser = $this->browser()
            ->get('/note_cards/'.$noteCard->getId());

        $browser->assertStatus(200);
        $browser->json()->hasSubset([
            '@context' => '/contexts/NoteCard',
            '@type' => 'NoteCard',
            'front' => $noteCard->getFront(),
            'back' => $noteCard->getBack(),
            'isPublished' => $noteCard->getIsPublished(),
            'ownedBy' => '/users/'.$noteCard->getOwnedBy()->getId(),
        ]);
    }

    public function testUpdateNoteCard(): void
    {
        $noteCard = NoteCardFactory::new()->create();
        $updatedFrontValue = Factory::create()->sentence();
        $updatedBackValue = Factory::create()->sentence();
        $updatedIsPublished = !$noteCard->getIsPublished();

        $this->assertFalse($noteCard->getFront() === $updatedFrontValue);
        $this->assertFalse($noteCard->getBack() === $updatedBackValue);
        $this->assertFalse($noteCard->getIsPublished() === $updatedIsPublished);

        $browser = $this->browser(user: $user = $noteCard->getOwnedBy())
            ->put('/note_cards/'.$noteCard->getId(), [
                'json' => [
                    'front' => $updatedFrontValue,
                    'back' => $updatedBackValue,
                    'isPublished' => $updatedIsPublished,
                    'ownedBy' => sprintf('/users/%s', $user->getId()),
                ],
            ]);

        $browser->assertStatus(200);
        $browser->json()->hasSubset([
            '@context' => '/contexts/NoteCard',
            '@type' => 'NoteCard',
            'front' => $updatedFrontValue,
            'back' => $updatedBackValue,
            'isPublished' => $updatedIsPublished,
            'ownedBy' => sprintf('/users/%s', $user->getId()),
        ]);
    }

    public function testUpdateNoteCardPartially(): void
    {
        $noteCard = NoteCardFactory::new()->create();
        $previousIsPublished = $noteCard->getIsPublished();
        $previousBackValue = $noteCard->getBack();
        $updatedFrontValue = Factory::create()->sentence();

        $this->assertFalse($noteCard->getFront() === $updatedFrontValue);

        $browser = $this->browser(user: $user = $noteCard->getOwnedBy())
            ->patch('/note_cards/'.$noteCard->getId(), [
                'json' => [
                    'front' => $updatedFrontValue,
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                ],
            ]);

        $browser->assertStatus(200);
        $browser->json()->hasSubset([
            '@context' => '/contexts/NoteCard',
            '@type' => 'NoteCard',
            'front' => $updatedFrontValue,
            'back' => $previousBackValue,
            'isPublished' => $previousIsPublished,
            'ownedBy' => sprintf('/users/%s', $user->getId()),
        ]);
    }

    public function testAddNoteCardToDeck(): void
    {
        $iriConverter = self::getContainer()->get('api_platform.iri_converter');
        \assert($iriConverter instanceof IriConverterInterface);

        $noteCard = NoteCardFactory::new()->create();
        $deck = DeckFactory::new()->create()->object();
        $deckIRI = $iriConverter->getIriFromResource($deck);

        $browser = $this->browser(user: $user = $noteCard->getOwnedBy())
            ->patch('/note_cards/'.$noteCard->getId(), [
                'json' => [
                    'decks' => [$deckIRI],
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                ],
            ]);

        $browser->assertStatus(200);
        $browser->json()->hasSubset([
            '@context' => '/contexts/NoteCard',
            '@type' => 'NoteCard',
            'front' => $noteCard->getFront(),
            'back' => $noteCard->getBack(),
            'isPublished' => $noteCard->getIsPublished(),
            'ownedBy' => sprintf('/users/%s', $user->getId()),
        ]);
    }

    public function testRemoveNoteCardFromDeck(): void
    {
        $deck = DeckFactory::new()->create();
        $noteCard = $deck->getCards()[0];

        $browser = $this->browser(user: $user = $noteCard->getOwnedBy())
            ->patch('/note_cards/'.$noteCard->getId(), [
                'json' => [
                    'decks' => [],
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                ],
            ]);

        $browser->assertStatus(200);
        $browser->json()->hasSubset([
            '@context' => '/contexts/NoteCard',
            '@type' => 'NoteCard',
            'front' => $noteCard->getFront(),
            'back' => $noteCard->getBack(),
            'isPublished' => $noteCard->getIsPublished(),
            'ownedBy' => sprintf('/users/%s', $user->getId()),
            'decks' => [],
        ]);
    }

    public function testDeleteNoteCard(): void
    {
        $noteCard = NoteCardFactory::new()->create();
        $id = $noteCard->getId();

        $browser = $this->browser(user: $user = $noteCard->getOwnedBy())
            ->delete('/note_cards/'.$id);
        $browser->assertStatus(204);

        $browser = $this->browser(user: $user)
            ->get('/note_cards/'.$id);
        $browser->assertStatus(404);
    }

    public function testNotFoundNoteCard(): void
    {
        $browser = $this->browser()
            ->get('/note_cards/1');

        $browser->assertStatus(404);
    }

    public function testIsPublishedFilter(): void
    {
        $user = UserFactory::new()->create()->object();
        NoteCardFactory::new()->create(['isPublished' => true, 'ownedBy' => $user]);
        NoteCardFactory::new()->create(['isPublished' => false, 'ownedBy' => $user]);

        $browser = $this->browser(user: $user)
            ->get('/note_cards', [
                'query' => [
                    'isPublished' => true,
                ],
            ]);

        $browser->assertStatus(200);
        $browser->json()->hasSubset([
            '@context' => '/contexts/NoteCard',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 1,
            'hydra:member' => [
                [
                    '@type' => 'NoteCard',
                    'isPublished' => true,
                ],
            ],
        ]);
    }

    public function testFrontFilter(): void
    {
        $user = UserFactory::new()->create()->object();
        $filteredCard = NoteCardFactory::new()->create(['ownedBy' => $user]);
        NoteCardFactory::new()->create(['ownedBy' => $user]);

        $browser = $this->browser()
            ->get('/note_cards', [
                'query' => [
                    'front' => $filteredCard->getFront(),
                ],
            ]);

        $browser->assertStatus(200);
        $browser->json()->hasSubset([
            '@context' => '/contexts/NoteCard',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 1,
            'hydra:member' => [
                [
                    '@type' => 'NoteCard',
                    'front' => $filteredCard->getFront(),
                ],
            ],
        ]);
    }

    public function testBackFilter(): void
    {
        $user = UserFactory::new()->create()->object();
        $filteredCard = NoteCardFactory::new()->create(['ownedBy' => $user]);
        NoteCardFactory::new()->create(['ownedBy' => $user]);
        $browser = $this->browser()
            ->get('/note_cards', [
                'query' => [
                    'back' => $filteredCard->getBack(),
                ],
            ]);

        $browser->assertStatus(200);
        $browser->json()->hasSubset([
            '@context' => '/contexts/NoteCard',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 1,
            'hydra:member' => [
                [
                    '@type' => 'NoteCard',
                    'back' => $filteredCard->getBack(),
                ],
            ],
        ]);
    }

    public function testPropertyFilter(): void
    {
        $card = NoteCardFactory::new()->create();

        $browser = $this->browser(user: $user = $card->getOwnedBy())
            ->get('/note_cards', [
                'query' => [
                    'properties' => ['front'],
                ],
            ]);

        $browser->assertStatus(200);
        $browser->json()
            ->hasSubset([
                '@context' => '/contexts/NoteCard',
                '@type' => 'hydra:Collection',
                'hydra:totalItems' => 1,
                'hydra:member' => [
                    [
                        'front' => $card->getFront(),
                    ],
                ],
            ])
            ->assertMissing('"hydra:member"[0].back');
    }
}
