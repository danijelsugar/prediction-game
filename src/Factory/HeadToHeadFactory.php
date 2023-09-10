<?php

namespace App\Factory;

use App\Dto\HeadToHeadDto;

class HeadToHeadFactory
{
    public static function fromFootballData(object $headToHead): HeadToHeadDto
    {
        return new HeadToHeadDto(
            $headToHead->numberOfMatches,
            $headToHead->totalGoals,
            $headToHead->homeTeam->wins,
            $headToHead->homeTeam->draws,
            $headToHead->homeTeam->losses,
            $headToHead->awayTeam->wins,
            $headToHead->awayTeam->draws,
            $headToHead->awayTeam->losses
        );
    }

    public static function fromFootballDataNew(object $headToHead): HeadToHeadDto
    {
        return new HeadToHeadDto(
            $headToHead->numberOfMatches,
            $headToHead->totalGoals,
            $headToHead->homeTeam->wins,
            $headToHead->homeTeam->draws,
            $headToHead->homeTeam->losses,
            $headToHead->awayTeam->wins,
            $headToHead->awayTeam->draws,
            $headToHead->awayTeam->losses
        );
    }
}
