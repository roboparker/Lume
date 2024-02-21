<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Serializer\Filter\PropertyFilter;
use App\Entity\Trait\IsPublishedTrait;
use App\Repository\NoteCardRepository;
use App\Security\Entity\OwnedByInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: NoteCardRepository::class)]
#[ApiResource(
    operations: [
        new Delete(security: 'is_granted("DELETE", object)'),
        new Get(),
        new GetCollection(),
        new Patch(security: 'is_granted("PATCH", object)'),
        new Post(),
        new Put(security: 'is_granted("PUT", object)'),
    ]
)]
#[ApiFilter(PropertyFilter::class)]
class NoteCard implements OwnedByInterface
{
    use IsPublishedTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Assert\Uuid]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'noteCards')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $ownedBy = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'partial')]
    private ?string $front = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'partial')]
    private ?string $back = null;

    #[ORM\ManyToMany(targetEntity: Deck::class, mappedBy: 'cards')]
    #[Assert\Valid]
    private Collection $decks;

    public function __construct()
    {
        $this->decks = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getOwnedBy(): ?User
    {
        return $this->ownedBy;
    }

    public function setOwnedBy(?User $ownedBy): static
    {
        $this->ownedBy = $ownedBy;

        return $this;
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
