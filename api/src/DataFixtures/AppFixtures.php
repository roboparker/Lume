<?php

namespace App\DataFixtures;

use App\Factory\DeckFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::new()->createMany(10);
        DeckFactory::new()->createMany(10);
    }
}
