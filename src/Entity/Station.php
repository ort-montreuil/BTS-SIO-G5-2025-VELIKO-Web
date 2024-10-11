<?php

namespace App\Entity;

use App\Repository\StationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StationRepository::class)]
class Station
{
    #[ORM\Id]

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $station_id = null;

    #[ORM\Column]
    private ?int $station_code = null;

    #[ORM\Column(length: 150)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $lat = null;

    #[ORM\Column]
    private ?float $lon = null;

    #[ORM\Column]
    private ?int $capacity = null;

    public function getStationId(): ?string
    {
        return $this->station_id;
    }

    public function setStationId(string $station_id): static
    {
        $this->station_id = $station_id;

        return $this;
    }

    public function getStationCode(): ?int
    {
        return $this->station_code;
    }

    public function setStationCode(int $station_code): static
    {
        $this->station_code = $station_code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(float $lat): static
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLon(): ?float
    {
        return $this->lon;
    }

    public function setLon(float $lon): static
    {
        $this->lon = $lon;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }
}