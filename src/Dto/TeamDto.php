<?php

namespace App\Dto;

readonly class TeamDto
{
    public function __construct(
        public ?string $crest,
        public string $name,
        public ?string $shortName,
        public ?string $tla,
        public ?int $founded,
        public ?string $clubColors,
        public ?string $venue
    ) {
    }
}
