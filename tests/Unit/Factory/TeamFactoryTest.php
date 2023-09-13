<?php

namespace App\Tests\Unit\Factory;

use App\Dto\TeamDto;
use App\Factory\TeamFactory;
use PHPUnit\Framework\TestCase;

class TeamFactoryTest extends TestCase
{
    /**
     * @dataProvider footballDataProvider
     *
     * @param TeamDto[]   $expected
     * @param \stdClass[] $teams
     */
    public function testFromFootballData(array $expected, array $teams): void
    {
        $result = TeamFactory::fromFootballData($teams);

        // Assert that the factory method returns an array of CompetitionDto objects
        $this->assertIsArray($result, 'Result should be an array');
        $this->assertContainsOnlyInstancesOf(TeamDto::class, $result, 'Result should only contain CompetitionDto objects');

        $this->assertEquals($expected, $result, 'Result does not match expected');
    }

    /**
     * @dataProvider footballDataNewProvider
     *
     * @param TeamDto[]   $expected
     * @param \stdClass[] $teams
     */
    public function testFromFootballDataNew(array $expected, array $teams): void
    {
        $result = TeamFactory::fromFootballDataNew($teams);

        // Assert that the factory method returns an array of CompetitionDto objects
        $this->assertIsArray($result, 'Result should be an array');
        $this->assertContainsOnlyInstancesOf(TeamDto::class, $result, 'Result should only contain CompetitionDto objects');

        $this->assertEquals($expected, $result, 'Result does not match expected');
    }

    public function footballDataProvider(): \Iterator
    {
        yield 'REGULAR_SEASON' => [
            [
                new TeamDto(
                    'https://crests.football-data.org/57.png',
                    'Arsenal FC',
                    'Arsenal',
                    'ARS',
                    1886,
                    'Red / White',
                    'Emirates Stadium'
                ),
                new TeamDto(
                    'https://crests.football-data.org/58.png',
                    'Aston Villa FC',
                    'Aston Villa',
                    'AVL',
                    1872,
                    'Claret / Sky Blue',
                    'Villa Park'
                ),
            ],
            [
                (object) [
                    'crestUrl' => 'https://crests.football-data.org/57.png',
                    'name' => 'Arsenal FC',
                    'shortName' => 'Arsenal',
                    'tla' => 'ARS',
                    'founded' => 1886,
                    'clubColors' => 'Red / White',
                    'venue' => 'Emirates Stadium',
                ],
                (object) [
                    'crestUrl' => 'https://crests.football-data.org/58.png',
                    'name' => 'Aston Villa FC',
                    'shortName' => 'Aston Villa',
                    'tla' => 'AVL',
                    'founded' => 1872,
                    'clubColors' => 'Claret / Sky Blue',
                    'venue' => 'Villa Park',
                ],
            ],
        ];
    }

    public function footballDataNewProvider(): \Iterator
    {
        yield 'REGULAR_SEASON' => [
            [
                new TeamDto(
                    'https://crests.football-data.org/57.png',
                    'Arsenal FC',
                    'Arsenal',
                    'ARS',
                    1886,
                    'Red / White',
                    'Emirates Stadium'
                ),
                new TeamDto(
                    'https://crests.football-data.org/58.png',
                    'Aston Villa FC',
                    'Aston Villa',
                    'AVL',
                    1872,
                    'Claret / Sky Blue',
                    'Villa Park'
                ),
            ],
            [
                (object) [
                    'crest' => 'https://crests.football-data.org/57.png',
                    'name' => 'Arsenal FC',
                    'shortName' => 'Arsenal',
                    'tla' => 'ARS',
                    'founded' => 1886,
                    'clubColors' => 'Red / White',
                    'venue' => 'Emirates Stadium',
                ],
                (object) [
                    'crest' => 'https://crests.football-data.org/58.png',
                    'name' => 'Aston Villa FC',
                    'shortName' => 'Aston Villa',
                    'tla' => 'AVL',
                    'founded' => 1872,
                    'clubColors' => 'Claret / Sky Blue',
                    'venue' => 'Villa Park',
                ],
            ],
        ];
    }
}
