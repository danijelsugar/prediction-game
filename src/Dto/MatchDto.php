<?php

namespace App\Dto;

class MatchDto
{
    public function __construct(
        private int $matchId, 
        private string $stage, 
        private ?string $groupName, 
        private \DateTimeInterface $date, 
        private ?string $homeTeamName, 
        private ?string $awayTeamName, 
        private ?int $fullTimeHomeTeamScore, 
        private ?int $fullTimeAwayTeamScore, 
        private ?int $extraTimeHomeTeamScore, 
        private ?int $extraTimeAwayTeamScore, 
        private ?string $winner, 
        private \DateTimeInterface $lastUpdated, 
        private ?int $matchday, 
        private string $status, 
        private int $competitionId, 
        private ?Head2HeadDto $head2Head
    ) {
    }

    public function getMatchId(): int
    {
        return $this->matchId;
    }

    public function getStage(): string
    {
        return $this->stage;
    }

    public function getGroupName(): ?string
    {
        return $this->groupName;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function getHomeTeamName(): ?string
    {
        return $this->homeTeamName;
    }

    public function getAwayTeamName(): ?string
    {
        return $this->awayTeamName;
    }

    public function getFullTimeHomeTeamScore(): ?int
    {
        return $this->fullTimeHomeTeamScore;
    }

    public function getFullTimeAwayTeamScore(): ?int
    {
        return $this->fullTimeAwayTeamScore;
    }

    public function getExtraTimeHomeTeamScore(): ?int
    {
        return $this->extraTimeHomeTeamScore;
    }

    public function getExtraTimeAwayTeamScore(): ?int
    {
        return $this->extraTimeAwayTeamScore;
    }

    public function getWinner(): ?string
    {
        return $this->winner;
    }

    public function getLastUpdated(): \DateTimeInterface
    {
        return $this->lastUpdated;
    }

    public function getMatchday(): ?int
    {
        return $this->matchday;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCompetitionId(): int
    {
        return $this->competitionId;
    }

    public function getHead2Head(): ?Head2HeadDto
    {
        return $this->head2Head;
    }
}
