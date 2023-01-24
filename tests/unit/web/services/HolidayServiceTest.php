<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Phpreport\Web\services\HolidayService;

if (!defined('PHPREPORT_ROOT')) define('PHPREPORT_ROOT', __DIR__ . '/../../../../');

require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

class HolidayServiceTest extends TestCase
{
    private \LoginManager $loginManagerMock;

    public function setUp(): void
    {
        $this->loginManagerMock = $this->createMock(\LoginManager::class);
        $this->instance = new HolidayService(
            $this->loginManagerMock
        );
    }

    public function testReturnEmptyArrayIfNoDatesGiven(): void
    {
        $dates = [];
        $expectedRanges = [];
        $this->assertEquals(
            $expectedRanges,
            $this->instance->datesToRanges($dates)
        );
    }

    public function testReturnRangeWithOnlySingleDate(): void
    {
        $dates = ['2021-01-02'];
        $expectedRanges = [
            ['start' => '2021-01-02', 'end' => '2021-01-02']
        ];

        $this->assertSame(
            $expectedRanges,
            $this->instance->datesToRanges($dates)
        );
    }

    public function testReturnRangesWithSpreadDates(): void
    {
        $dates = ['2021-01-02', '2021-05-10'];
        $expectedRanges = [
            ['start' => '2021-01-02', 'end' => '2021-01-02'],
            ['start' => '2021-05-10', 'end' => '2021-05-10']
        ];

        $this->assertSame(
            $expectedRanges,
            $this->instance->datesToRanges($dates)
        );
    }

    public function testReturnRangesWithCloseDates(): void
    {
        $dates = ['2021-01-02', '2021-01-03'];
        $expectedRanges = [
            ['start' => '2021-01-02', 'end' => '2021-01-03'],
        ];

        $this->assertSame(
            $expectedRanges,
            $this->instance->datesToRanges($dates)
        );
    }

    public function testReturnRangesWithMultipleCloseDates(): void
    {
        $dates = ['2021-01-02', '2021-01-03', '2021-01-04', '2021-01-06', '2021-01-07'];
        $expectedRanges = [
            ['start' => '2021-01-02', 'end' => '2021-01-04'],
            ['start' => '2021-01-06', 'end' => '2021-01-07'],
        ];

        $this->assertSame(
            $expectedRanges,
            $this->instance->datesToRanges($dates)
        );
    }

    public function testReturnRangesWithCloseAndSpreadDates(): void
    {
        $dates = ['2021-01-02', '2021-01-05', '2021-01-06', '2021-01-07', '2021-01-15'];
        $expectedRanges = [
            ['start' => '2021-01-02', 'end' => '2021-01-02'],
            ['start' => '2021-01-05', 'end' => '2021-01-07'],
            ['start' => '2021-01-15', 'end' => '2021-01-15'],
        ];

        $this->assertSame(
            $expectedRanges,
            $this->instance->datesToRanges($dates)
        );
    }

    public function testReturnTrueIfDateIsSaturday(): void
    {
        $date = '2021-09-25';
        $this->assertEquals(
            true,
            $this->instance->isWeekend($date)
        );
    }

    public function testReturnTrueIfDateIsSunday(): void
    {
        $date = '2021-09-26';
        $this->assertEquals(
            true,
            $this->instance->isWeekend($date)
        );
    }

    public function testReturnFalseIfDateIsNotOnWeekend(): void
    {
        $date = '2021-09-24';
        $this->assertEquals(
            false,
            $this->instance->isWeekend($date)
        );
    }

    public function testFormatHoursSplitDaysCorrectly(): void
    {
        $result = '7 d 05:00';
        $this->assertEquals(
            $result,
            $this->instance::formatHours(54, 7, 5)
        );
    }

    public function testFormatHoursWithRoundNumberOfDays(): void
    {
        $result = '14:00';
        $this->assertEquals(
            $result,
            $this->instance::formatHours(14, 7, 5)
        );
    }

    public function testFormatHoursWithRoundNumberOfDaysSmallLimit(): void
    {
        $result = '2 d 00:00';
        $this->assertEquals(
            $result,
            $this->instance::formatHours(14, 7, 1)
        );
    }

    public function testGroupByWeeksSameYear(): void
    {
        $dates = [
            "2021-10-14" => [
                "init" => 0,
                "end" => 420,
            ],
            "2021-10-15" => [
                "init" => 0,
                "end" => 420,
            ],
            "2021-11-01" => [
                "init" => 0,
                "end" => 420,
            ]
        ];
        $result = [
            '2021W41' => 2,
            '2021W44' => 1
        ];
        $this->assertEquals(
            $result,
            $this->instance::groupByWeeks($dates)
        );
    }

    public function testGroupByWeeksWithPartialLeaves(): void
    {
        $dates = [
            "2021-10-14" => [
                "init" => 0,
                "end" => 210,
                "amount" => 0.5
            ],
            "2021-10-15" => [
                "init" => 0,
                "end" => 420,
            ],
            "2021-11-01" => [
                "init" => 0,
                "end" => 420,
            ]
        ];
        $result = [
            '2021W41' => 1.5,
            '2021W44' => 1
        ];
        $this->assertEquals(
            $result,
            $this->instance::groupByWeeks($dates)
        );
    }

