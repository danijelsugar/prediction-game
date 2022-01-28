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
        $response = $this->client->request(
            'GET',
            self::URL . $uri . '?' . http_build_query($filters), [
                'headers' => [
                    'X-Auth-Token' => $this->footballApiToken
                ]
            ]
        );

        $data = $response->getContent();

        $decoded = json_decode($data);

        return $decoded;
    }

    public function getPredictionRoundsInfo(array $matches): array
    {
        $matchdayDates = [];
        foreach ($matches as $match) {
            $matchdayDates[] = [
                'matchday' => $match->matchday,
                'stage' => $match->stage,
                'group' => $match->group,
                'date' => $match->utcDate
            ];
        }

        $dates = [];
        foreach ($matchdayDates as $date) {
            if (!is_null($date['matchday']) && is_null($date['group']) && $date['stage'] !== 'REGULAR_SEASON') {
                $dates[$date['stage']][] = $date['date'];
            } elseif (!is_null($date['matchday'])) {
                $dates[$date['matchday']][] = $date['date'];
            } else {
                $dates[$date['stage']][] = $date['date'];
            }
            
        }
        
        $matchdayFirsLastDates = [];
        foreach ($dates as $matchday => $date) {
            $firstTimestamp = min(array_map('strtotime', $date));
            $firstDate = new \DateTime();
            $firstDate->setTimestamp($firstTimestamp);
            $matchdayFirsLastDates[$matchday] = $firstDate->format('d.m.y');

            $lastTimestamp = max(array_map('strtotime', $date));
            $lastDate = new \DateTime();
            $lastDate->setTimestamp($lastTimestamp);
            $matchdayFirsLastDates[$matchday] .= ' - ' . $lastDate->format('d.m.Y');
        }

        return $matchdayFirsLastDates;
    }

    public function getSeason($season): string
    {
        $seasonStartDate = $season->startDate;
        $startDate = (new \DateTime($seasonStartDate))->format('Y');

        $seasonEndDate = $season->endDate;
        $endDate = (new \DateTime($seasonEndDate))->format('Y');
        dump($startDate, $endDate);

        if ($startDate !== $endDate) {
            return $startDate . '/' . $endDate;
        }
        
        return $startDate;
        
    }
}