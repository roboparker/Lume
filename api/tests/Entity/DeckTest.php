<?php

namespace App\Tests\Entity;

use App\Entity\Deck;
use App\Entity\NoteCard;
use Faker\Factory;
use PHPUnit\Framework\TestCase;

final class DeckTest extends TestCase
{
    public function testGetId()
    {
        $deck = new Deck();
        $this->assertNull($deck->getId());
    }

    public function testGetIsPublished()
    {
        $deck = new Deck();
        $deck->setIsPublished(true);
        $this->assertTrue($deck->getIsPublished());
    }

    public function testGetTitle()
    {
        $deck = new Deck();
        $title = Factory::create()->text(255);
        $deck->setTitle($title);
        $this->assertEquals($title, $deck->getTitle());
    }

    public function testGetDescription()
    {
        $deck = new Deck();
        $description = Factory::create()->text();
        $deck->setDescription($description);
        $this->assertEquals($description, $deck->getDescription());
    }

    public function testGetCards()
    {
        $deck = new Deck();
        $noteCard = new NoteCard();
        $noteCard->setFront(Factory::create()->text(255));
        $noteCard->setBack(Factory::create()->text());
        $deck->addCard($noteCard);
        $this->assertContains($noteCard, $deck->getCards());
    }
}
