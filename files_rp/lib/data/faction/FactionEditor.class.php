<?php

namespace rp\data\faction;

use rp\system\cache\builder\FactionCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;


/**
 * Provides functions to edit faction.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @method static   Faction     create(array $parameters = [])
 * @method          Faction     getDecoratedObject()
 * @mixin           Faction
 */
class FactionEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Faction::class;

    /**
     * @inheritDoc
     */
    public static function resetCache(): void
    {
        FactionCacheBuilder::getInstance()->reset();
    }
}
