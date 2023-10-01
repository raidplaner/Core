<?php

namespace rp\system\user\notification\event;

use rp\data\event\Event;
use rp\data\event\EventAction;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\user\UserProfile;
use wcf\system\WCF;

/**
 *  Project:    Raidplaner: Core
 *  Package:    dev.daries.rp
 *  Link:       http://daries.dev
 *
 *  Copyright (C) 2018-2023 Daries.dev Developer Team
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Provides a method to create a event for testing user notification
 * events.
 *
 * @author      Marco Daries
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
