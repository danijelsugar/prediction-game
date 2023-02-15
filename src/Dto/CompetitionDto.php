<?php

namespace App\Dto;

class CompetitionDto
{
    public function __construct(
        private int $competition,
        private string $name,
        private string $code,
        private string $area,
        private string $emblemUrl,
        private ?\DateTimeInterface $lastUpdated
    ) {
    }

    public function getCompetition(): ?int
    {
        return $this->competition;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getArea(): ?string
    {
        return $this->area;
    }

    public function getEmblemUrl(): ?string
    {
        return $this->emblemUrl;
    }

    public function getLastUpdated(): ?\DateTimeInterface
    {
        return $this->lastUpdated;
    }
}
