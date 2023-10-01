<?php

namespace rp\system\calendar;

use wcf\system\request\LinkHandler;


/**
 * Displays a specific month.
 * 
 * @author  Marco Daries
 * @package     Daries\RP\System\Calendar
 */
final class Month
{
    /**
     * days objects of this month
     * @var Day[]
     */
    private ?array $days = null;

    /**
     * month
     */
    private int $month = 0;

    /**
     * name of this month
     */
    private ?string $name = null;

    /**
     * next month
     */
    private ?Month $nextMonth = null;

    /**
     * number of days
     */
    private ?int $numberOfDays = null;

    /**
     * previous month
     */
    private ?Month $previousMonth = null;

    /**
     * year
     */
    private int $year = 0;

    /**
     * Returns days of this month
     * 
     * @return Day[]
     */
    public function getDays(): array
    {
        if ($this->days === null) {
            for ($i = 1; $i <= $this->getNumberOfDays(); $i++) {
                $this->days[] = new Day($i, $this);
            }
        }

        return $this->days;
    }

    /**
     * Returns the first day of the month.
     */
    public function getFirstDayOfMonth(): Day
    {
        return $this->getDays()[0];
    }

    /**
     * Returns the last day of the month.
     */
    public function getLastDayOfMonth(): Day
    {
        return $this->getDays()[$this->getNumberOfDays() - 1];
    }

    /**
     * Returns the link to the month
     */
    public function getLink(): string
    {
        return LinkHandler::getInstance()->getLink('Calendar', [
                'application' => 'rp',
                'month' => $this->getMonth(),
                'year' => $this->getYear()
        ]);
    }

    /**
     * Returns the month
     */
    public function getMonth(): int
    {
        return $this->month;
    }

    /**
     * Returns the name of the month.
     */
    public function getName(): string
    {
        if ($this->name === null) {
            $date = new \DateTime();
            $date->setDate($this->getYear(), $this->getMonth(), 1);
            $this->name = \strtolower($date->format('F'));
        }

        return $this->name;
    }

    /**
     * Returns the next month
     */
    public function getNextMonth(): Month
    {
        if ($this->nextMonth === null) {
            if ($this->month == 12) {
                $this->nextMonth = new self(1, $this->year + 1);
            } else {
                $this->nextMonth = new self($this->month + 1, $this->year);
            }
        }

        return $this->nextMonth;
    }

    /**
     * Returns the link to the next month
     */
    public function getNextMonthLink(): string
    {
        return LinkHandler::getInstance()->getLink('Calendar', [
                'application' => 'rp',
                'month' => $this->getNextMonth()->getMonth(),
                'year' => $this->getNextMonth()->getYear()
        ]);
    }

    /**
     * Returns number of days in this month
     */
    public function getNumberOfDays(): int
    {
        if ($this->numberOfDays === null) {
            $date = new \DateTime();
            $date->setDate($this->getYear(), $this->getMonth(), 1);
            $this->numberOfDays = $date->format('t');
        }

        return $this->numberOfDays;
    }

    /**
     * Returns the previous month
     */
    public function getPreviousMonth(): Month
    {
        if ($this->previousMonth === null) {
            if ($this->month == 1) {
                $this->previousMonth = new self(12, $this->year - 1);
            } else {
                $this->previousMonth = new self($this->month - 1, $this->year);
            }
        }

        return $this->previousMonth;
    }

    /**
     * Returns the link to the last month
     */
    public function getPreviousMonthLink(): string
    {
        return LinkHandler::getInstance()->getLink('Calendar', [
                'application' => 'rp',
                'month' => $this->getPreviousMonth()->getMonth(),
                'year' => $this->getPreviousMonth()->getYear()
        ]);
    }

    /**
     * Returns the year
     */
    public function getYear(): int
    {
        return $this->year;
    }

    public function __construct(int $month, int $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function __toString()
    {
        return $this->getYear() . '-' . ($this->getMonth() < 10 ? '0' : '') . $this->getMonth();
    }
}
