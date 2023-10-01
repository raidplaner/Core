<?php

namespace rp\acp\page;

use rp\data\point\account\PointAccountList;
use wcf\page\MultipleLinkPage;


/**
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @property    PointAccountList    $objectList
 */
class PointAccountListPage extends MultipleLinkPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'rp.acp.menu.link.point.account.list';

    /**
     * @inheritDoc
     */
    public $neededModules = ['RP_ITEM_ACCOUNT_EASYMODE_DISABLED'];

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.rp.canManagePointAccount'];

    /**
     * @inheritDoc
     */
    public $objectListClassName = PointAccountList::class;

    /**
     * @inheritDoc
     */
    public $sqlOrderBy = 'showOrder ASC, pointAccountID ASC';

}
