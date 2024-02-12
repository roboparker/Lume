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
        $isPublished = true;
        $client = static::createClient();
        $client->loginUser(UserFactory::new()->create()->object());

        $client->request('POST', '/note_cards', [
            'json' => [
                'front' => $front,
                'back' => $back,
                'isPublished' => $isPublished,
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
            'isPublished' => $isPublished,
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

        $client = static::createClient();
        $client->loginUser(UserFactory::new()->create()->object());

        $client->request('PUT', '/note_cards/'.$noteCard->getId(), [
            'json' => [
                'front' => $updatedFrontValue,
                'back' => $updatedBackValue,
                'isPublished' => $updatedIsPublished,
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
            'isPublished' => $previousIsPublished,
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

    public function testIsPublishedFilter(): void
    {
        NoteCardFactory::new()->create(['isPublished' => true]);
        NoteCardFactory::new()->create(['isPublished' => false]);
        $client = static::createClient();
        $client->loginUser(UserFactory::new()->create()->object());

        $client->request('GET', '/note_cards', [
            'query' => [
                'isPublished' => true,
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '@context' => '/contexts/NoteCard',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 1,
        ]);
    }

    public function testFrontFilter(): void
    {
        $front = Factory::create()->text(255);
        NoteCardFactory::new()->create(['front' => $front]);
        NoteCardFactory::new()->create(['front' => Factory::create()->text(255)]);
        $client = static::createClient();
        $client->loginUser(UserFactory::new()->create()->object());

        $client->request('GET', '/note_cards', [
            'query' => [
                'front' => $front,
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '@context' => '/contexts/NoteCard',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 1,
        ]);
    }

    public function testBackFilter(): void
    {
        $back = Factory::create()->text();
        NoteCardFactory::new()->create(['back' => $back]);
        NoteCardFactory::new()->create(['back' => Factory::create()->text()]);
        $client = static::createClient();
        $client->loginUser(UserFactory::new()->create()->object());

        $client->request('GET', '/note_cards', [
            'query' => [
                'back' => $back,
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '@context' => '/contexts/NoteCard',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 1,
        ]);
    }
}
