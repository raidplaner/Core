<?php

namespace rp\data\event\legend;

use rp\system\cache\builder\EventLegendCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;


/**
 * Provides functions to edit event legend.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @method static   EventLegend     create(array $parameters = [])
 * @method          EventLegend     getDecoratedObject()
 * @mixin           EventLegend
 */
class EventLegendEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = EventLegend::class;

    /**
     * @inheritDoc
     */
    public static function resetCache(): void
    {
        EventLegendCacheBuilder::getInstance()->reset();
    }
}
