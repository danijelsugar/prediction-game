<?php

namespace App\Factory;

use App\Dto\MatchDto;

class MatchFactory
{
    /**
     * @param object[] $matches
     *
     * @return MatchDto[]
     */
    public static function fromFootballData(array $matches, int $competition = null): array
    {
        $dtos = [];
        foreach ($matches as $match) {
            if (isset($match->competition->id)) {
                $matchCompetition = $match->competition->id;
            } else {
                $matchCompetition = $competition;
            }

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
                $match->status,
                $matchCompetition,
                null
            );
        }

        return $dtos;
    }

    public static function fromFootballDataSingle(object $headToHead, object $match): MatchDto
    {
        return new MatchDto(
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
            $match->status,
            $match->competition->id,
            HeadToHeadFactory::fromFootballData($headToHead)
        );
    }

    /**
     * @param object[]|object $matches
     *
     * @return MatchDto[]|MatchDto
     */
    public static function fromFootballDataNew($matches): array|MatchDto
    {
        if (!is_array($matches)) {
            return new MatchDto(
                $matches->id,
                $matches->stage,
                $matches->group,
                new \DateTimeImmutable($matches->utcDate),
                $matches->homeTeam->name,
                $matches->awayTeam->name,
                $matches->score->fullTime->home,
                $matches->score->fullTime->away,
                $matches->score->extraTime->home ?? null,
                $matches->score->extraTime->home ?? null,
                $matches->score->winner,
                new \DateTimeImmutable($matches->lastUpdated),
                $matches->matchday,
                $matches->status,
                $matches->competition->id,
                null
            );
        }

        $dtos = [];
        foreach ($matches as $match) {
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
                $match->status,
                $match->competition->id,
                null
            );
        }

        return $dtos;
    }
}
