<?php

namespace App\Helper;

use App\Factory\CompetitionFactory;
use App\Factory\MatchFactory;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FootballData implements FootballInterface
{
    private const URL = 'http://api.football-data.org/v2/';

    private HttpClientInterface $client;

    private $footballApiToken;

    public function __construct(string $footballApiToken, HttpClientInterface $client)
    {
        $this->footballApiToken = $footballApiToken;
        $this->client = $client->withOptions([
            'headers' => [
                'X-Auth-Token' => $this->footballApiToken,
            ],
        ]);
    }

    public function fetchData(string $uri, array $filters = [])
    {
        $response = $this->client->request(
            'GET',
            rtrim(self::URL.$uri.'?'.http_build_query($filters), '?')
        );

        $data = $response->getContent();

        $decoded = json_decode($data);

        return $decoded;
    }

    public function getCompetitions(array $filters = []): array
    {
        $resource = 'competitions/';

        $response = $this->client->request(
            'GET',
            rtrim(self::URL.$resource.'?'.http_build_query($filters), '?')
        );

        $data = $response->getContent();

        $decode = json_decode($data);

        return CompetitionFactory::fromFootballData($decode->competitions);
    }

    public function getCompetitionMatches($competition, array $filters = []): array
    {
        $resource = 'competitions/'.$competition.'/matches';

        $response = $this->client->request(
            'GET',
            rtrim(self::URL.$resource.'?'.http_build_query($filters), '?')
        );

        $data = $response->getContent();

        $decode = json_decode($data);

        return MatchFactory::fromFootballData($decode->matches);
    }
}
