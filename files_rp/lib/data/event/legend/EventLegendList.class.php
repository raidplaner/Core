<?php

namespace rp\data\event\legend;

use wcf\data\DatabaseObjectList;


/**
 * Represents a list of event legends.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      EventLegend         current()
 * @method      EventLegend[]       getObjects()
 * @method      EventLegend|null    search($objectID)
 * @property    EventLegend[]       $objects
 */
class EventLegendList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = EventLegend::class;

}
