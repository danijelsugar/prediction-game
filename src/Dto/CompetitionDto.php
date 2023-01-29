<?php

namespace App\Dto;

class CompetitionDto
{
    private int $competition;

    private string $name;

    private string $code;

    private string $area;

    private string $emblemUrl;

    private ?\DateTimeInterface $lastUpdated;

    public function __construct(int $competition, string $name, string $code, string $area, string $emblemUrl, \DateTimeInterface $lastUpdated)
    {
        $this->competition = $competition;
        $this->name = $name;
        $this->code = $code;
        $this->area = $area;
        $this->emblemUrl = $emblemUrl;
        $this->lastUpdated = $lastUpdated;
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
