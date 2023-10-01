<?php

namespace rp\data\item\database;

use wcf\data\AbstractDatabaseObjectAction;


/**
 * Executes item database-related actions.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      ItemDatabase            create()
 * @method      ItemDatabaseEditor[]    getObjects()
 * @method      ItemDatabaseEditor      getSingleObject()
 */
class ItemDatabaseAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = ItemDatabaseEditor::class;

}
