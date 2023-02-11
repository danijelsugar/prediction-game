<?php

namespace App\Factory;

use App\Dto\TeamDto;

class TeamFactory
{
    /**
     * @param object[] $teams
     *
     * @return TeamDto[]
     */
    public static function fromFootballData(array $teams)
    {
        $dtos = [];
        foreach ($teams as $team) {
            $dtos[] = new TeamDto(
                $team->crestUrl,
                $team->name,
                $team->shortName,
                $team->tla,
                $team->founded,
                $team->clubColors,
                $team->venue
            );
        }

        return $dtos;
    }

    /**
     * @param object[] $teams
     *
     * @return TeamDto[]
     */
    public static function fromFootballDataNew(array $teams): array
    {
        $dtos = [];
        foreach ($teams as $team) {
            $dtos[] = new TeamDto(
                $team->crest,
                $team->name,
                $team->shortName,
                $team->tla,
                $team->founded,
                $team->clubColors,
                $team->venue
            );
        }

        return $dtos;
    }
}
