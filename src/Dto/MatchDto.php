<?php

namespace App\Dto;

readonly class MatchDto
{
    public function __construct(
        public int $matchId,
        public string $stage,
        public ?string $groupName,
        public \DateTimeInterface $date,
        public ?string $homeTeamName,
        public ?string $awayTeamName,
        public ?int $fullTimeHomeTeamScore,
        public ?int $fullTimeAwayTeamScore,
        public ?int $extraTimeHomeTeamScore,
        public ?int $extraTimeAwayTeamScore,
        public ?string $winner,
        public \DateTimeInterface $lastUpdated,
        public ?int $matchday,
        public string $status,
        public int $competitionId,
        public ?Head2HeadDto $head2Head
    ) {
    }
}
