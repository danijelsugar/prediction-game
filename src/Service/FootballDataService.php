<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class FootballDataService
{
    private const URL = 'http://api.football-data.org/v2/';

    private HttpClientInterface $client;

    private $footballApiToken;

    public function __construct(string $footballApiToken, HttpClientInterface $client)
    {
        $this->client = $client;
        $this->footballApiToken = $footballApiToken;
    }

    public function fetchData(string $uri, $filters = [])
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
}