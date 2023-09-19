<?php

namespace App\Service;

use App\Dto\MatchDto;
use App\Dto\RoundDto;

class FootballDataService
{
    public const STATUS_FINISHED = 'FINISHED';
    public const STATUS_SCHEDULED = 'SCHEDULED';
    public const STATUS_PARTIALLY_FINISHED = 'PARTIALLY_FINISHED';

    /**
     * Gets dates of matchdays sorted by matchday.
     *
     * @param MatchDto[] $matches
     *
     * @return RoundDto[] $rounds
     */
    public function getRoundInfo(array $matches): array
    {
        $roundMatches = $this->groupMatchesByRounds($matches);
        $rounds = $this->generateRoundData($roundMatches);

        return $rounds;
    }

    /**
     * @param MatchDto[] $matches
     *
     * @return array<int|string, MatchDto[]>
     */
    private function groupMatchesByRounds(array $matches): array
    {
        $roundMatches = [];

        foreach ($matches as $match) {
            $roundName = $this->getRoundName($match);
            $roundMatches[$roundName][] = $match;
        }

        return $roundMatches;
    }

    private function getRoundName(MatchDto $match): string|int
    {
        if (!is_null($match->matchday) && is_null($match->groupName) && 'REGULAR_SEASON' !== $match->stage) {
            return $match->stage;
        }

        if (!is_null($match->matchday)) {
            return $match->matchday;
        }

        return $match->stage;
    }

    /**
     * @param array<int|string, MatchDto[]> $roundMatches
     *
     * @return RoundDto[] $rounds
     */
    public function generateRoundData(array $roundMatches): array
    {
        $rounds = [];

        foreach ($roundMatches as $round => $matches) {
            $stage = $this->getRoundStage($matches);

            [$dateFrom, $dateTo] = $this->getFirstAndLastMatchdayDate($matches);

            $status = $this->getRoundStatus($matches);

            $rounds[] = new RoundDto(
                $round,
                $stage,
                $dateFrom,
                $dateTo,
                $status
            );
        }

        return $rounds;
    }

    /**
     * Gets date of first and last match for each round.
     *
     * @param MatchDto[] $matches
     *
     * @return array<int, \DatetimeInterface>
     */
    private function getFirstAndLastMatchdayDate(array $matches): array
    {
        $dates = array_column($matches, 'date');

        sort($dates);

        $dateFrom = reset($dates);
        $dateTo = end($dates);

        return [$dateFrom, $dateTo];
    }

    /**
     * Gets status of each round (if all matches are finished, scheduled or partially finished).
     *
     * @param MatchDto[] $matches
     */
    private function getRoundStatus(array $matches): string
    {
        $roundStatus = array_column($matches, 'status');

        $uniqueStatus = array_unique($roundStatus);
        $statusCount = count($uniqueStatus);

        if (1 === $statusCount && $uniqueStatus === [self::STATUS_FINISHED]) {
            return self::STATUS_FINISHED;
        } elseif (1 === $statusCount && $uniqueStatus === [self::STATUS_SCHEDULED]) {
            return self::STATUS_SCHEDULED;
        } else {
            return self::STATUS_PARTIALLY_FINISHED;
        }
    }

    /**
     * Gets stage of round.
     *
     * @param MatchDto[] $matches
     */
    private function getRoundStage(array $matches): string
    {
        $stages = array_map(fn ($match) => $match->stage, $matches);

        return end($stages);
    }

    /** Get competition season(2020/2021) or year(2018) */
    public function getSeason(string $seasonStartDate, string $seasonEndDate): string
    {
        $startDate = (new \DateTime($seasonStartDate))->format('Y');

        $endDate = (new \DateTime($seasonEndDate))->format('Y');

        if ($startDate !== $endDate) {
            return $startDate.'/'.$endDate;
        }

        return $startDate;
    }
}
