<?php

namespace rp\system\importer;

use wcf\data\object\type\ObjectTypeCache;
use wcf\system\importer\AbstractCommentImporter;
use wcf\system\importer\ImportHandler;


/**
 * Imports event comments.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class EventCommentImporter extends AbstractCommentImporter
{
    /**
     * @inheritDoc
     */
    protected $objectTypeName = 'dev.daries.rp.event.comment';

    /**
     * Creates a new EventCommentImporter object.
     */
    public function __construct()
    {
        $objectType = ObjectTypeCache::getInstance()
            ->getObjectTypeByName('com.woltlab.wcf.comment.commentableContent', 'dev.daries.rp.eventComment');
        $this->objectTypeID = $objectType->objectTypeID;
    }

    /**
     * @inheritDoc
     */
    public function import($oldID, array $data, array $additionalData = []): mixed
    {
        $eventID = ImportHandler::getInstance()->getNewID('dev.daries.rp.event', $data['objectID'] ?? $additionalData['eventID']);
        if (!$eventID) return 0;
        $data['objectID'] = $eventID;

        return parent::import($oldID, $data);
    }
}
