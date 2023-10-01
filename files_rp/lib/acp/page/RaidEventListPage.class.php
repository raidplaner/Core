<?php

namespace rp\acp\page;

use rp\data\raid\event\I18nRaidEventList;
use wcf\page\SortablePage;


/**
 * Shows a list of raid events.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @property	I18nRaidEventList   $objectList
 */
class RaidEventListPage extends SortablePage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'rp.acp.menu.link.raid.event.list';

    /**
     * @inheritDoc
     */
    public $defaultSortField = 'eventNameI18n';

    /**
     * @inheritDoc
     */
    public $defaultSortOrder = 'ASC';

    /**
     * @inheritDoc
     */
    public $itemsPerPage = 50;

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.rp.canManageRaidEvent'];

    /**
     * @inheritDoc
     */
    public $objectListClassName = I18nRaidEventList::class;

    /**
     * @inheritDoc
     */
    public $validSortFields = ['defaultPoints', 'pointAccountName', 'eventID', 'eventNameI18n'];

    /**
     * @inheritDoc
     */
    protected function initObjectList(): void
    {
        parent::initObjectList();

        if (!empty($this->objectList->sqlSelects)) {
            $this->objectList->sqlSelects .= ',';
        }
        $this->objectList->sqlSelects .= 'point_account.pointAccountName';
        $this->objectList->sqlJoins .= " LEFT JOIN rp" . WCF_N . "_point_account point_account ON (point_account.pointAccountID = raid_event.pointAccountID)";
    }
}
