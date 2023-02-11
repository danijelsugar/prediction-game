<?php

namespace App\Factory;

use App\Dto\Head2HeadDto;

class Head2HeadFactory
{
    public static function fromFootballData(object $head2Head): Head2HeadDto
    {
        return new Head2HeadDto(
            $head2Head->numberOfMatches,
            $head2Head->totalGoals,
            $head2Head->homeTeam->wins,
            $head2Head->homeTeam->draws,
            $head2Head->homeTeam->losses,
            $head2Head->awayTeam->wins,
            $head2Head->awayTeam->draws,
            $head2Head->awayTeam->losses
        );
    }

    public static function fromFootballDataNew(object $head2Head): Head2HeadDto
    {
        return new Head2HeadDto(
            $head2Head->numberOfMatches,
            $head2Head->totalGoals,
            $head2Head->homeTeam->wins,
            $head2Head->homeTeam->draws,
            $head2Head->homeTeam->losses,
            $head2Head->awayTeam->wins,
            $head2Head->awayTeam->draws,
            $head2Head->awayTeam->losses
        );
    }
}
