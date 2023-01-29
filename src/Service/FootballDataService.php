<?php

namespace App\Service;

use App\Dto\MatchDto;

class FootballDataService
{
    /**
     * Gets only needed data from api response.
     */
    public function getMatchesInfo(array $matches): array
    {
        $matchdayInfo = [];

        /** @var MatchDto[] $matches */
        foreach ($matches as $match) {
            $matchdayInfo[] = [
                'matchday' => $match->getMatchday(),
                'stage' => $match->getStage(),
                'group' => $match->getGroupName(),
                'date' => $match->getDate(),
                'status' => $match->getStatus(),
            ];
        }

        return $matchdayInfo;
    }

    /**
     * Gets dates of matchdays sorted by matchday.
     */
    public function getRoundInfo(array $data): array
    {
        $dates = [];
        foreach ($data as $info) {
            if (!is_null($info['matchday']) && is_null($info['group']) && 'REGULAR_SEASON' !== $info['stage']) {
                $dates[$info['stage']][] = [
                    'matchday' => $info['matchday'],
                    'stage' => $info['stage'],
                    'date' => $info['date'],
                    'status' => $info['status'],
                ];
            } elseif (!is_null($info['matchday'])) {
                $dates[$info['matchday']][] = [
                    'matchday' => $info['matchday'],
                    'stage' => $info['stage'],
                    'date' => $info['date'],
                    'status' => $info['status'],
                ];
            } else {
                $dates[$info['stage']][] = [
                    'matchday' => $info['matchday'],
                    'stage' => $info['stage'],
                    'date' => $info['date'],
                    'status' => $info['status'],
                ];
            }
        }

        if (!array_filter(array_keys($dates), 'is_string')) {
            ksort($dates);
        }

        return $dates;
    }

    /**
     * Gets date of first and last match for each round.
     */
    public function getFirstAndLastMatchdayDate(array $data): array
    {
        $dates = [];
        foreach ($data as $match) {
            $dates[] = $match['date']->format('Y-m-d H:i:s');
        }

        $firstTimestamp = min(array_map('strtotime', $dates));
        $firstDate = (new \DateTime())->setTimestamp($firstTimestamp);

        $lastTimestamp = max(array_map('strtotime', $dates));
        $lastDate = (new \DateTime())->setTimestamp($lastTimestamp);

        return ['dateFrom' => $firstDate, 'dateTo' => $lastDate];
    }

    /**
     * Gets status of each round (if all matches are finished, scheduled or half finished).
     */
    public function getRoundStatus(array $data): string
    {
        $roundStatus = [];
        foreach ($data as $match) {
            $roundStatus[] = $match['status'];
        }

        if (1 === count(array_unique($roundStatus)) && 'FINISHED' === end($roundStatus)) {
            $status = 'FINISHED';
        } elseif (1 === count(array_unique($roundStatus)) && 'SCHEDULED' === end($roundStatus)) {
            $status = 'SCHEDULED';
        } else {
            $status = 'HALF';
        }

        return $status;
    }

    /**
     * Gets stage of round.
     */
    public function getRoundStage(array $data): string
    {
        $roundStage = [];
        foreach ($data as $match) {
            $roundStage[] = $match['stage'];
        }

        return end($roundStage);
    }

    /** Get competition season(2020/2021) or year(2018) */
    public function getSeason($season): string
    {
        $seasonStartDate = $season->startDate;
        $startDate = (new \DateTime($seasonStartDate))->format('Y');

        $seasonEndDate = $season->endDate;
        $endDate = (new \DateTime($seasonEndDate))->format('Y');

        if ($startDate !== $endDate) {
            return $startDate.'/'.$endDate;
        }

        return $startDate;
    }
}
