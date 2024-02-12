<?php

namespace App\Entity\Trait;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Metadata\ApiFilter;
use Doctrine\ORM\Mapping as ORM;

trait IsPublishedTrait
{
    #[ApiFilter(BooleanFilter::class)]
    #[ORM\Column(type: 'boolean')]
    private bool $isPublished = false;

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): static
    {
        $this->isPublished = $isPublished;

        return $this;
    }
}
