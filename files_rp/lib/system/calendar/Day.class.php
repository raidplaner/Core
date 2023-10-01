<?php

namespace rp\system\calendar;

use wcf\util\DateUtil;


/**
 * Displays a specific day in the month.
 * 
 * @author  Marco Daries
 * @package     Daries\RP\System\Calendar
 */
final class Day
{
    /**
     * day
     */
    protected int $day = 0;

    /**
     * day of the week
     */
    protected ?int $dayOfTheWeek = null;

    /**
     * month object
     */
    protected Month $month;

    /**
     * next day
     */
    protected ?Day $nextDay = null;

    /**
     * previous day
     */
    protected ?Day $previousDay = null;

    /**
     * Week number of the day
     */
    protected ?int $week = null;

    /**
     * Returns the day.
     */
    public function getDay(): int
    {
        return $this->day;
    }

    /**
     * Returns the day in the week
     */
    public function getDayOfTheWeek(): int
    {
        if ($this->dayOfTheWeek === null) {
            $date = new \DateTime();
            $date->setDate($this->getMonth()->getYear(), $this->month->getMonth(), $this->getDay());
            $this->dayOfTheWeek = $date->format('w');
        }

        return $this->dayOfTheWeek;
    }

    /**
     * Returns the current month object of the tag.
     */
    public function getMonth(): Month
    {
        return $this->month;
    }

    /**
     * Returns the next day.
     */
    public function getNextDay(): Day
    {
        if ($this->nextDay === null) {
            if ($this->isLastDayOfMonth()) {
                $this->nextDay = $this->getMonth()->getNextMonth()->getFirstDayOfMonth();
            } else {
                $this->nextDay = new Day($this->getDay() + 1, $this->getMonth());
            }
        }

        return $this->nextDay;
    }

    /**
     * Returns the previous day.
     */
    public function getPreviousDay(): Day
    {
        if ($this->previousDay === null) {
            if ($this->isFirstDayOfMonth()) {
                $this->previousDay = $this->getMonth()->getPreviousMonth()->getLastDayOfMonth();
            } else {
                $this->previousDay = new Day($this->getDay() - 1, $this->getMonth());
            }
        }

        return $this->previousDay;
    }

    public function getWeek(): int
    {
        if ($this->week === null) {
            $date = new \DateTime();
            $date->setDate($this->getMonth()->getYear(), $this->month->getMonth(), $this->getDay());
            $this->week = $date->format('W');
        }

        return $this->week;
    }

    /**
     * Returns true if the given day is equal.
     */
    public function isCurrentDay(Day $day): bool
    {
        if ($this->__toString() == $day->__toString()) return true;
        return false;
    }

    /**
     * Returns true, if the day is the first day of the month.
     */
    public function isFirstDayOfMonth(): bool
    {
        return $this->getDay() == 1;
    }

    /**
     * Returns true, if the day is the first day of the week.
     */
    public function isFirstDayOfWeek(): bool
    {
        return $this->getDayOfTheWeek() == DateUtil::getFirstDayOfTheWeek();
    }

    /**
     * Returns true, if the day is the last day in the month.
     */
    public function isLastDayOfMonth(): bool
    {
        return $this->getDay() == $this->getMonth()->getLastDayOfMonth()->getDay();
    }

    /**
     * Returns true, if the day is the last day in the week.
     */
    public function isLastDayOfWeek(): bool
    {
        return $this->getDayOfTheWeek() == (DateUtil::getFirstDayOfTheWeek() == 1 ? 0 : 6);
    }

    public function __construct(int $day, Month $month)
    {
        $this->day = $day;
        $this->month = $month;
    }

    public function __toString()
    {
        return $this->getMonth()->getYear() . '-' . ($this->getMonth()->getMonth() < 10 ? '0' : '') . $this->getMonth()->getMonth() . '-' . ($this->getDay() < 10 ? '0' : '') . $this->getDay();
    }
}
