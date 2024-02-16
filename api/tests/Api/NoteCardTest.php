<?php

namespace App\Tests\Api;

use ApiPlatform\Api\IriConverterInterface;
use App\Factory\DeckFactory;
use App\Factory\NoteCardFactory;
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

        $browser = $this->browser()
            ->post('/note_cards', [
                'json' => [
                    'front' => $front,
                    'back' => $back,
                    'isPublished' => $isPublished,
                ],
            ]);

        $browser->assertStatus(201);
        $browser->json()->hasSubset([
            '@context' => '/contexts/NoteCard',
            '@type' => 'NoteCard',
            'front' => $front,
            'back' => $back,
            'isPublished' => $isPublished,
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

        $browser = $this->browser()
            ->put('/note_cards/'.$noteCard->getId(), [
                'json' => [
                    'front' => $updatedFrontValue,
                    'back' => $updatedBackValue,
                    'isPublished' => $updatedIsPublished,
                ],
            ]);

        $browser->assertStatus(200);
        $browser->json()->hasSubset([
            '@context' => '/contexts/NoteCard',
            '@type' => 'NoteCard',
            'front' => $updatedFrontValue,
            'back' => $updatedBackValue,
            'isPublished' => $updatedIsPublished,
        ]);
    }

    public function testUpdateNoteCardPartially(): void
    {
        $noteCard = NoteCardFactory::new()->create();
        $previousIsPublished = $noteCard->getIsPublished();
        $previousBackValue = $noteCard->getBack();
        $updatedFrontValue = Factory::create()->sentence();

        $this->assertFalse($noteCard->getFront() === $updatedFrontValue);

        $browser = $this->browser()
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
        ]);
    }

    public function testAddNoteCardToDeck(): void
    {
        $iriConverter = self::getContainer()->get('api_platform.iri_converter');
        \assert($iriConverter instanceof IriConverterInterface);

        $noteCard = NoteCardFactory::new()->create();
        $deck = DeckFactory::new()->create()->object();
        $deckIRI = $iriConverter->getIriFromResource($deck);

        $browser = $this->browser()
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
        ]);
    }

    public function testRemoveNoteCardFromDeck(): void
    {
        $deck = DeckFactory::new()->create();
        $noteCard = $deck->getCards()[0];

        $browser = $this->browser()
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
            'decks' => [],
        ]);
    }

    public function testDeleteNoteCard(): void
    {
        $noteCard = NoteCardFactory::new()->create();
        $id = $noteCard->getId();

        $browser = $this->browser()
            ->delete('/note_cards/'.$id);
        $browser->assertStatus(204);

        $browser = $this->browser()
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
        NoteCardFactory::new()->create(['isPublished' => true]);
        NoteCardFactory::new()->create(['isPublished' => false]);

        $browser = $this->browser()
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
        $front = Factory::create()->text(255);
        NoteCardFactory::new()->create(['front' => $front]);
        NoteCardFactory::new()->create(['front' => Factory::create()->text(255)]);

        $browser = $this->browser()
            ->get('/note_cards', [
                'query' => [
                    'front' => $front,
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
                    'front' => $front,
                ],
            ],
        ]);
    }

    public function testBackFilter(): void
    {
        $back = Factory::create()->text();
        NoteCardFactory::new()->create(['back' => $back]);
        NoteCardFactory::new()->create(['back' => Factory::create()->text()]);
        $browser = $this->browser()
            ->get('/note_cards', [
                'query' => [
                    'back' => $back,
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
                    'back' => $back,
                ],
            ],
        ]);
    }

    public function testPropertyFilter(): void
    {
        $front = Factory::create()->text(255);
        $back = Factory::create()->text();
        NoteCardFactory::new()->create(['front' => $front, 'back' => $back]);

        $browser = $this->browser()
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
                        'front' => $front,
                    ],
                ],
            ])
            ->assertMissing('"hydra:member"[0].back');
    }
}
