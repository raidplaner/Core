<?php

namespace rp\data\item;

use rp\system\cache\builder\ItemCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;


/**
 * Provides functions to edit item.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method static   Item    ceate(array $parameters = [])
 * @method          Item    getDecoratedObject()
 * @mixin           Item
 */
class ItemEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Item::class;

    /**
     * @inheritDoc
     */
    public static function resetCache(): void
    {
        ItemCacheBuilder::getInstance()->reset();
    }
}
