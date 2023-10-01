<?php

namespace rp\system\importer;

use rp\data\raid\Raid;
use rp\data\raid\RaidEditor;
use wcf\system\importer\AbstractImporter;
use wcf\system\importer\ImportHandler;
use wcf\system\WCF;


/**
 * Imports raids.
 * 
 * @author  Marco Daries
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
        $data['raidEventID'] = ImportHandler::getInstance()->getNewID('dev.daries.rp.raid.event', $data['raidEventID']);

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
                    ImportHandler::getInstance()->getNewID('dev.daries.rp.character', $attendee['characterID']),
                    $attendee['characterName'],
                    $attendee['classificationID'],
                    $attendee['roleID'],
                ]);
            }
            WCF::getDB()->commitTransaction();
        }
        
        ImportHandler::getInstance()->saveNewID('dev.daries.rp.raid', $oldID, $raid->raidID);
        
        return $raid->raidID;
    }
}
