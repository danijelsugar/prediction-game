<?php

namespace App\Tests\Unit\Factory;

use App\Dto\HeadToHeadDto;
use App\Factory\HeadToHeadFactory;
use PHPUnit\Framework\TestCase;

class HeadToHeadFactoryTest extends TestCase
{
    /**
     * @dataProvider headToHeadProvider
     */
    public function testFromFootballData(HeadToHeadDto $expected, \stdClass $headToHead): void
    {
        $result = HeadToHeadFactory::fromFootballData($headToHead);

        $this->assertInstanceOf(HeadToHeadDto::class, $result, 'Result is not an instance of HeadToHeadDto');

        $this->assertEquals($expected, $result, 'Result does not match expected');
    }

    /**
     * @dataProvider headToHeadProvider
     */
    public function testFromFootballDataNew(HeadToHeadDto $expected, \stdClass $headToHead): void
    {
        $result = HeadToHeadFactory::fromFootballDataNew($headToHead);

        $this->assertInstanceOf(HeadToHeadDto::class, $result, 'Result is not an instance of HeadToHeadDto');

        $this->assertEquals($expected, $result, 'Result does not match expected');
    }

    public function headToHeadProvider(): \iterator
    {
        yield [
            new HeadToHeadDto(
                10,
                28,
                4,
                2,
                4,
                4,
                2,
                4
            ),
            (object) [
                'numberOfMatches' => 10,
                'totalGoals' => 28,
                'homeTeam' => (object) [
                    'wins' => 4,
                    'draws' => 2,
                    'losses' => 4,
                ],
                'awayTeam' => (object) [
                    'wins' => 4,
                    'draws' => 2,
                    'losses' => 4,
                ],
            ],
        ];
        yield [
            new HeadToHeadDto(
                10,
                30,
                6,
                1,
                3,
                3,
                1,
                6
            ),
            (object) [
                'numberOfMatches' => 10,
                'totalGoals' => 30,
                'homeTeam' => (object) [
                    'wins' => 6,
                    'draws' => 1,
                    'losses' => 3,
                ],
                'awayTeam' => (object) [
                    'wins' => 3,
                    'draws' => 1,
                    'losses' => 6,
                ],
            ],
        ];
    }
}
