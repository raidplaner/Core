<?php

namespace rp\data\race;

use wcf\data\AbstractDatabaseObjectAction;


/**
 * Executes race related actions.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @method      RaceEditor[]    getObjects()
 * @method      RaceEditor      getSingleObject()
 */
class RaceAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = RaceEditor::class;

    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['admin.rp.canManageGame'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.rp.canManageGame'];

    /**
     * @inheritDoc
     */
    protected $permissionsUpdate = ['admin.rp.canManageGame'];

    /**
     * @inheritDoc
     */
    protected $requireACP = ['create', 'delete', 'update'];

}
