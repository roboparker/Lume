<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
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
        $client = static::createClient();
        $client->loginUser(UserFactory::new()->create()->object());

        $client->request('POST', '/note_cards', [
            'json' => [
                'front' => $front,
                'back' => $back,
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            '@context' => '/contexts/NoteCard',
            '@type' => 'NoteCard',
            'front' => $front,
            'back' => $back,
        ]);
    }

    public function testReadNoteCard(): void
    {
        $noteCard = NoteCardFactory::new()->create();
        $client = static::createClient();
        $client->loginUser(UserFactory::new()->create()->object());

        $client->request('GET', '/note_cards/'.$noteCard->getId());

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
        $updatedFrontValue = Factory::create()->sentence();
        $updatedBackValue = Factory::create()->sentence();

        $this->assertFalse($noteCard->getFront() === $updatedFrontValue);
        $this->assertFalse($noteCard->getBack() === $updatedBackValue);

        $client = static::createClient();
        $client->loginUser(UserFactory::new()->create()->object());

        $client->request('PUT', '/note_cards/'.$noteCard->getId(), [
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
        $updatedFrontValue = Factory::create()->sentence();

        $this->assertFalse($noteCard->getFront() === $updatedFrontValue);

        $client = static::createClient();
        $client->loginUser(UserFactory::new()->create()->object());

        $client->request('PATCH', '/note_cards/'.$noteCard->getId(), [
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

        $client = static::createClient();
        $client->loginUser(UserFactory::new()->create()->object());

        $client->request('DELETE', '/note_cards/'.$id);

        $this->assertResponseStatusCodeSame(204);

        $client = static::createClient();
        $client->loginUser(UserFactory::new()->create()->object());

        $client->request('GET', '/note_cards/'.$id);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testNotFoundNoteCard(): void
    {
        $client = static::createClient();
        $client->loginUser(UserFactory::new()->create()->object());

        $client->request('GET', '/note_cards/1');

        $this->assertResponseStatusCodeSame(404);
    }
}
