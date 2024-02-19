<?php

namespace App\Factory;

use App\Entity\Deck;
use App\Repository\DeckRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Deck>
 *
 * @method        Deck|Proxy                     create(array|callable $attributes = [])
 * @method static Deck|Proxy                     createOne(array $attributes = [])
 * @method static Deck|Proxy                     find(object|array|mixed $criteria)
 * @method static Deck|Proxy                     findOrCreate(array $attributes)
 * @method static Deck|Proxy                     first(string $sortedField = 'id')
 * @method static Deck|Proxy                     last(string $sortedField = 'id')
 * @method static Deck|Proxy                     random(array $attributes = [])
 * @method static Deck|Proxy                     randomOrCreate(array $attributes = [])
 * @method static DeckRepository|RepositoryProxy repository()
 * @method static Deck[]|Proxy[]                 all()
 * @method static Deck[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Deck[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Deck[]|Proxy[]                 findBy(array $attributes)
 * @method static Deck[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Deck[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class DeckFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function getDefaults(): array
    {
        return [
            'title' => self::faker()->text(255),
            'description' => self::faker()->text(),
            'isPublished' => self::faker()->boolean(),
            'cards' => NoteCardFactory::new()->many(5),
            'ownedBy' => UserFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Deck $deck): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Deck::class;
    }
}
