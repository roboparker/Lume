<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class NoteCardTest extends ApiTestCase
{
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
}
