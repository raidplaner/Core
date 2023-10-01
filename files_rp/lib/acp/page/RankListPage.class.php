<?php

namespace rp\acp\page;

use rp\data\rank\RankList;
use wcf\page\MultipleLinkPage;


/**
 * Shows the list of ranks.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @property    RankList    $objectList
 */
class RankListPage extends MultipleLinkPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'rp.acp.menu.link.rank.list';

    /**
     * @inheritDoc
     */
    public $neededModules = ['RP_ENABLE_RANK'];

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.rp.canManageRank'];

    /**
     * @inheritDoc
     */
    public $objectListClassName = RankList::class;

    /**
     * @inheritDoc
     */
    public $sqlOrderBy = 'showOrder ASC, rankID ASC';

    /**
     * @inheritDoc
     */
    protected function initObjectList(): void
    {
        parent::initObjectList();

        $this->objectList->getConditionBuilder()->add('rank.gameID = ?', [RP_DEFAULT_GAME_ID]);
    }
}
