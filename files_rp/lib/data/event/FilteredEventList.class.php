<?php

namespace rp\data\event;


/**
 * Represents a filtered list of events.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class FilteredEventList extends AccessibleEventList
{
    /**
     * Events filtered by date
     */
    protected ?array $events = null;

    /**
     * Creates a new AccessibleEventList object.
     */
    public function __construct(int $start = 0, int $end = 0)
    {
        parent::__construct();

        if ($start && $end) {
            $this->getConditionBuilder()->add(
                '((event.startTime > ? AND event.startTime < ?) OR (event.startTime < ? AND event.endTime >= ?))',
                [$start, $end, $start, $start]
            );
        }
    }

    /**
     * Returns all events of the specified day.
     */
    public function getEventsByDay(string $day): array
    {
        $this->sortFilterEvents();

        return $this->events[$day] ?? [];
    }

    /**
     * @inheritDoc
     */
    public function readObjects(): void
    {
        parent::readObjects();

        $this->sortFilterEvents();
    }

    /**
     * Sorts the events by date
     */
    protected function sortFilterEvents(): void
    {
        if ($this->events === null) {
            $this->events = [];

            foreach ($this->objects as $event) {
                $days = $event->getEventDays();
                foreach ($days as $day) {
                    $index = $day->__toString();
                    if (!isset($this->events[$index])) $this->events[$index] = [];
                    $this->events[$index][] = $event;
                }
            }
        }
    }
}
