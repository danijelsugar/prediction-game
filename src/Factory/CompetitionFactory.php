<?php

namespace App\Factory;

use App\Dto\CompetitionDto;

class CompetitionFactory
{
    /**
     * @param object[] $competitions
     *
     * @return CompetitionDto[]
     */
    public static function fromFootballData(array $competitions): array
    {
        $dtos = [];
        foreach ($competitions as $competition) {
            $dtos[] = new CompetitionDto(
                $competition->id,
                $competition->name,
                $competition->code,
                $competition->area->name,
                $competition->emblemUrl ?? $competition->area->ensignUrl,
                new \DateTime($competition->lastUpdated)
            );
        }

        return $dtos;
    }

    /**
     * @param object[] $competitions
     *
     * @return CompetitionDto[]
     */
    public static function fromFootballDataNew(array $competitions): array
    {
        $dtos = [];
        foreach ($competitions as $competition) {
            $dtos[] = new CompetitionDto(
                $competition->id,
                $competition->name,
                $competition->code,
                $competition->area->name,
                $competition->emblem ?? $competition->area->flag,
                new \DateTime($competition->lastUpdated)
            );
        }

        return $dtos;
    }
}
