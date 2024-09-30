<?php

namespace App\Entity;

use App\Repository\StationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StationRepository::class)]
class Station
{
    #[ORM\Id]
    #[ORM\Column(type: 'bigint')]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'stations')]
    #[ORM\JoinColumn(name: "emailuser_id", referencedColumnName: "id")]
    private ?User $emailuser = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getEmailuser(): ?User
    {
        return $this->emailuser;
    }

    public function setEmailuser(?User $emailuser): static
    {
        $this->emailuser = $emailuser;

        return $this;
    }
}