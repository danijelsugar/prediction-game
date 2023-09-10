<?php

namespace App\Helper;

use App\Dto\HeadToHeadDto;
use App\Dto\MatchDto;
use App\Factory\CompetitionFactory;
use App\Factory\HeadToHeadFactory;
use App\Factory\MatchFactory;
use App\Factory\StandingFactory;
use App\Factory\TeamFactory;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FootballDataNew implements FootballInterface
{
    private const URL = 'http://api.football-data.org/v4/';

    private HttpClientInterface $client;

    public function __construct(
        private string $footballApiToken,
        HttpClientInterface $client
    ) {
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

        $decode = json_decode($data, null, 512, JSON_THROW_ON_ERROR);

        return CompetitionFactory::fromFootballDataNew($decode->competitions);
    }

    public function getCompetitionStandings(int $competition, array $filters = []): array
    {
        $resource = 'competitions/'.$competition.'/standings';

        $response = $this->client->request(
            'GET',
            rtrim(self::URL.$resource.'?'.http_build_query($filters), '?')
        );

        $data = $response->getContent();

        $decode = json_decode($data, null, 512, JSON_THROW_ON_ERROR);

        return StandingFactory::fromFootballDataNew($decode->standings);
    }

    public function getCompetitionTeams(int $competition, array $filters = []): array
    {
        $resource = 'competitions/'.$competition.'/teams';

        $response = $this->client->request(
            'GET',
            rtrim(self::URL.$resource.'?'.http_build_query($filters), '?')
        );

        $data = $response->getContent();

        $decode = json_decode($data, null, 512, JSON_THROW_ON_ERROR);

        return TeamFactory::fromFootballDataNew($decode->teams);
    }

    public function getCompetitionMatches(int $competition, array $filters = []): array
    {
        $resource = 'competitions/'.$competition.'/matches';

        $response = $this->client->request(
            'GET',
            rtrim(self::URL.$resource.'?'.http_build_query($filters), '?')
        );

        $data = $response->getContent();

        $decode = json_decode($data, null, 512, JSON_THROW_ON_ERROR);

        return MatchFactory::fromFootballDataNew($decode->matches);
    }

    public function getMatch(int $match): MatchDto
    {
        $resource = 'matches/'.$match;

        $response = $this->client->request(
            'GET',
            self::URL.$resource
        );

        $data = $response->getContent();

        $decode = json_decode($data, null, 512, JSON_THROW_ON_ERROR);

        return MatchFactory::fromFootballDataNew($decode);
    }

    public function getMatches(array $filters = []): array
    {
        $resource = 'matches/';

        $response = $this->client->request(
            'GET',
            rtrim(self::URL.$resource.'?'.http_build_query($filters), '?')
        );

        $data = $response->getContent();

        $decode = json_decode($data, null, 512, JSON_THROW_ON_ERROR);

        return MatchFactory::fromFootballDataNew($decode->matches);
    }

    /**
     * List previous encounters for the teams of a match.
     *
     * @param array<string, string> $filters
     */
    public function getHeadToHead(int $match, array $filters = []): HeadToHeadDto
    {
        $resource = 'matches/'.$match.'/head2head';

        $response = $this->client->request(
            'GET',
            rtrim(self::URL.$resource.'?'.http_build_query($filters), '?')
        );

        $data = $response->getContent();

        $decode = json_decode($data, null, 512, JSON_THROW_ON_ERROR);

        return HeadToHeadFactory::fromFootballDataNew($decode->aggregates);
    }
}
