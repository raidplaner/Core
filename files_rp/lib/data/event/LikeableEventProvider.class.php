<?php

namespace rp\data\event;

use wcf\data\like\ILikeObjectTypeProvider;
use wcf\data\like\object\ILikeObject;
use wcf\data\object\type\AbstractObjectTypeProvider;
use wcf\system\like\IViewableLikeProvider;
use wcf\system\WCF;


/**
 * Like Object type provider for events.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      LikeableEvent       getObjectByID($objectID)
 * @method      LikeableEvent[]     getObjectsByIDs(array $objectIDs)
 */
class LikeableEventProvider extends AbstractObjectTypeProvider implements
ILikeObjectTypeProvider, IViewableLikeProvider
{
    /**
     * @inheritDoc
     */
    public $className = Event::class;

    /**
     * @inheritDoc
     */
    public $decoratorClassName = LikeableEvent::class;

    /**
     * @inheritDoc
     */
    public $listClassName = EventList::class;

    /**
     * @inheritDoc
     */
    public function checkPermissions(ILikeObject $object): bool
    {
        /** @var LikeableEvent $object */
        return $object->eventID && $object->canRead();
    }

    /**
     * @inheritDoc
     */
    public function prepare(array $likes): void
    {
        $eventIDs = [];
        foreach ($likes as $like) {
            $eventIDs[] = $like->objectID;
        }

        // fetch events
        $eventList = new ViewableEventList();
        $eventList->setObjectIDs($eventIDs);
        $eventList->readObjects();
        $events = $eventList->getObjects();

        // set message
        foreach ($likes as $like) {
            if (isset($events[$like->objectID])) {
                $event = $events[$like->objectID];

                // check permissions
                if (!$event->canRead()) {
                    continue;
                }
                $like->setIsAccessible();

                // short output
                $text = WCF::getLanguage()->getDynamicVariable('wcf.like.title.dev.daries.rp.likeableEvent', [
                    'event' => $event,
                    'reaction' => $like,
                ]);
                $like->setTitle($text);
            }
        }
    }
}
