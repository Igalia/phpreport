<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

if (!defined('PHPREPORT_ROOT')) define('PHPREPORT_ROOT', __DIR__ . '/../../../../');

include_once(PHPREPORT_ROOT . '/model/vo/JourneyHistoryVO.php');

class JourneyHistoryVOTest extends TestCase
{
    public function setUp(): void
    {
        $this->instance = new \JourneyHistoryVO();
        $this->instance->setJourney(5);
        $this->instance->setUserId(1);
        $this->instance->setInitDate(date_create("2021-01-01"));
        $this->instance->setEndDate(date_create("2021-06-01"));
    }

    public function testReturnFalseIfDateIsBeforeJourney(): void
    {
        $date = date_create("2020-01-01");
        $this->assertEquals(
            false,
            $this->instance->dateBelongsToHistory($date)
        );
    }

    public function testReturnTrueIfDateBelongsToJourney(): void
    {
        $date = date_create("2021-05-01");
        $this->assertEquals(
            true,
            $this->instance->dateBelongsToHistory($date)
        );
    }

    public function testReturnTrueIfDateEqualsInitOfJourney(): void
    {
        $date = date_create("2021-01-01");
        $this->assertEquals(
            true,
            $this->instance->dateBelongsToHistory($date)
        );
    }

    public function testReturnTrueIfDateEqualsEndOfJourney(): void
    {
        $date = date_create("2021-06-01");
        $this->assertEquals(
            true,
            $this->instance->dateBelongsToHistory($date)
        );
    }

    public function testReturnFalseIfDateIsAfterJourney(): void
    {
        $date = date_create("2021-07-01");
        $this->assertEquals(
            false,
            $this->instance->dateBelongsToHistory($date)
        );
    }
}
