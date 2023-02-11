<?php

namespace App\Dto;

class TeamDto
{
    private ?string $crest;

    private string $name;

    private ?string $shortName;

    private ?string $tla;

    private ?string $founded;

    private ?string $clubColors;

    private ?string $venue;

    public function __construct(
        ?string $crest,
        string $name,
        ?string $shortName,
        ?string $tla,
        ?string $founded,
        ?string $clubColors,
        ?string $venue
    ) {
        $this->crest = $crest;
        $this->name = $name;
        $this->shortName = $shortName;
        $this->tla = $tla;
        $this->founded = $founded;
        $this->clubColors = $clubColors;
        $this->venue = $venue;
    }

    public function getCrest(): ?string
    {
        return $this->crest;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function getTla(): ?string
    {
        return $this->tla;
    }

    public function getFounded(): ?string
    {
        return $this->founded;
    }

    public function getClubColors(): ?string
    {
        return $this->clubColors;
    }

    public function getVenue(): ?string
    {
        return $this->venue;
    }
}
