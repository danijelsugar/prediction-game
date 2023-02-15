<?php

namespace App\Dto;

class TeamDto
{
    public function __construct(
        private ?string $crest, 
        private string $name, 
        private ?string $shortName, 
        private ?string $tla, 
        private ?string $founded, 
        private ?string $clubColors, 
        private ?string $venue
    ) {
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
