<?php

namespace App\Factory;

use App\Dto\CompetitionDto;

class CompetitionFactory
{
    /**
     * @return CompetitionDto[]
     */
    public static function fromFootballData(array $data): array
    {
        $dtos = [];
        foreach ($data as $competition) {
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
     * @return CompetitionDto[]
     */
    public static function fromFootballDataNew(array $data): array
    {
        $dtos = [];
        foreach ($data as $competition) {
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
