<?php

namespace rp\data\event;

use rp\data\modification\log\EventModificationLogList;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\message\embedded\object\MessageEmbeddedObjectManager;
use wcf\system\visitTracker\VisitTracker;
use wcf\system\WCF;


/**
 * Represents a list of events.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      ViewableEvent           current()
 * @method      ViewableEvent[]         getObjects()
 * @method      ViewableEvent|null      getSingleObject()
 * @method      ViewableEvent|null      search($objectID)
 * @property    ViewableEvent[]         $objects
 */
class ViewableEventList extends EventList
{
    /**
     * @inheritDoc
     */
    public $decoratorClassName = ViewableEvent::class;

    /**
     * ids of events with embedded objects
     * @var int[]
     */
    public array $embeddedObjectEventIDs = [];

    /**
     * enables/disables the loading of embedded objects
     */
    protected bool $embeddedObjectLoading = true;

    /**
     * load delete notes
     */
    public bool $loadDeleteNote = true;

    /**
     * Enables/disables the loading of embedded objects.
     */
    public function enableEmbeddedObjectLoading(bool $enable = true): void
    {
        $this->embeddedObjectLoading = $enable;
    }

    /**
     * Reads the embedded objects of the posts in the list.
     */
    public function readEmbeddedObjects(): void
    {
        if (!empty($this->embeddedObjectEventIDs)) {
            // load embedded objects
            MessageEmbeddedObjectManager::getInstance()
                ->loadObjects('dev.daries.rp.event.notes', $this->embeddedObjectEventIDs);
        }
    }

    /**
     * @inheritDoc
     */
    public function readObjects(): void
    {
        parent::readObjects();

        $userIDs = $eventIDs = [];
        foreach ($this->objects as $event) {
            if ($event->userID) {
                $userIDs[] = $event->userID;
            }
        }

        // cache user profiles
        if (!empty($userIDs)) {
            UserProfileRuntimeCache::getInstance()->cacheObjectIDs($userIDs);
        }

        if ($this->loadDeleteNote) {
            $objectIDs = [];
            foreach ($this->objects as $object) {
                if ($object->isDeleted) {
                    $objectIDs[] = $object->eventID;
                }
                if ($object->hasEmbeddedObjects) {
                    $this->embeddedObjectEventIDs[] = $object->eventID;
                }
            }

            // load deletion data
            if (!empty($objectIDs)) {
                $logList = new EventModificationLogList();
                $logList->setEventData($objectIDs, 'trash');
                $logList->readObjects();

                foreach ($logList as $logEntry) {
                    /** @var ViewableEvent $object */
                    foreach ($this->objects as $object) {
                        if ($object->eventID == $logEntry->objectID) {
                            $object->setLogEntry($logEntry);
                            $logEntry->setEvent($object->getDecoratedObject());
                        }
                    }
                }
            }
        }

        if ($this->embeddedObjectLoading) {
            $this->readEmbeddedObjects();
        }
    }

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();

        if (WCF::getUser()->userID != 0) {
            // last visit time
            if (!empty($this->sqlSelects)) $this->sqlSelects .= ',';
            $this->sqlSelects .= 'tracked_visit.visitTime';
            $this->sqlJoins .= "
                LEFT JOIN   wcf" . WCF_N . "_tracked_visit tracked_visit
                ON          tracked_visit.objectTypeID = " . VisitTracker::getInstance()->getObjectTypeID('dev.daries.rp.event') . "
                        AND tracked_visit.objectID = event.eventID
                        AND tracked_visit.userID = " . WCF::getUser()->userID;
        }
    }
}
