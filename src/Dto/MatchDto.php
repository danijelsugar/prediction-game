<?php

namespace App\Dto;

class MatchDto
{
    private int $matchId;

    private string $stage;

    private ?string $groupName;

    private \DateTimeInterface $date;

    private ?string $homeTeamName;

    private ?string $awayTeamName;

    private ?int $fullTimeHomeTeamScore;

    private ?int $fullTimeAwayTeamScore;

    private ?int $extraTimeHomeTeamScore;

    private ?int $extraTimeAwayTeamScore;

    private ?string $winner;

    private \DateTimeInterface $lastUpdated;

    private ?int $matchday;

    private string $status;

    public function __construct(
        int $matchId,
        string $stage,
        ?string $groupName,
        \DateTimeInterface $date,
        ?string $homeTeamName,
        ?string $awayTeamName,
        ?int $fullTimeHomeTeamScore,
        ?int $fullTimeAwayTeamScore,
        ?int $extraTimeHomeTeamScore,
        ?int $extraTimeAwayTeamScore,
        ?string $winner,
        \DateTimeInterface $lastUpdated,
        ?int $matchday,
        string $status
    ) {
        $this->matchId = $matchId;
        $this->stage = $stage;
        $this->groupName = $groupName;
        $this->date = $date;
        $this->homeTeamName = $homeTeamName;
        $this->awayTeamName = $awayTeamName;
        $this->fullTimeHomeTeamScore = $fullTimeHomeTeamScore;
        $this->fullTimeAwayTeamScore = $fullTimeAwayTeamScore;
        $this->extraTimeHomeTeamScore = $extraTimeHomeTeamScore;
        $this->extraTimeAwayTeamScore = $extraTimeAwayTeamScore;
        $this->winner = $winner;
        $this->lastUpdated = $lastUpdated;
        $this->matchday = $matchday;
        $this->status = $status;
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
}
