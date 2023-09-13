<?php

declare(strict_types=1);

namespace App\Tests\Unit\Factory;

use App\Dto\HeadToHeadDto;
use App\Dto\MatchDto;
use App\Factory\MatchFactory;
use PHPUnit\Framework\TestCase;

class MatchFactoryTest extends TestCase
{
    /**
     * @dataProvider footballDataProvider
     *
     * @param MatchDto[]  $expected
     * @param \stdClass[] $matches
     */
    public function testFromFootballData(array $expected, array $matches, int $competition): void
    {
        $result = MatchFactory::fromFootballData($matches, $competition);

        // Assert that the factory method returns an array of CompetitionDto objects
        $this->assertIsArray($result, 'Result should be an array');
        $this->assertContainsOnlyInstancesOf(MatchDto::class, $result, 'Result should only contain MatchDto objects');

        $this->assertEquals($expected, $result, 'Result does not match expected');
    }

    /**
     * @dataProvider footballDataSingleMatchProvider
     */
    public function testFromFootballDataSingle(MatchDto $expected, \stdClass $match, \stdClass $headToHead): void
    {
        $result = MatchFactory::fromFootballDataSingle($headToHead, $match);

        $this->assertInstanceOf(MatchDto::class, $result, 'Result is not an instance of MatchDto');
        $this->assertEquals($expected, $result, 'Result does not match expected');
    }

    /**
     * @dataProvider footballDataNewNewSingelMatchProvider
     */
    public function testFromFootballDataNewSingleMatch(MatchDto $expected, \stdClass $match): void
    {
        $result = MatchFactory::fromFootballDataNew($match);

        $this->assertInstanceOf(MatchDto::class, $result, 'Result is not an instance of MatchDto');
        $this->assertEquals($expected, $result, 'Result does not match expected');
    }

    /**
     * @dataProvider footballDataNewProvider
     *
     * @param MatchDto[]  $expected
     * @param \stdClass[] $matches
     */
    public function testFromFootballDataNew(array $expected, array $matches): void
    {
        $result = MatchFactory::fromFootballDataNew($matches);

        // Assert that the factory method returns an array of CompetitionDto objects
        $this->assertIsArray($result, 'Result should be an array');
        $this->assertContainsOnlyInstancesOf(MatchDto::class, $result, 'Result should only contain MatchDto objects');

        $this->assertEquals($expected, $result, 'Result does not match expected');
    }

