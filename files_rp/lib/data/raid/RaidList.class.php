<?php

namespace rp\data\raid;

use wcf\data\DatabaseObjectList;


/**
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      Raid        current()
 * @method      Raid[]      getObjects()
 * @method      Raid|null   search($objectID)
 * @property	Raid[]      $objects
 */
class RaidList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Raid::class;

    /**
     * Returns timestamp of oldest raid fetched.
     */
    public function getLastRaidTime(): int
    {
        $lastRaidTime = 0;
        foreach ($this->objects as $raid) {
            if (!$lastRaidTime) {
                $lastRaidTime = $raid->date;
            }

            $lastRaidTime = \min($lastRaidTime, $raid->date);
        }

        return $lastRaidTime;
    }

    /**
     * Truncates the items in object list to given number of items.
     */
    public function truncate(int $limit): void
    {
        $this->objects = \array_slice($this->objects, 0, $limit, true);
        $this->indexToObject = \array_keys($this->objects);
    }
}
