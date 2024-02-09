<?php

namespace App\Tests\Entity;

use App\Entity\NoteCard;
use Faker\Factory;
use PHPUnit\Framework\TestCase;

final class NoteCardTest extends TestCase
{
    public function testGetId(): void
    {
        $noteCard = new NoteCard();
        $this->assertNull($noteCard->getId());
    }

    public function testGetFront(): void
    {
        $noteCard = new NoteCard();
        $front = Factory::create()->sentence();
        $noteCard->setFront($front);
        $this->assertSame($front, $noteCard->getFront());
    }

    public function testGetBack(): void
    {
        $noteCard = new NoteCard();
        $back = Factory::create()->sentence();
        $noteCard->setBack($back);
        $this->assertSame($back, $noteCard->getBack());
    }
}
