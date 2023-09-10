<?php

namespace App\Dto;

readonly class HeadToHeadDto
{
    public function __construct(
        public int $numberOfMatches,
        public int $totalGoals,
        public int $homeTeamWins,
        public int $homeTeamDraws,
        public int $homeTeamLosses,
        public int $awayTeamWins,
        public int $awayTeamDraws,
        public int $awayTeamLosses
    ) {
    }
}
