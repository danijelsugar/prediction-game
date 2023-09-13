<?php

namespace App\Tests;

use App\Dto\StandingDto;
use App\Factory\StandingFactory;
use PHPUnit\Framework\TestCase;

class StandingFactoryTest extends TestCase
{
    /**
     * @dataProvider footballDataProvider
     *
     * @param StandingDto[] $expected
     * @param \stdClass[]   $standings
     */
    public function testFromFootballData(array $expected, array $standings): void
    {
        $result = StandingFactory::fromFootballData($standings);

        // Assert that the factory method returns an array of CompetitionDto objects
        $this->assertIsArray($result, 'Result should be an array');
        $this->assertContainsOnlyInstancesOf(StandingDto::class, $result, 'Result should only contain CompetitionDto objects');

        $this->assertEquals($expected, $result, 'Result does not match expected');
    }

    /**
     * @dataProvider footballDataNewProvider
     *
     * @param StandingDto[] $expected
     * @param \stdClass[]   $standings
     */
    public function testFromFootballDataNew(array $expected, array $standings): void
    {
        $result = StandingFactory::fromFootballDataNew($standings);

        // Assert that the factory method returns an array of CompetitionDto objects
        $this->assertIsArray($result, 'Result should be an array');
        $this->assertContainsOnlyInstancesOf(StandingDto::class, $result, 'Result should only contain CompetitionDto objects');

        $this->assertEquals($expected, $result, 'Result does not match expected');
    }

    public function footballDataProvider(): \Iterator
    {
        yield 'REGULAR_SEASON' => [
            [
                new StandingDto(
                    null,
                    1,
                    'https://crests.football-data.org/65.png',
                    'Manchester City FC',
                    12,
                    4,
                    null,
                    4,
                    0,
                    0,
                    11,
                    2,
                    9
                ),
                new StandingDto(
                    null,
                    2,
                    'https://crests.football-data.org/73.svg',
                    'Tottenham Hotspur FC',
                    10,
                    4,
                    null,
                    3,
                    1,
                    0,
                    11,
                    4,
                    7
                ),
            ],
            [
                (object) [
                    'group' => null,
                    'table' => [
                        (object) [
                            'position' => 1,
                            'team' => (object) [
                                'name' => 'Manchester City FC',
                                'crestUrl' => 'https://crests.football-data.org/65.png',
                            ],
                            'points' => 12,
                            'playedGames' => 4,
                            'form' => null,
                            'won' => 4,
                            'draw' => 0,
                            'lost' => 0,
                            'goalsFor' => 11,
                            'goalsAgainst' => 2,
                            'goalDifference' => 9,
                        ],
                        (object) [
                            'position' => 2,
                            'team' => (object) [
                                'name' => 'Tottenham Hotspur FC',
                                'crestUrl' => 'https://crests.football-data.org/73.svg',
                            ],
                            'points' => 10,
                            'playedGames' => 4,
                            'form' => null,
                            'won' => 3,
                            'draw' => 1,
                            'lost' => 0,
                            'goalsFor' => 11,
                            'goalsAgainst' => 4,
                            'goalDifference' => 7,
                        ],
                    ],
                ],
            ],
        ];
    }

    public function footballDataNewProvider(): \Iterator
    {
        yield 'REGULAR_SEASON' => [
            [
                new StandingDto(
                    null,
                    1,
                    'https://crests.football-data.org/65.png',
                    'Manchester City FC',
                    12,
                    4,
                    null,
                    4,
                    0,
                    0,
                    11,
                    2,
                    9
                ),
                new StandingDto(
                    null,
                    2,
                    'https://crests.football-data.org/73.svg',
                    'Tottenham Hotspur FC',
                    10,
                    4,
                    null,
                    3,
                    1,
                    0,
                    11,
                    4,
                    7
                ),
            ],
            [
                (object) [
                    'group' => null,
                    'table' => [
                        (object) [
                            'position' => 1,
                            'team' => (object) [
                                'name' => 'Manchester City FC',
                                'crest' => 'https://crests.football-data.org/65.png',
                            ],
                            'points' => 12,
                            'playedGames' => 4,
                            'form' => null,
                            'won' => 4,
                            'draw' => 0,
                            'lost' => 0,
                            'goalsFor' => 11,
                            'goalsAgainst' => 2,
                            'goalDifference' => 9,
                        ],
                        (object) [
                            'position' => 2,
                            'team' => (object) [
                                'name' => 'Tottenham Hotspur FC',
                                'crest' => 'https://crests.football-data.org/73.svg',
                            ],
                            'points' => 10,
                            'playedGames' => 4,
                            'form' => null,
                            'won' => 3,
                            'draw' => 1,
                            'lost' => 0,
                            'goalsFor' => 11,
                            'goalsAgainst' => 4,
                            'goalDifference' => 7,
                        ],
                    ],
                ],
            ],
        ];
    }
}
