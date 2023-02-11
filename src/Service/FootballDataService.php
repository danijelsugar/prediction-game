<?php

namespace App\Service;

use App\Dto\MatchDto;

class FootballDataService
{
    /**
     * Gets dates of matchdays sorted by matchday.
     *
     * @param MatchDto[] $matches
     *
     * @return array<int|string, array<int, array<string, \DateTimeInterface|int|string|null>>> $dates
     */
    public function getRoundInfo(array $matches): array
    {
        $dates = [];
        foreach ($matches as $match) {
            if (!is_null($match->getMatchday()) && is_null($match->getGroupName()) && 'REGULAR_SEASON' !== $match->getStage()) {
                $dates[$match->getStage()][] = [
                    'matchday' => $match->getMatchday(),
                    'stage' => $match->getStage(),
                    'date' => $match->getDate(),
                    'status' => $match->getStatus(),
                ];
            } elseif (!is_null($match->getMatchday())) {
                $dates[$match->getMatchday()][] = [
                    'matchday' => $match->getMatchday(),
                    'stage' => $match->getStage(),
                    'date' => $match->getDate(),
                    'status' => $match->getStatus(),
                ];
            } else {
                $dates[$match->getStage()][] = [
                    'matchday' => $match->getMatchday(),
                    'stage' => $match->getStage(),
                    'date' => $match->getDate(),
                    'status' => $match->getStatus(),
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
     *
     * @param array<int, array<string, \DateTimeInterface|int|string|null>> $data
     *
     * @return array<string, \DatetimeInterface>
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
     *
     * @param array<int, array<string, \DateTimeInterface|int|string|null>> $data
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
     *
     * @param array<int, array<string, \DateTimeInterface|int|string|null>> $data
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
