<?php

namespace rp\data\race;

use rp\system\cache\builder\RaceCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;


/**
 * Provides functions to edit race.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @method static   Race    create(array $parameters = [])
 * @method          Race    getDecoratedObject()
 * @mixin           Race
 */
class RaceEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Race::class;

    /**
     * @inheritDoc
     */
    public static function resetCache(): void
    {
        RaceCacheBuilder::getInstance()->reset();
    }
}
