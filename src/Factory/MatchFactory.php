<?php

namespace App\Factory;

use App\Dto\MatchDto;

class MatchFactory
{
    /**
     * @return MatchDto[]
     */
    public static function fromFootballData(array $data): array
    {
        $dtos = [];
        foreach ($data as $match) {
            $dtos[] = new MatchDto(
                $match->id,
                $match->stage,
                $match->group,
                new \DateTimeImmutable($match->utcDate),
                $match->homeTeam->name,
                $match->awayTeam->name,
                $match->score->fullTime->homeTeam,
                $match->score->fullTime->awayTeam,
                $match->score->extraTime->homeTeam,
                $match->score->extraTime->awayTeam,
                $match->score->winner,
                new \DateTimeImmutable($match->lastUpdated),
                $match->matchday,
                $match->status
            );
        }

        return $dtos;
    }

    /**
     * @return MatchDto[]
     */
    public static function fromFootballDataNew(array $data): array
    {
        $dtos = [];
        foreach ($data as $match) {
            $dtos[] = new MatchDto(
                $match->id,
                $match->stage,
                $match->group,
                new \DateTimeImmutable($match->utcDate),
                $match->homeTeam->name,
                $match->awayTeam->name,
                $match->score->fullTime->home,
                $match->score->fullTime->away,
                $match->score->extraTime->home ?? null,
                $match->score->extraTime->home ?? null,
                $match->score->winner,
                new \DateTimeImmutable($match->lastUpdated),
                $match->matchday,
                $match->status
            );
        }

        return $dtos;
    }
}
