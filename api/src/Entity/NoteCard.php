<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Serializer\Filter\PropertyFilter;
use App\Entity\Trait\IsPublishedTrait;
use App\Repository\NoteCardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: NoteCardRepository::class)]
#[ApiResource]
#[ApiFilter(PropertyFilter::class)]
class NoteCard
{
    use IsPublishedTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'partial')]
    private ?string $front = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'partial')]
    private ?string $back = null;

    #[ORM\ManyToMany(targetEntity: Deck::class, mappedBy: 'cards')]
    private Collection $decks;

    public function __construct()
    {
        $this->decks = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getFront(): ?string
    {
        return $this->front;
    }

    public function setFront(string $front): static
    {
        $this->front = $front;

        return $this;
    }

    public function getBack(): ?string
    {
        return $this->back;
    }

    public function setBack(?string $back): static
    {
        $this->back = $back;

        return $this;
    }

    /**
     * @return Collection<int, Deck>
     */
    public function getDecks(): Collection
    {
        return $this->decks;
    }

    public function addDeck(Deck $deck): static
    {
        if (!$this->decks->contains($deck)) {
            $this->decks->add($deck);
            $deck->addCard($this);
        }

        return $this;
    }

    public function removeDeck(Deck $deck): static
    {
        if ($this->decks->removeElement($deck)) {
            $deck->removeCard($this);
        }

        return $this;
    }
}
