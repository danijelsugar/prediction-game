<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class FootballDataService implements FootballDataInterface
{
    private const URL = 'http://api.football-data.org/v2/';

    private HttpClientInterface $client;

    private $footballApiToken;

    public function __construct(string $footballApiToken, HttpClientInterface $client)
    {
        $this->client = $client;
        $this->footballApiToken = $footballApiToken;
    }

    public function fetchData(string $uri, array $filters = [])
    {
        $response = $this->client->request(
            'GET',
            rtrim(self::URL.$uri.'?'.http_build_query($filters), '?'), [
                'headers' => [
                    'X-Auth-Token' => $this->footballApiToken,
                ],
            ]
        );

        $data = $response->getContent();

        $decoded = json_decode($data);

        return $decoded;
    }

    /**
     * Gets only needed data from api response.
     */
    public function getMatchesInfo(array $matches): array
    {
        $matchdayInfo = [];
        foreach ($matches as $match) {
            $matchdayInfo[] = [
                'matchday' => $match->matchday,
                'stage' => $match->stage,
                'group' => $match->group,
                'date' => $match->utcDate,
                'status' => $match->status,
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
            $dates[] = $match['date'];
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
