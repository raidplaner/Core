<?php

namespace rp\system\user\notification\event;

use rp\data\event\Event;
use rp\data\event\EventAction;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\user\UserProfile;
use wcf\system\WCF;

/**
 * Provides a method to create a event for testing user notification
 * events.
 *
 * @author  Marco Daries
 * @package     Daries\RP\System\User\Notification\Object\Type
 */
trait TTestableEventUserNotificationEvent
{

    /**
     * Creates an test event.
     */
    public static function getTestEvent(UserProfile $author): Event
    {
        /** @var Event $event */
        return (new EventAction([], 'create', [
                'data' => [
                    'additionalData' => \serialize([
                        'timezone' => WCF::getUser()->getTimeZone()->getName(),
                    ]),
                    'enableComments' => 1,
                    'endTime' => TIME_NOW + (60 * 60 * 2),
                    'notes' => 'Test Notes',
                    'objectTypeID' => ObjectTypeCache::getInstance()->getObjectTypeIDByName('dev.daries.rp.eventController', 'dev.daries.rp.event.default'),
                    'startTime' => TIME_NOW,
                    'title' => 'Test Event',
                    'userID' => $author->userID,
                    'username' => $author->username,
                ],
                ]))->executeAction()['returnValues'];
    }
}
