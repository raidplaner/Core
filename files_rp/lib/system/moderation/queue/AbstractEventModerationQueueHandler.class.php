<?php

namespace rp\system\moderation\queue;

use rp\data\event\Event;
use rp\data\event\EventAction;
use rp\data\event\EventList;
use rp\data\event\ViewableEvent;
use rp\system\cache\runtime\EventRuntimeCache;
use wcf\data\moderation\queue\ModerationQueue;
use wcf\data\moderation\queue\ViewableModerationQueue;
use wcf\system\moderation\queue\AbstractModerationQueueHandler;
use wcf\system\moderation\queue\ModerationQueueManager;
use wcf\system\WCF;


/**
 * An abstract implementation of IModerationQueueHandler for events.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class AbstractEventModerationQueueHandler extends AbstractModerationQueueHandler
{
    /**
     * @inheritDoc
     */
    protected $className = Event::class;

    /**
     * @inheritDoc
     */
    protected $objectType = 'dev.daries.rp.event';

    /**
     * @inheritDoc
     */
    public function assignQueues(array $queues): void
    {
        $assignments = [];

        foreach ($queues as $queue) {
            $assignUser = false;

            if (WCF::getSession()->getPermission('mod.rp.canModerateEvent')) {
                $assignUser = true;
            }

            $assignments[$queue->queueID] = $assignUser;
        }

        ModerationQueueManager::getInstance()->setAssignment($assignments);
    }

    /**
     * @inheritDoc
     */
    public function getContainerID($objectID): int
    {
        return 0;
    }

    /**
     * Returns a event object by event id or `null` if event id is invalid.
     */
    protected function getEvent(int $objectID): ?Event
    {
        return EventRuntimeCache::getInstance()->getObject($objectID);
    }

    /**
     * Returns the parsed template for the target event.
     */
    protected function getRelatedContent(ViewableModerationQueue $queue): string
    {
        WCF::getTPL()->assign([
            'event' => new ViewableEvent($queue->getAffectedObject()),
        ]);

        return WCF::getTPL()->fetch('moderationEvent', 'rp');
    }

    /**
     * @inheritDoc
     */
    public function isValid($objectID): bool
    {
        if ($this->getEvent($objectID) === null) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function populate(array $queues): void
    {
        $objectIDs = [];
        foreach ($queues as $object) {
            $objectIDs[] = $object->objectID;
        }

        // fetch events
        $eventList = new EventList();
        $eventList->setObjectIDs($objectIDs);
        $eventList->readObjects();
        $events = $eventList->getObjects();

        foreach ($queues as $object) {
            if (isset($events[$object->objectID])) {
                $object->setAffectedObject($events[$object->objectID]);
            } else {
                $object->setIsOrphaned();
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function removeContent(ModerationQueue $queue, $message): void
    {
        if ($this->isValid($queue->objectID)) {
            $action = new EventAction([$this->getEvent($queue->objectID)], 'trash', ['data' => ['reason' => $message]]);
            $action->executeAction();
        }
    }
}
