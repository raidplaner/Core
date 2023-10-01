<?php

namespace rp\system\user\notification\object;

use rp\data\event\Event;
use wcf\data\DatabaseObjectDecorator;
use wcf\system\user\notification\object\IUserNotificationObject;

/**
 * Represents a event as a notification object type.
 *
 * @author  Marco Daries
 * @package     Daries\RP\System\User\Notification\Object
 *
 * @method      Event       getDecoratedObject()
 * @mixin       Event
 */
class EventRaidUserNotificationObject extends DatabaseObjectDecorator implements IUserNotificationObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Event::class;

    /**
     * @inheritDoc
     */
    public function getAuthorID(): int
    {
        return $this->getDecoratedObject()->userID;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->getDecoratedObject()->getTitle();
    }

    /**
     * @inheritDoc
     */
    public function getURL(): string
    {
        return $this->getDecoratedObject()->getLink();
    }
}
