<?php

namespace App\Dto;

class Head2HeadDto
{
    public function __construct(
        private int $numberOfMatches, 
        private int $totalGoals, 
        private int $homeTeamWins, 
        private int $homeTeamDraws, 
        private int $homeTeamLosses, 
        private int $awayTeamWins, 
        private int $awayTeamDraws, 
        private int $awayTeamLosses
    ) {
    }

    public function getNumberOfMatches(): int
    {
        return $this->numberOfMatches;
    }

    public function getTotalGoals(): int
    {
        return $this->totalGoals;
    }

    public function getHomeTeamWins(): int
    {
        return $this->homeTeamWins;
    }

    public function getHomeTeamDraws(): int
    {
        return $this->homeTeamDraws;
    }

    public function getHomeTeamLosses(): int
    {
        return $this->homeTeamLosses;
    }

    public function getAwayTeamWins(): int
    {
        return $this->awayTeamWins;
    }

    public function getAwayTeamDraws(): int
    {
        return $this->awayTeamDraws;
    }

    public function getAwayTeamLosses(): int
    {
        return $this->awayTeamLosses;
    }
}
