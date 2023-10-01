<?php

namespace rp\data\event\legend;

use wcf\data\AbstractDatabaseObjectAction;


/**
 * Executes event legend related actions.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @method      EventLegendEditor[] getObjects()
 * @method      EventLegendEditor   getSingleObject()
 */
class EventLegendAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = EventLegendEditor::class;

    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['admin.rp.canManageEventLegend'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.rp.canManageEventLegend'];

    /**
     * @inheritDoc
     */
    protected $permissionsUpdate = ['admin.rp.canManageEventLegend'];

    /**
     * @inheritDoc
     */
    protected $requireACP = ['create', 'delete', 'update'];

}