    public function footballDataProvider(): \Iterator
    {
        yield 'REGULAR_SEASON' => [
            [
                new MatchDto(
                    435952,
                    'REGULAR_SEASON',
                    null,
                    new \DateTimeImmutable('2023-08-14T19:00:00Z'),
                    'Manchester United FC',
                    'Wolverhampton Wanderers FC',
                    1,
                    0,
                    null,
                    null,
                    'HOME_TEAM',
                    new \DateTimeImmutable('2023-09-04T11:55:39Z'),
                    1,
                    'FINISHED',
                    2021,
                    null,
                ),
            ],
            [
                (object) [
                    'id' => 435952,
                    'stage' => 'REGULAR_SEASON',
                    'group' => null,
                    'utcDate' => '2023-08-14T19:00:00Z',
                    'homeTeam' => (object) [
                        'name' => 'Manchester United FC',
                    ],
                    'awayTeam' => (object) [
                        'name' => 'Wolverhampton Wanderers FC',
                    ],
                    'score' => (object) [
                        'fullTime' => (object) [
                            'homeTeam' => 1,
                            'awayTeam' => 0,
                        ],
                        'extraTime' => (object) [
                            'homeTeam' => null,
                            'awayTeam' => null,
                        ],
                        'winner' => 'HOME_TEAM',
                    ],
                    'lastUpdated' => '2023-09-04T11:55:39Z',
                    'matchday' => 1,
                    'status' => 'FINISHED',
                ],
            ],
            2021,
        ];
        yield 'GROUP_STAGE' => [
            [
                new MatchDto(
                    391882,
                    'GROUP_STAGE',
                    'GROUP_A',
                    new \DateTimeImmutable('2022-11-20T16:00:00Z'),
                    'Qatar',
                    'Ecuador',
                    0,
                    2,
                    null,
                    null,
                    'AWAY_TEAM',
                    new \DateTimeImmutable('2023-08-31T15:20:00Z'),
                    1,
                    'FINISHED',
                    2000,
                    null,
                ),
            ],
            [
                (object) [
                    'id' => 391882,
                    'stage' => 'GROUP_STAGE',
                    'group' => 'GROUP_A',
                    'utcDate' => '2022-11-20T16:00:00Z',
                    'homeTeam' => (object) [
                        'name' => 'Qatar',
                    ],
                    'awayTeam' => (object) [
                        'name' => 'Ecuador',
                    ],
                    'score' => (object) [
                        'fullTime' => (object) [
                            'homeTeam' => 0,
                            'awayTeam' => 2,
                        ],
                        'extraTime' => (object) [
                            'homeTeam' => null,
                            'awayTeam' => null,
                        ],
                        'winner' => 'AWAY_TEAM',
                    ],
                    'lastUpdated' => '2023-08-31T15:20:00Z',
                    'matchday' => 1,
                    'status' => 'FINISHED',
                ],
            ],
            2000,
        ];
        yield 'QUARTER_FINALS' => [
            [
                new MatchDto(
                    391937,
                    'QUARTER_FINALS',
                    null,
                    new \DateTimeImmutable('2022-12-09T15:00:00Z'),
                    'Croatia',
                    'Brazil',
                    5,
                    3,
                    1,
                    1,
                    'HOME_TEAM',
                    new \DateTimeImmutable('2023-08-31T15:20:01Z'),
                    5,
                    'FINISHED',
                    2000,
                    null
                ),
            ],
            [
                (object) [
                    'id' => 391937,
                    'stage' => 'QUARTER_FINALS',
                    'group' => null,
                    'utcDate' => '2022-12-09T15:00:00Z',
                    'homeTeam' => (object) [
                        'name' => 'Croatia',
                    ],
                    'awayTeam' => (object) [
                        'name' => 'Brazil',
                    ],
                    'score' => (object) [
                        'fullTime' => (object) [
                            'homeTeam' => 5,
                            'awayTeam' => 3,
                        ],
                        'extraTime' => (object) [
                            'homeTeam' => 1,
                            'awayTeam' => 1,
                        ],
                        'winner' => 'HOME_TEAM',
                    ],
                    'lastUpdated' => '2023-08-31T15:20:01Z',
                    'matchday' => 5,
                    'status' => 'FINISHED',
                ],
            ],
            2000,
        ];
    }

    public function footballDataSingleMatchProvider(): \Iterator
    {
        yield 'REGULAR_SEASON' => [
            new MatchDto(
                435970,
                'REGULAR_SEASON',
                null,
                new \DateTimeImmutable('2023-08-26T14:00:00Z'),
                'Manchester United FC',
                'Nottingham Forest FC',
                3,
                2,
                null,
                null,
                'HOME_TEAM',
                new \DateTimeImmutable('2023-09-04T11:55:39Z'),
                3,
                'FINISHED',
                2021,
                new HeadToHeadDto(
                    10,
                    41,
                    10,
                    0,
                    0,
                    0,
                    0,
                    10,
                )
            ),
            (object) [
                'id' => 435970,
                'competition' => (object) [
                    'id' => 2021,
                    'name' => 'Premier League',
                ],
                'stage' => 'REGULAR_SEASON',
                'group' => null,
                'utcDate' => '2023-08-26T14:00:00Z',
                'homeTeam' => (object) [
                    'name' => 'Manchester United FC',
                ],
                'awayTeam' => (object) [
                    'name' => 'Nottingham Forest FC',
                ],
                'score' => (object) [
                    'fullTime' => (object) [
                        'homeTeam' => 3,
                        'awayTeam' => 2,
                    ],
                    'extraTime' => (object) [
                        'homeTeam' => null,
                        'awayTeam' => null,
                    ],
                    'winner' => 'HOME_TEAM',
                ],
                'lastUpdated' => '2023-09-04T11:55:39Z',
                'matchday' => 3,
                'status' => 'FINISHED',
            ],
            (object) [
                'numberOfMatches' => 10,
                'totalGoals' => 41,
                'homeTeam' => (object) [
                    'wins' => 10,
                    'draws' => 0,
                    'losses' => 0,
                ],
                'awayTeam' => (object) [
                    'wins' => 0,
                    'draws' => 0,
                    'losses' => 10,
                ],
            ],
        ];
    }

