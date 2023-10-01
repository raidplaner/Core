<?php

namespace rp\acp\page;

use rp\data\event\legend\EventLegendList;
use wcf\page\MultipleLinkPage;


/**
 * Shows a list of event legends.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class EventLegendListPage extends MultipleLinkPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'rp.acp.menu.link.event.legend.list';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.rp.canManageEventLegend'];

    /**
     * @inheritDoc
     */
    public $objectListClassName = EventLegendList::class;

    /**
     * @inheritDoc
     */
    public $sqlOrderBy = 'name ASC, legendID ASC';

}
