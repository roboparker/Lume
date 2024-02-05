<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\NoteCardFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class NoteCardTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function testCreateNoteCard(): void
    {
        static::createClient()->request('POST', '/note_cards', [
            'json' => [
                'front' => 'Question',
                'back' => 'Answer',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            '@context' => '/contexts/NoteCard',
            '@type' => 'NoteCard',
            'front' => 'Question',
            'back' => 'Answer',
        ]);
    }

    public function testReadNoteCard(): void
    {
        $noteCard = NoteCardFactory::new()->create();

        static::createClient()->request('GET', '/note_cards/'.$noteCard->getId());

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '@context' => '/contexts/NoteCard',
            '@type' => 'NoteCard',
            'front' => $noteCard->getFront(),
            'back' => $noteCard->getBack(),
        ]);
    }

    public function testUpdateNoteCard(): void
    {
        $noteCard = NoteCardFactory::new()->create();
        $updatedFrontValue = 'Question';
        $updatedBackValue = 'Answer';

        $this->assertFalse($noteCard->getFront() === $updatedFrontValue);
        $this->assertFalse($noteCard->getBack() === $updatedBackValue);

        static::createClient()->request('PUT', '/note_cards/'.$noteCard->getId(), [
            'json' => [
                'front' => $updatedFrontValue,
                'back' => $updatedBackValue,
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '@context' => '/contexts/NoteCard',
            '@type' => 'NoteCard',
            'front' => $updatedFrontValue,
            'back' => $updatedBackValue,
        ]);
    }

    public function testUpdateNoteCardPartially(): void
    {
        $noteCard = NoteCardFactory::new()->create();
        $previousBackValue = $noteCard->getBack();
        $updatedFrontValue = 'Question';

        $this->assertFalse($noteCard->getFront() === $updatedFrontValue);

        static::createClient()->request('PATCH', '/note_cards/'.$noteCard->getId(), [
            'json' => [
                'front' => $updatedFrontValue,
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '@context' => '/contexts/NoteCard',
            '@type' => 'NoteCard',
            'front' => $updatedFrontValue,
            'back' => $previousBackValue,
        ]);
    }

    public function testDeleteNoteCard(): void
    {
        $noteCard = NoteCardFactory::new()->create();
        $id = $noteCard->getId();

        static::createClient()->request('DELETE', '/note_cards/'.$id);

        $this->assertResponseStatusCodeSame(204);

        static::createClient()->request('GET', '/note_cards/'.$id);

        $this->assertResponseStatusCodeSame(404);
    }
}