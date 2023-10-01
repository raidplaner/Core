<?php

namespace rp\data\event\legend;

use wcf\data\DatabaseObject;
use wcf\data\ITitledObject;


/**
 * Represents a event legend.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @property-read   int     $legendID       unique id of the event legend
 * @property-read   string  $name           name of the event legend
 * @property-read   string  $frontColor     front color of the event legend
 * @property-read   string  $bgColor        background color of the event legend
 */
class EventLegend extends DatabaseObject implements ITitledObject
{
    CONST TYPE_DEFAULT = -1;

    CONST TYPE_INDIVIDUAL = 0;

    CONST TYPE_SELECT = 2;

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->name;
    }
}
