<?php

namespace rp\data\raid\event;

use rp\system\cache\builder\RaidEventCacheBuilder;
use wcf\system\SingletonFactory;


/**
 * Manages the raid event cache.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class RaidEventCache extends SingletonFactory
{
    /**
     * cached raid events
     * @var RaidEvent[]
     */
    protected $cachedRaidEvents = [];

    /**
     * Returns the raid event with the given raid event id or `null` if no such raid event exists.
     */
    public function getRaidEventByID(int $raidEventID): ?RaidEvent
    {
        return $this->cachedRaidEvents[$raidEventID] ?? null;
    }

    /**
     * Returns all raid events.
     * 
     * @return	RaidEvent[]
     */
    public function getRaidEvents(): array
    {
        return $this->cachedRaidEvents;
    }

    /**
     * Returns the raid events with the given raid event ids.
     */
    public function getRaidEventsByIDs(array $raidEventIDs): array
    {
        $events = [];

        foreach ($raidEventIDs as $raidEventID) {
            $events[] = $this->getRaidEventByID($raidEventID);
        }

        return $events;
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $this->cachedRaidEvents = RaidEventCacheBuilder::getInstance()->getData(['gameID' => RP_DEFAULT_GAME_ID]);
    }
}
