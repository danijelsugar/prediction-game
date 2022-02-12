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

    public function fetchData(string $uri, array $filters = []): object
    {
        //dd(self::URL.$uri.'?'.http_build_query($filters));
        $response = $this->client->request(
            'GET',
            self::URL.$uri.'?'.http_build_query($filters), [
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
    public function getPredictionRoundsInfo(array $matches): array
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
    public function getMatchdayDates(array $data): array
    {
        $dates = [];
        foreach ($data as $info) {
            if (!is_null($info['matchday']) && is_null($info['group']) && 'REGULAR_SEASON' !== $info['stage']) {
                $dates[$info['stage']][] = $info['date'];
            } elseif (!is_null($info['matchday'])) {
                $dates[$info['matchday']][] = $info['date'];
            } else {
                $dates[$info['stage']][] = $info['date'];
            }
        }

        if (!array_filter(array_keys($dates), 'is_string')) {
            ksort($dates);
        }

        return $dates;
    }

    /**
     * Gets date of first and last match for each matchday.
     */
    public function getFirstAndLastMatchdayDate(array $data): array
    {
        $firstAndLastMatchdayDate = [];
        foreach ($data as $matchday => $value) {
            $firstTimestamp = min(array_map('strtotime', $value));
            $firstDate = new \DateTime();
            $firstDate->setTimestamp($firstTimestamp);
            //$firstAndLastMatchdayDate[$matchday] = ['dateFrom' => $firstDate->format('d.m.Y')];

            $lastTimestamp = max(array_map('strtotime', $value));
            $lastDate = new \DateTime();
            $lastDate->setTimestamp($lastTimestamp);
            $firstAndLastMatchdayDate[$matchday] = ['dateFrom' => $firstDate, 'dateTo' => $lastDate];
        }

        return $firstAndLastMatchdayDate;
    }

    /**
     * Gets status of each matchday (if all matches are finished, scheduled or half finished).
     */
    public function getRoundStatus(array $data): array
    {
        $roundStatus = [];
        foreach ($data as $info) {
            if (!is_null($info['matchday']) && is_null($info['group']) && 'REGULAR_SEASON' !== $info['stage']) {
                $roundStatus[$info['stage']][] = $info['status'];
            } elseif (!is_null($info['matchday'])) {
                $roundStatus[$info['matchday']][] = $info['status'];
            } else {
                $roundStatus[$info['stage']][] = $info['status'];
            }
        }

        foreach ($roundStatus as $key => $value) {
            if (1 === count(array_unique($value)) && 'FINISHED' === end($value)) {
                $roundStatus[$key] = 'FINISHED';
            } elseif (1 === count(array_unique($value)) && 'SCHEDULED' === end($value)) {
                $roundStatus[$key] = 'SCHEDULED';
            } else {
                $roundStatus[$key] = 'HALF';
            }
        }

        return $roundStatus;
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
