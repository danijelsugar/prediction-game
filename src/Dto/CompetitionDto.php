<?php

namespace App\Dto;

readonly class CompetitionDto
{
    public function __construct(
        public int $competition,
        public string $name,
        public string $code,
        public string $area,
        public string $emblemUrl,
        public ?\DateTimeInterface $lastUpdated
    ) {
    }
}
