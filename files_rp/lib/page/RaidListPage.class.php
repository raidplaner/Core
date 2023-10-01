<?php

namespace rp\page;

use rp\data\raid\event\RaidEvent;
use rp\data\raid\event\RaidEventCache;
use rp\data\raid\RaidList;
use wcf\page\MultipleLinkPage;
use wcf\system\WCF;


/**
 * Shows the raids page.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class RaidListPage extends MultipleLinkPage
{
    /**
     * @inheritDoc
     */
    public $itemsPerPage = 60;

    /**
     * @inheritDoc
     */
    public $objectListClassName = RaidList::class;

    /**
     * raid event object
     */
    public ?RaidEvent $raidEvent = null;

    /**
     * @inheritDoc
     */
    public $sortField = 'date';

    /**
     * @inheritDoc
     */
    public $sortOrder = 'DESC';

    /**
     * @inheritDoc
     */
    public function assignVariables(): void
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'raidEvent' => $this->raidEvent,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function initObjectList(): void
    {
        parent::initObjectList();

        if ($this->raidEvent) {
            $this->objectList->getConditionBuilder()->add('raidEventID = ?', [$this->raidEvent->eventID]);
        }

        if (RP_ENABLE_ITEM) {
            $this->objectList->sqlSelects = "(
                SELECT  COUNT(*)
                FROM    rp" . WCF_N . "_item_to_raid
                WHERE   raidID = raid.raidID
            ) AS items";
        }
    }

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        if (isset($_REQUEST['raidEventID'])) {
            $raidEventID = \intval($_REQUEST['raidEventID']);
            $this->raidEvent = RaidEventCache::getInstance()->getRaidEventByID($raidEventID);
        }
    }
}
