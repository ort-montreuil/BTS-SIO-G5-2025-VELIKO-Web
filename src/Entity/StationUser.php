<?php

namespace App\Entity;

use App\Repository\StationUserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StationUserRepository::class)]
class StationUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idUser = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $idStation = null;

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function getIdStation(): ?string
    {
        return $this->idStation;
    }

    public function setIdStation(string $idStation): static
    {
        $this->idStation = $idStation;

        return $this;
    }
}
