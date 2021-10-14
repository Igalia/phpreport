<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Phpreport\Web\services\HolidayService;

if (!defined('PHPREPORT_ROOT')) define('PHPREPORT_ROOT', __DIR__ . '/../../../');

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
            "2021-10-14",
            "2021-10-15",
            "2021-11-01"
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

    public function testGroupByWeeksWithDateInPastYearISOWeek(): void
    {
        // The first week of 2021 doesn't belong to the ISO week one of 2021,
        // instead it belongs to the last week (53) of 2020
        $dates = [
            "2021-01-01",
            "2021-01-02",
            "2021-11-01"
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
}
