<?php

namespace App\Helper;

use App\Dto\MatchDto;
use App\Factory\CompetitionFactory;
use App\Factory\MatchFactory;
use App\Factory\StandingFactory;
use App\Factory\TeamFactory;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FootballData implements FootballInterface
{
    private const URL = 'http://api.football-data.org/v2/';

    private HttpClientInterface $client;

    private string $footballApiToken;

    public function __construct(string $footballApiToken, HttpClientInterface $client)
    {
        $this->footballApiToken = $footballApiToken;
        $this->client = $client->withOptions([
            'headers' => [
                'X-Auth-Token' => $this->footballApiToken,
            ],
        ]);
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

    public function getCompetitionStandings(int $competition, array $filters = []): array
    {
        $resource = 'competitions/'.$competition.'/standings';

        $response = $this->client->request(
            'GET',
            rtrim(self::URL.$resource.'?'.http_build_query($filters), '?')
        );

        $data = $response->getContent();

        $decode = json_decode($data);

        return StandingFactory::fromFootballData($decode->standings);
    }

    public function getCompetitionTeams(int $competition, array $filters = []): array
    {
        $resource = 'competitions/'.$competition.'/teams';

        $response = $this->client->request(
            'GET',
            rtrim(self::URL.$resource.'?'.http_build_query($filters), '?')
        );

        $data = $response->getContent();

        $decode = json_decode($data);

        return TeamFactory::fromFootballData($decode->teams);
    }

    public function getCompetitionMatches(int $competition, array $filters = []): array
    {
        $resource = 'competitions/'.$competition.'/matches';

        $response = $this->client->request(
            'GET',
            rtrim(self::URL.$resource.'?'.http_build_query($filters), '?')
        );

        $data = $response->getContent();

        $decode = json_decode($data);

        return MatchFactory::fromFootballData($decode->matches, $competition);
    }

    public function getMatch(int $match): MatchDto
    {
        $resource = 'matches/'.$match;

        $response = $this->client->request(
            'GET',
            self::URL.$resource
        );

        $data = $response->getContent();

        $decode = json_decode($data);

        return MatchFactory::fromFootballDataSingle($decode->head2head, $decode->match);
    }

    public function getMatches(array $filters = []): array
    {
        $resource = 'matches/';

        $response = $this->client->request(
            'GET',
            rtrim(self::URL.$resource.'?'.http_build_query($filters), '?')
        );

        $data = $response->getContent();

        $decode = json_decode($data);

        return MatchFactory::fromFootballData($decode->matches);
    }
}
