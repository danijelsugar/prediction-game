<?php

namespace App\Dto;

readonly class StandingDto
{
    public function __construct(
        public ?string $group,
        public int $position,
        public ?string $teamCrest,
        public ?string $teamName,
        public int $points,
        public int $playedGames,
        public ?string $form,
        public int $won,
        public int $draw,
        public int $lost,
        public int $goalsFor,
        public int $goalsAgainst,
        public int $goalDifference
    ) {
    }
}
