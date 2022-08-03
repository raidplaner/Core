<?php

namespace rp\system\importer;

use rp\data\event\Event;
use rp\data\event\EventEditor;
use rp\data\event\raid\attendee\EventRaidAttendeeEditor;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\cache\runtime\UserRuntimeCache;
use wcf\system\importer\AbstractImporter;
use wcf\system\importer\ImportHandler;
use wcf\system\search\SearchIndexManager;
use wcf\system\user\storage\UserStorageHandler;

/**
 *  Project:    Raidplaner: Core
 *  Package:    info.daries.rp
 *  Link:       http://daries.info
 *
 *  Copyright (C) 2018-2022 Daries.info Developer Team
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
 * Imports events.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\System\Importer
 */
class EventImporter extends AbstractImporter
{
    /**
     * @inheritDoc
     */
    protected $className = Event::class;

    /**
     * @inheritDoc
     */
    public function import($oldID, array $data, array $additionalData = []): mixed
    {
        // check old id
        if (\ctype_digit((string) $oldID)) {
            $event = new Event($oldID);
            if (!$event->eventID) $data['eventID'] = $oldID;
        }

        $data['userID'] = ImportHandler::getInstance()->getNewID('com.woltlab.wcf.user', $data['userID']);
        if ($data['userID']) {
            $user = UserRuntimeCache::getInstance()->getObject($data['userID']);
            $data['username'] = $user->username;
        }


        if (isset($data['raidID']) && $data['raidID'] !== null) {
            $data['raidID'] = ImportHandler::getInstance()->getNewID('info.daries.rp.raid', $data['raidID']);
        }
        if (isset($data['legendID']) && $data['legendID'] !== null) {
            $data['legendID'] = ImportHandler::getInstance()->getNewID('info.daries.rp.event.legend', $data['legendID']);
        }

        switch ($additionalData['objectType']) {
            case 'info.daries.rp.event.default':
                $data['objectTypeID'] = ObjectTypeCache::getInstance()->getObjectTypeIDByName('info.daries.rp.eventController', 'info.daries.rp.event.appointment');

                if (isset($data['additionalData']['participants'])) {
                    $appointments = [];

                    foreach ($data['additionalData']['participants'] as $status => $userIDs) {
                        $appointments[$status] = [];
                        foreach ($userIDs as $key => $userID) {
                            $userID = ImportHandler::getInstance()->getNewID('com.woltlab.wcf.user', $userID);
                            $appointments[$status][$key] = $userID;
                        }
                    }

                    $data['additionalData']['appointments'] = $appointments;
                    unset($data['additionalData']['participants']);
                }
                break;
            case 'info.daries.rp.event.raid':
                $data['objectTypeID'] = ObjectTypeCache::getInstance()->getObjectTypeIDByName('info.daries.rp.eventController', 'info.daries.rp.event.raid');

                $data['additionalData']['raidEventID'] = ImportHandler::getInstance()->getNewID('info.daries.rp.raid.event', $data['additionalData']['raidEventID']);

                foreach ($data['additionalData']['leaders'] as $key => $leaderID) {
                    $data['additionalData']['leaders'][$key] = ImportHandler::getInstance()->getNewID('info.daries.rp.character', $leaderID);
                }
                break;
        }

        $data['additionalData'] = \serialize($data['additionalData']);

        $event = EventEditor::create($data);

        if (isset($additionalData['attendees'])) {
            foreach ($additionalData['attendees'] as $attendee) {
                if ($attendee['characterID']) $attendee['characterID'] = ImportHandler::getInstance()->getNewID('info.daries.rp.character', $attendee['characterID']);
                $attendee['eventID'] = $event->eventID;
                $attendee['internID'] = $attendee['characterID'] ?? 0;

                EventRaidAttendeeEditor::create($attendee);
            }
        }

        SearchIndexManager::getInstance()->set(
            'info.daries.rp.event',
            $event->eventID,
            $event->notes,
            $event->getTitle(),
            $event->created,
            $event->userID,
            $event->username
        );

        ImportHandler::getInstance()->saveNewID('info.daries.rp.event', $oldID, $event->eventID);

        UserStorageHandler::getInstance()->resetAll('rpUnreadEvents');
        
        return $event->eventID;
    }
}
