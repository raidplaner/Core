<?php

namespace rp\data\item\database;

use wcf\data\DatabaseObject;


/**
 * Represents a item database.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @property-read   string      $databaseName   unique name and textual identifier of the item database
 * @property-read   int|null    $packageID      id of the package the which delivers the item database
 * @property-read   string      $className      name of the PHP class implementing `rp\system\item\database\IItemDatabase` handling search handled data
 */
class ItemDatabase extends DatabaseObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexName = 'databaseName';

    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexIsIdentity = false;

    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'item_database';

}
