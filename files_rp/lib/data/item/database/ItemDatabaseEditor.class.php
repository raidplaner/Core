<?php

namespace rp\data\item\database;

use wcf\data\DatabaseObjectEditor;


/**
 * Provides functions to edit item databases.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method static   ItemDatabase    create(array $parameters = [])
 * @method          ItemDatabase    getDecoratedObject()
 * @mixin           ItemDatabase
 */
class ItemDatabaseEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = ItemDatabase::class;

}
