<?php

namespace App\Tests\Entity;

use App\Entity\NoteCard;
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
        $noteCard->setFront('Question');
        $this->assertSame('Question', $noteCard->getFront());
    }

    public function testGetBack(): void
    {
        $noteCard = new NoteCard();
        $noteCard->setBack('Answer');
        $this->assertSame('Answer', $noteCard->getBack());
    }
}
