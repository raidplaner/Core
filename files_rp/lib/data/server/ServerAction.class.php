<?php

namespace rp\data\server;

use wcf\data\AbstractDatabaseObjectAction;


/**
 * Executes server related actions.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @method      ServerEditor[]      getObjects()
 * @method      ServerEditor        getSingleObject()
 */
class ServerAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = ServerEditor::class;

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
