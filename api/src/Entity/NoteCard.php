<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\NoteCardRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: NoteCardRepository::class)]
#[ApiResource]
class NoteCard
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private UUID $id;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $front = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $back = null;

    public function getId(): UUID
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
}
