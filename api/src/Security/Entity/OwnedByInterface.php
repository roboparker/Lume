<?php

namespace App\Security\Entity;

use App\Entity\User;

interface OwnedByInterface
{
    public function getOwnedBy(): ?User;
}
