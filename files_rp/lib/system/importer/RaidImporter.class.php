<?php

namespace rp\system\importer;

use rp\data\raid\Raid;
use rp\data\raid\RaidEditor;
use wcf\system\importer\AbstractImporter;
use wcf\system\importer\ImportHandler;
use wcf\system\WCF;

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
 * Imports raids.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\System\Importer
 */
class RaidImporter extends AbstractImporter
{
    /**
     * @inheritDoc
     */
    protected $className = Raid::class;

    /**
     * @inheritDoc
     */
    public function import($oldID, array $data, array $additionalData = []): mixed
    {
        $data['raidEventID'] = ImportHandler::getInstance()->getNewID('info.daries.rp.raid.event', $data['raidEventID']);

        // check old id
        if (\ctype_digit((string) $oldID)) {
            $raid = new Raid($oldID);
            if (!$raid->raidID) $data['raidID'] = $oldID;
        }

        $raid = RaidEditor::create($data);

        $attendees = $additionalData['attendees'] ?? [];
        if (!empty($attendees)) {
            $sql = "INSERT INTO rp" . WCF_N . "_raid_attendee
                                (raidID, characterID, characterName, classificationID, roleID)
                    VALUES      (?, ?, ?, ?, ?)";
            $statement = WCF::getDB()->prepareStatement($sql);
            WCF::getDB()->beginTransaction();
            foreach ($attendees as $attendee) {
                $statement->execute([
                    $raid->raidID,
                    ImportHandler::getInstance()->getNewID('info.daries.rp.character', $attendee['characterID']),
                    $attendee['characterName'],
                    $attendee['classificationID'],
                    $attendee['roleID'],
                ]);
            }
            WCF::getDB()->commitTransaction();
        }
        
        ImportHandler::getInstance()->saveNewID('info.daries.rp.raid', $oldID, $raid->raidID);
        
        return $raid->raidID;
    }
}
