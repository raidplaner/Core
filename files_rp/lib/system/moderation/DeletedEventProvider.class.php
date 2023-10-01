<?php

namespace rp\system\moderation;

use rp\data\event\DeletedEventList;
use wcf\system\moderation\AbstractDeletedContentProvider;


/**
 * Implementation of IDeletedContentProvider for deleted events.
 * 
 * @author  Marco Daries
 * @package     Daries\RP\System\Moderation
 */
class DeletedEventProvider extends AbstractDeletedContentProvider
{

    /**
     * @inheritDoc
     */
    public function getObjectList(): DeletedEventList
    {
        $eventList = new DeletedEventList();
        $eventList->sqlOrderBy = "event.deleteTime DESC, event.eventID DESC";

        return $eventList;
    }

    /**
     * @inheritDoc
     */
    public function getTemplateName(): string
    {
        return 'deletedEventList';
    }
}