    public function footballDataNewNewSingelMatchProvider(): \Iterator
    {
        yield 'REGULAR_SEASON' => [
            new MatchDto(
                432220,
                'REGULAR_SEASON',
                null,
                new \DateTimeImmutable('2023-09-14T00:30:00Z'),
                'SC Internacional',
                'São Paulo FC',
                null,
                null,
                null,
                null,
                null,
                new \DateTimeImmutable('2023-08-28T08:20:09Z'),
                23,
                'TIMED',
                2013,
                null
            ),
            (object) [
                'id' => 432220,
                'competition' => (object) [
                    'id' => 2013,
                    'name' => 'Campeonato Brasileiro Série A',
                ],
                'stage' => 'REGULAR_SEASON',
                'group' => null,
                'utcDate' => '2023-09-14T00:30:00Z',
                'homeTeam' => (object) [
                    'name' => 'SC Internacional',
                ],
                'awayTeam' => (object) [
                    'name' => 'São Paulo FC',
                ],
                'score' => (object) [
                    'fullTime' => (object) [
                        'home' => null,
                        'away' => null,
                    ],
                    'extraTime' => (object) [
                        'home' => null,
                        'away' => null,
                    ],
                    'winner' => null,
                ],
                'lastUpdated' => '2023-08-28T08:20:09Z',
                'matchday' => 23,
                'status' => 'TIMED',
            ],
        ];
    }

    public function footballDataNewProvider(): \Iterator
    {
        yield 'REGULAR_SEASON' => [
            [
                new MatchDto(
                    442712,
                    'REGULAR_SEASON',
                    null,
                    new \DateTimeImmutable('2023-08-12 19:00:00.0 +00:00'),
                    'Paris Saint-Germain FC',
                    'FC Lorient',
                    0,
                    0,
                    null,
                    null,
                    'DRAW',
                    new \DateTimeImmutable('2023-08-31 15:20:26.0 +00:00'),
                    1,
                    'FINISHED',
                    2015,
                    null,
                ),
                new MatchDto(
                    444290,
                    'REGULAR_SEASON',
                    null,
                    new \DateTimeImmutable('2023-09-16 13:00:00.0 +00:00'),
                    'Juventus FC',
                    'SS Lazio',
                    null,
                    null,
                    null,
                    null,
                    null,
                    new \DateTimeImmutable('2023-08-03 16:20:22.0 +00:00'),
                    4,
                    'TIMED',
                    2019,
                    null
                ),
            ],
            [
                (object) [
                    'id' => 442712,
                    'competition' => (object) [
                        'id' => 2015,
                        'name' => 'Ligue 1',
                    ],
                    'stage' => 'REGULAR_SEASON',
                    'group' => null,
                    'utcDate' => '2023-08-12T19:00:00Z',
                    'homeTeam' => (object) [
                        'name' => 'Paris Saint-Germain FC',
                    ],
                    'awayTeam' => (object) [
                        'name' => 'FC Lorient',
                    ],
                    'score' => (object) [
                        'fullTime' => (object) [
                            'home' => 0,
                            'away' => 0,
                        ],
                        'extraTime' => (object) [
                            'home' => null,
                            'away' => null,
                        ],
                        'winner' => 'DRAW',
                    ],
                    'lastUpdated' => '2023-08-31T15:20:26Z',
                    'matchday' => 1,
                    'status' => 'FINISHED',
                ],
                (object) [
                    'id' => 444290,
                    'competition' => (object) [
                        'id' => 2019,
                        'name' => 'Seria A',
                    ],
                    'stage' => 'REGULAR_SEASON',
                    'group' => null,
                    'utcDate' => '2023-09-16T13:00:00Z',
                    'homeTeam' => (object) [
                        'name' => 'Juventus FC',
                    ],
                    'awayTeam' => (object) [
                        'name' => 'SS Lazio',
                    ],
                    'score' => (object) [
                        'fullTime' => (object) [
                            'home' => null,
                            'away' => null,
                        ],
                        'extraTime' => (object) [
                            'home' => null,
                            'away' => null,
                        ],
                        'winner' => null,
                    ],
                    'lastUpdated' => '2023-08-03T16:20:22Z',
                    'matchday' => 4,
                    'status' => 'TIMED',
                ],
            ],
        ];
    }
}
