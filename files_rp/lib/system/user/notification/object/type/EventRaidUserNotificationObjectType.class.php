<?php

namespace rp\system\user\notification\object\type;

use rp\data\event\Event;
use rp\data\event\EventList;
use rp\system\user\notification\object\EventRaidUserNotificationObject;
use wcf\system\user\notification\object\type\AbstractUserNotificationObjectType;

/**
 * Represents a event raid as a notification object type.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class EventRaidUserNotificationObjectType extends AbstractUserNotificationObjectType
{
    /**
     * @inheritDoc
     */
    protected static $decoratorClassName = EventRaidUserNotificationObject::class;

    /**
     * @inheritDoc
     */
    protected static $objectClassName = Event::class;

    /**
     * @inheritDoc
     */
    protected static $objectListClassName = EventList::class;

}
