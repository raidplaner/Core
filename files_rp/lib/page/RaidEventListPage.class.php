<?php

namespace rp\page;

use rp\data\raid\event\I18nRaidEventList;
use wcf\page\MultipleLinkPage;


/**
 * Shows the raid events page.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class RaidEventListPage extends MultipleLinkPage
{
    /**
     * @inheritDoc
     */
    public $itemsPerPage = 60;

    /**
     * @inheritDoc
     */
    public $objectListClassName = I18nRaidEventList::class;

    /**
     * @inheritDoc
     */
    public $sortField = 'eventNameI18n';

    /**
     * @inheritDoc
     */
    public $sortOrder = 'ASC';

}
