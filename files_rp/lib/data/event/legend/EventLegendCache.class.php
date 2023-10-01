<?php

namespace rp\data\event\legend;

use rp\system\cache\builder\EventLegendCacheBuilder;
use wcf\system\SingletonFactory;


/**
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class EventLegendCache extends SingletonFactory
{
    /**
     * cached event legends
     * @var EventLegend[]
     */
    protected array $cached = [];

    /**
     * Returns the event legend with the given event legend id or `null` if no such event legend exists.
     */
    public function getLegendByID(int $legendID): ?EventLegend
    {
        return $this->cached[$legendID] ?? null;
    }

    /**
     * Returns all event legends.
     * 
     * @return  EventLegend[]
     */
    public function getLegends(): array
    {
        return $this->cached;
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $this->cached = EventLegendCacheBuilder::getInstance()->getData();
    }
}
