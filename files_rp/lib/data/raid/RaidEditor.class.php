<?php

namespace rp\data\raid;

use rp\data\event\Event;
use rp\data\event\raid\attendee\EventRaidAttendee;
use rp\data\event\raid\attendee\EventRaidAttendeeList;
use rp\system\cache\runtime\CharacterRuntimeCache;
use wcf\data\DatabaseObjectEditor;
use wcf\system\event\EventHandler;
use wcf\system\WCF;


/**
 * Provides functions to edit raid.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method static   Raid    create(array $parameters = [])
 * @method          Raid    getDecoratedObject()
 * @mixin           Raid
 */
class RaidEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Raid::class;

    /**
     * Adds attendees to the raid.
     */
    public function addAttendees(array $attendeeIDs, bool $deleteOldAttendees = true, ?Event $event = null): void
    {
        // remove old attendees
        if ($deleteOldAttendees) {
            $sql = "DELETE FROM rp" . WCF_N . "_raid_attendee
                    WHERE       raidID = ?";
            $statement = WCF::getDB()->prepare($sql);
            $statement->execute([$this->raidID]);
        }

        // insert new attendees
        $attendees = [];
        if ($event !== null) {
            $attendeeList = new EventRaidAttendeeList();
            $attendeeList->getConditionBuilder()->add('eventID = ?', [$event->eventID]);
            $attendeeList->getConditionBuilder()->add('status = ?', [EventRaidAttendee::STATUS_CONFIRMED]);
            $attendeeList->readObjects();

            foreach ($attendeeList as $attendee) {
                if ($attendee->characterID === null) continue;
                $attendees[] = $attendee;
            }
        } else {
            $parameters = [
                'attendeeIDs' => $attendeeIDs,
                'attendees' => $attendees,
            ];
            EventHandler::getInstance()->fireAction($this, 'addAttendees', $parameters);
            $attendees = $parameters['attendees'];

            if (empty($attendees)) {
                $attendees = CharacterRuntimeCache::getInstance()->getObjects($attendeeIDs);
            }
        }

        if (empty($attendees)) return;

        $sql = "INSERT IGNORE INTO  rp" . WCF_N . "_raid_attendee
                                    (raidID, characterID, characterName, classificationID, roleID)
                VALUES              (?, ?, ?, ?, ?)";
        $statement = WCF::getDB()->prepare($sql);
        WCF::getDB()->beginTransaction();
        foreach ($attendees as $attendee) {
            $statement->execute([
                $this->raidID,
                $attendee->characterID,
                $attendee->characterName,
                $attendee->classificationID,
                $attendee->roleID
            ]);
        }
        WCF::getDB()->commitTransaction();
    }

    /**
     * Adds raid items to the raid.
     */
    public function addRaidItems(array $raidItems, bool $deleteOldRaidItems = true)
    {
        // remove olt raid items
        $sql = "DELETE FROM rp" . WCF_N . "_item_to_raid
                WHERE       raidID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$this->raidID]);

        // insert new raid items
        if (!empty($raidItems)) {
            $sql = "INSERT IGNORE INTO  rp" . WCF_N . "_item_to_raid
                                        (itemID, characterID, raidID, pointAccountID, points)
                    VALUES              (?, ?, ?, ?, ?)";
            $statement = WCF::getDB()->prepare($sql);
            foreach ($raidItems as $raidItem) {
                $statement->execute([
                    $raidItem['itemID'],
                    $raidItem['characterID'],
                    $this->raidID,
                    $raidItem['pointAccountID'],
                    $raidItem['points']
                ]);
            }
        }
    }
}
