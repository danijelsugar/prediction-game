<?php

namespace App\Dto;

class StandingDto
{
    private ?string $group;

    private int $position;

    private ?string $teamCrest;

    private ?string $teamName;

    private int $points;

    private int $playedGames;

    private ?string $form;

    private int $won;

    private int $draw;

    private int $lost;

    private int $goalsFor;

    private int $goalsAgainst;

    private int $goalDifference;

    public function __construct(
        ?string $group,
        int $position,
        ?string $teamCrest,
        ?string $teamName,
        int $points,
        int $playedGames,
        ?string $form,
        int $won,
        int $draw,
        int $lost,
        int $goalsFor,
        int $goalsAgainst,
        int $goalDifference
    ) {
        $this->group = $group;
        $this->position = $position;
        $this->teamCrest = $teamCrest;
        $this->teamName = $teamName;
        $this->points = $points;
        $this->playedGames = $playedGames;
        $this->form = $form;
        $this->won = $won;
        $this->draw = $draw;
        $this->lost = $lost;
        $this->goalsFor = $goalsFor;
        $this->goalsAgainst = $goalsAgainst;
        $this->goalDifference = $goalDifference;
    }

    public function getGroup(): ?string
    {
        return $this->group;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getTeamCrest(): ?string
    {
        return $this->teamCrest;
    }

    public function getTeamName(): ?string
    {
        return $this->teamName;
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function getPlayedGames(): int
    {
        return $this->playedGames;
    }

    public function getForm(): ?string
    {
        return $this->form;
    }

    public function getWon(): int
    {
        return $this->won;
    }

    public function getDraw(): int
    {
        return $this->draw;
    }

    public function getLost(): int
    {
        return $this->lost;
    }

    public function getGoalsFor(): int
    {
        return $this->goalsFor;
    }

    public function getGoalsAgainst(): int
    {
        return $this->goalsAgainst;
    }

    public function getGoalDifference(): int
    {
        return $this->goalDifference;
    }
}