    public function testGroupByWeeksWithDateInPastYearISOWeek(): void
    {
        // The first week of 2021 doesn't belong to the ISO week one of 2021,
        // instead it belongs to the last week (53) of 2020
        $dates = [
            "2021-01-01" =>  [
                "init" => 0,
                "end" => 420,
            ],
            "2021-01-02" =>  [
                "init" => 0,
                "end" => 420,
            ],
            "2021-11-01" =>  [
                "init" => 0,
                "end" => 420,
            ],
        ];
        $result = [
            '2020W53' => 2,
            '2021W44' => 1
        ];
        $this->assertEquals(
            $result,
            $this->instance::groupByWeeks($dates)
        );
    }

    public function testGetWeeksFromYear(): void
    {
        // Should return all the ISO weeks of a year
        $expectedRanges = [
            '2020W53' => 0,
            '2021W01' => 0,
            '2021W02' => 0,
            '2021W03' => 0,
            '2021W04' => 0,
            '2021W05' => 0,
            '2021W06' => 0,
            '2021W07' => 0,
            '2021W08' => 0,
            '2021W09' => 0,
            '2021W10' => 0,
            '2021W11' => 0,
            '2021W12' => 0,
            '2021W13' => 0,
            '2021W14' => 0,
            '2021W15' => 0,
            '2021W16' => 0,
            '2021W17' => 0,
            '2021W18' => 0,
            '2021W19' => 0,
            '2021W20' => 0,
            '2021W21' => 0,
            '2021W22' => 0,
            '2021W23' => 0,
            '2021W24' => 0,
            '2021W25' => 0,
            '2021W26' => 0,
            '2021W27' => 0,
            '2021W28' => 0,
            '2021W29' => 0,
            '2021W30' => 0,
            '2021W31' => 0,
            '2021W32' => 0,
            '2021W33' => 0,
            '2021W34' => 0,
            '2021W35' => 0,
            '2021W36' => 0,
            '2021W37' => 0,
            '2021W38' => 0,
            '2021W39' => 0,
            '2021W40' => 0,
            '2021W41' => 0,
            '2021W42' => 0,
            '2021W43' => 0,
            '2021W44' => 0,
            '2021W45' => 0,
            '2021W46' => 0,
            '2021W47' => 0,
            '2021W48' => 0,
            '2021W49' => 0,
            '2021W50' => 0,
            '2021W51' => 0,
            '2021W52' => 0
        ];

        $this->assertSame(
            $expectedRanges,
            $this->instance->getWeeksFromYear(2021)
        );
    }

    public function testGetWeeksFromYearWithFirstWeekFromNextYear(): void
    {
        // The last week of 2024 is the first ISO week of 2025, so make sure
        // our method covers this case.
        $expectedRanges = [
            '2024W01' => 0,
            '2024W02' => 0,
            '2024W03' => 0,
            '2024W04' => 0,
            '2024W05' => 0,
            '2024W06' => 0,
            '2024W07' => 0,
            '2024W08' => 0,
            '2024W09' => 0,
            '2024W10' => 0,
            '2024W11' => 0,
            '2024W12' => 0,
            '2024W13' => 0,
            '2024W14' => 0,
            '2024W15' => 0,
            '2024W16' => 0,
            '2024W17' => 0,
            '2024W18' => 0,
            '2024W19' => 0,
            '2024W20' => 0,
            '2024W21' => 0,
            '2024W22' => 0,
            '2024W23' => 0,
            '2024W24' => 0,
            '2024W25' => 0,
            '2024W26' => 0,
            '2024W27' => 0,
            '2024W28' => 0,
            '2024W29' => 0,
            '2024W30' => 0,
            '2024W31' => 0,
            '2024W32' => 0,
            '2024W33' => 0,
            '2024W34' => 0,
            '2024W35' => 0,
            '2024W36' => 0,
            '2024W37' => 0,
            '2024W38' => 0,
            '2024W39' => 0,
            '2024W40' => 0,
            '2024W41' => 0,
            '2024W42' => 0,
            '2024W43' => 0,
            '2024W44' => 0,
            '2024W45' => 0,
            '2024W46' => 0,
            '2024W47' => 0,
            '2024W48' => 0,
            '2024W49' => 0,
            '2024W50' => 0,
            '2024W51' => 0,
            '2024W52' => 0,
            '2025W01' => 0
        ];

        $this->assertSame(
            $expectedRanges,
            $this->instance->getWeeksFromYear(2024)
        );
    }

    public function testGetDaysBetweenDates(): void
    {
        $expectedDates = ['2022-12-01', '2022-12-02', '2022-12-03', '2022-12-04', '2022-12-05'];

        $this->assertSame(
            $expectedDates,
            $this->instance->getDaysBetweenDates('2022-12-01', '2022-12-05')
        );
    }
}
