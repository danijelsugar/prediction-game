<?php

namespace App\Tests\Unit\Factory;

use App\Dto\CompetitionDto;
use App\Factory\CompetitionFactory;
use PHPUnit\Framework\TestCase;

class CompetitionFactoryTest extends TestCase
{
    public function testFromFootballData(): void
    {
        $competitions = [
            (object) [
                'id' => 2021,
                'name' => 'Premier League',
                'code' => 'PL',
                'area' => (object) [
                    'name' => 'England',
                    'ensignUrl' => 'https://crests.football-data.org/770.svg',
                ],
                'emblemUrl' => 'https://crests.football-data.org/PL.png',
                'lastUpdated' => '2022-03-20T08:58:54Z',
            ],
            (object) [
                'id' => '2014',
                'name' => 'Primera Division',
                'code' => 'PD',
                'area' => (object) [
                    'name' => 'Spain',
                    'ensignUrl' => 'https://crests.football-data.org/760.svg',
                ],
                'emblemUrl' => 'https://crests.football-data.org/PD.png',
                'lastUpdated' => '2022-03-20T09:20:08Z',
            ],
        ];

        $expected = [
            new CompetitionDto(
                2021,
                'Premier League',
                'PL',
                'England',
                'https://crests.football-data.org/PL.png',
                new \DateTime('2022-03-20T08:58:54Z')
            ),
            new CompetitionDto(
                2014,
                'Primera Division',
                'PD',
                'Spain',
                'https://crests.football-data.org/PD.png',
                new \DateTime('2022-03-20T09:20:08Z')
            ),
        ];

        $result = CompetitionFactory::fromFootballData($competitions);

        // Assert that the factory method returns an array of CompetitionDto objects
        $this->assertIsArray($result, 'Result should be an array');
        $this->assertContainsOnlyInstancesOf(CompetitionDto::class, $result, 'Result should only contain CompetitionDto objects');

        $this->assertEquals($expected, $result, 'Result does not match expected');
    }

    public function testFromFootballDataWithMissingEmblem(): void
    {
        $competitions = [
            (object) [
                'id' => 2021,
                'name' => 'Premier League',
                'code' => 'PL',
                'area' => (object) [
                    'name' => 'England',
                    'ensignUrl' => 'https://crests.football-data.org/770.svg',
                ],
                'emblemUrl' => null,
                'lastUpdated' => '2022-03-20T08:58:54Z',
            ],
            (object) [
                'id' => '2014',
                'name' => 'Primera Division',
                'code' => 'PD',
                'area' => (object) [
                    'name' => 'Spain',
                    'ensignUrl' => 'https://crests.football-data.org/760.svg',
                ],
                'emblemUrl' => null,
                'lastUpdated' => '2022-03-20T09:20:08Z',
            ],
        ];

        $expected = [
            new CompetitionDto(
                2021,
                'Premier League',
                'PL',
                'England',
                'https://crests.football-data.org/770.svg',
                new \DateTime('2022-03-20T08:58:54Z')
            ),
            new CompetitionDto(
                2014,
                'Primera Division',
                'PD',
                'Spain',
                'https://crests.football-data.org/760.svg',
                new \DateTime('2022-03-20T09:20:08Z')
            ),
        ];

        $result = CompetitionFactory::fromFootballData($competitions);

        $this->assertEquals($expected, $result);
    }

    public function testFromFootballDataWithEmptyInput(): void
    {
        $competitions = [];
        $result = CompetitionFactory::fromFootballData($competitions);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testFromFootballDataNew(): void
    {
        $competitions = [
            (object) [
                'id' => 2021,
                'name' => 'Premier League',
                'code' => 'PL',
                'area' => (object) [
                    'name' => 'England',
                    'flag' => 'https://crests.football-data.org/770.svg',
                ],
                'emblem' => 'https://crests.football-data.org/PL.png',
                'lastUpdated' => '2022-03-20T08:58:54Z',
            ],
            (object) [
                'id' => '2014',
                'name' => 'Primera Division',
                'code' => 'PD',
                'area' => (object) [
                    'name' => 'Spain',
                    'flag' => 'https://crests.football-data.org/760.svg',
                ],
                'emblem' => 'https://crests.football-data.org/PD.png',
                'lastUpdated' => '2022-03-20T09:20:08Z',
            ],
        ];

        $expected = [
            new CompetitionDto(
                2021,
                'Premier League',
                'PL',
                'England',
                'https://crests.football-data.org/PL.png',
                new \DateTime('2022-03-20T08:58:54Z')
            ),
            new CompetitionDto(
                2014,
                'Primera Division',
                'PD',
                'Spain',
                'https://crests.football-data.org/PD.png',
                new \DateTime('2022-03-20T09:20:08Z')
            ),
        ];

        $result = CompetitionFactory::fromFootballDataNew($competitions);

        // Assert that the factory method returns an array of CompetitionDto objects
        $this->assertIsArray($result, 'Result should be an array');
        $this->assertContainsOnlyInstancesOf(CompetitionDto::class, $result, 'Result should only contain CompetitionDto objects');

        $this->assertEquals($expected, $result, 'Result does not match expected');
    }

    public function testFromFootballDataNewWithMissingEmblem(): void
    {
        $competitions = [
            (object) [
                'id' => 2021,
                'name' => 'Premier League',
                'code' => 'PL',
                'area' => (object) [
                    'name' => 'England',
                    'flag' => 'https://crests.football-data.org/770.svg',
                ],
                'emblem' => null,
                'lastUpdated' => '2022-03-20T08:58:54Z',
            ],
            (object) [
                'id' => '2014',
                'name' => 'Primera Division',
                'code' => 'PD',
                'area' => (object) [
                    'name' => 'Spain',
                    'flag' => 'https://crests.football-data.org/760.svg',
                ],
                'emblem' => null,
                'lastUpdated' => '2022-03-20T09:20:08Z',
            ],
        ];

        $expected = [
            new CompetitionDto(
                2021,
                'Premier League',
                'PL',
                'England',
                'https://crests.football-data.org/770.svg',
                new \DateTime('2022-03-20T08:58:54Z')
            ),
            new CompetitionDto(
                2014,
                'Primera Division',
                'PD',
                'Spain',
                'https://crests.football-data.org/760.svg',
                new \DateTime('2022-03-20T09:20:08Z')
            ),
        ];

        $result = CompetitionFactory::fromFootballDataNew($competitions);

        $this->assertEquals($expected, $result);
    }

    public function testFromFootballDataNewWithEmptyInput(): void
    {
        $competitions = [];
        $result = CompetitionFactory::fromFootballDataNew($competitions);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}
