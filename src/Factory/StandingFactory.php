<?php

namespace App\Factory;

use App\Dto\StandingDto;

class StandingFactory
{
    /**
     * @param object[] $competitionStandings
     *
     * @return StandingDto[]
     */
    public static function fromFootballData(array $competitionStandings): array
    {
        $dtos = [];
        foreach ($competitionStandings as $standings) {
            foreach ($standings->table as $team) {
                $dtos[] = new StandingDto(
                    $standings->group,
                    $team->position,
                    $team->team->crestUrl,
                    $team->team->name,
                    $team->points,
                    $team->playedGames,
                    $team->form,
                    $team->won,
                    $team->draw,
                    $team->lost,
                    $team->goalsFor,
                    $team->goalsAgainst,
                    $team->goalDifference
                );
            }
        }

        return $dtos;
    }

    /**
     * @param object[] $competitionStandings
     *
     * @return StandingDto[]
     */
    public static function fromFootballDataNew(array $competitionStandings)
    {
        $dtos = [];
        foreach ($competitionStandings as $standings) {
            foreach ($standings->table as $team) {
                $dtos[] = new StandingDto(
                    $standings->group,
                    $team->position,
                    $team->team->crest,
                    $team->team->name,
                    $team->points,
                    $team->playedGames,
                    $team->form,
                    $team->won,
                    $team->draw,
                    $team->lost,
                    $team->goalsFor,
                    $team->goalsAgainst,
                    $team->goalDifference
                );
            }
        }

        return $dtos;
    }
}
