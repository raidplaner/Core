<?php

namespace rp\data\raid;

use rp\data\character\Character;
use rp\data\character\CharacterProfile;
use rp\data\raid\event\RaidEvent;
use rp\data\raid\event\RaidEventCache;
use rp\system\cache\runtime\CharacterProfileRuntimeCache;
use wcf\data\DatabaseObject;
use wcf\data\ITitledLinkObject;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;


/**
 * Represents a raid.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @property-read   int         $raidID         unique id of the raid
 * @property-read   int         $raidEventID    raid event id of the raid
 * @property-read   int         $date           date of the raid event
 * @property-read   string      $addedBy        who added the raid
 * @property-read   string      $updatedBy      who updated the raid
 * @property-read   float       $points         points of the raid event
 * @property-read   string      $notes          notes of the raid event
 */
class Raid extends DatabaseObject implements IRouteController, ITitledLinkObject
{
    /**
     * attendees
     */
    protected ?array $attendees = null;

    /**
     * raid event object
     */
    protected ?RaidEvent $raidEvent = null;

    /**
     * Returns the attendees of the raid.
     */
    public function getAttendees(): array
    {
        if ($this->attendees === null) {
            $this->attendees = [];

            $sql = "SELECT  *
                    FROM    rp" . WCF_N . "_raid_attendee
                    WHERE   raidID = ?";
            $statement = WCF::getDB()->prepare($sql);
            $statement->execute([$this->raidID]);

            while ($row = $statement->fetchArray()) {
                $character = null;
                if ($row['characterID'] !== null) {
                    $character = CharacterProfileRuntimeCache::getInstance()->getObject($row['characterID']);
                }

                if ($character === null) {
                    $character = new CharacterProfile(
                        new Character(null, ['characterName' => $row['characterName']])
                    );
                }

                $character->classificationID = $row['classificationID'];
                $character->roleID = $row['roleID'];

                $this->attendees[] = $character;
            }
        }

        return $this->attendees;
    }

    /**
     * Returns the html code to display the icon.
     */
    public function getIcon(int $size): string
    {
        return $this->getRaidEvent()->getIcon($size);
    }

    /**
     * @inheritDoc
     */
    public function getLink(): string
    {
        return LinkHandler::getInstance()->getLink('Raid', [
                'application' => 'rp',
                'forceFrontend' => true,
                'object' => $this
        ]);
    }

    /**
     * Returns the raid event with the given raid event id.
     */
    public function getRaidEvent()
    {
        if ($this->raidEvent === null) {
            $this->raidEvent = RaidEventCache::getInstance()->getRaidEventByID($this->raidEventID);
        }

        return $this->raidEvent;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->getRaidEvent()->getTitle();
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->getTitle();
    }
}
