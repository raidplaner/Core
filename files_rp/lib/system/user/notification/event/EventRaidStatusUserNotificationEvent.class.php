<?php

namespace rp\system\user\notification\event;

use rp\system\user\notification\object\EventRaidUserNotificationObject;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;


/**
 * @author  Marco Daries
 * @package     Daries\RP\System\User\Notification\Object\Type
 *
 * @method      EventRaidUserNotificationObject     getUserNotificationObject()
 */
class EventRaidStatusUserNotificationEvent extends AbstractUserNotificationEvent
{

    /**
     * @inheritDoc
     */
    public function checkAccess(): bool
    {
        return $this->getUserNotificationObject()->canRead();
    }

    /**
     * @inheritDoc
     */
    public function getEventHash(): string
    {
        return \sha1($this->eventID . '-' . TIME_NOW);
    }

    /**
     * @inheritDoc
     */
    public function getLink(): string
    {
        return $this->getUserNotificationObject()->getLink();
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): string
    {
        return $this->getLanguage()->getDynamicVariable('rp.user.notification.raid.event.status.message', [
                'author' => $this->author,
                'event' => $this->userNotificationObject,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->getLanguage()->get('rp.user.notification.raid.event.status.title');
    }

    /**
     * @inheritDoc
     */
    public function supportsEmailNotification(): bool
    {
        return false;
    }
}
