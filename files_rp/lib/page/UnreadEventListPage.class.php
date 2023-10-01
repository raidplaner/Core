<?php

namespace rp\page;

use rp\data\event\UnreadEventList;
use wcf\page\MultipleLinkPage;


/**
 * Shows a list of unread events.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @property	UnreadEventList	$objectList
 */
class UnreadEventListPage extends MultipleLinkPage
{
    /**
     * @inheritDoc
     */
    public $neededPermissions = ['user.rp.canReadEvent'];

    /**
     * @inheritDoc
     */
    public $sortField = 'event.created';

    /**
     * @inheritDoc
     */
    public $sortOrder = 'DESC';

    /**
     * @inheritDoc
     */
    public $loginRequired = true;

    /**
     * @inheritDoc
     */
    public $objectListClassName = UnreadEventList::class;

}
