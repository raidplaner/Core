<?php

namespace rp\data\role;

use wcf\data\AbstractDatabaseObjectAction;


/**
 * Executes role related actions.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @method      RoleEditor[]    getObjects()
 * @method      RoleEditor      getSingleObject()
 */
class RoleAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = RoleEditor::class;

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
