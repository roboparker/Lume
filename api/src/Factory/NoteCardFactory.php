<?php

namespace App\Factory;

use App\Entity\NoteCard;
use App\Repository\NoteCardRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<NoteCard>
 *
 * @method        NoteCard|Proxy                     create(array|callable $attributes = [])
 * @method static NoteCard|Proxy                     createOne(array $attributes = [])
 * @method static NoteCard|Proxy                     find(object|array|mixed $criteria)
 * @method static NoteCard|Proxy                     findOrCreate(array $attributes)
 * @method static NoteCard|Proxy                     first(string $sortedField = 'id')
 * @method static NoteCard|Proxy                     last(string $sortedField = 'id')
 * @method static NoteCard|Proxy                     random(array $attributes = [])
 * @method static NoteCard|Proxy                     randomOrCreate(array $attributes = [])
 * @method static NoteCardRepository|RepositoryProxy repository()
 * @method static NoteCard[]|Proxy[]                 all()
 * @method static NoteCard[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static NoteCard[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static NoteCard[]|Proxy[]                 findBy(array $attributes)
 * @method static NoteCard[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static NoteCard[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class NoteCardFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'front' => self::faker()->text(),
            'back' => self::faker()->text(),
            'isPublished' => self::faker()->boolean(),
            'ownedBy' => UserFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(NoteCard $noteCard): void {})
        ;
    }

    protected static function getClass(): string
    {
        return NoteCard::class;
    }
}
