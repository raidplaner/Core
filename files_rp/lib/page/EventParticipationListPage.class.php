<?php

namespace rp\page;

use rp\data\event\AccessibleEventList;
use rp\data\event\raid\attendee\EventRaidAttendeeList;
use wcf\data\object\type\ObjectTypeCache;
use wcf\page\AbstractPage;
use wcf\page\MultipleLinkPage;
use wcf\system\event\EventHandler;
use wcf\system\WCF;


/**
 * Shows a list of event participations.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class EventParticipationListPage extends MultipleLinkPage
{
    /**
     * events shown
     */
    public array $events = [];

    /**
     * @inheritDoc
     */
    public $loginRequired = true;

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['user.rp.canReadEvent'];

    /**
     * @inheritDoc
     */
    public $sortField = 'startTime';

    /**
     * @inheritDoc
     */
    public $sortOrder = 'ASC';

    /**
     * @inheritDoc
     */
    public function assignVariables(): void
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'events' => $this->events,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function countItems(): int
    {
        // call countItems event
        EventHandler::getInstance()->fireAction($this, 'countItems');

        return \count($this->events);
    }

    /**
     * @inheritDoc
     */
    public function readData(): void
    {
        AbstractPage::readData();

        $eventList = new AccessibleEventList(TIME_NOW, TIME_NOW + 86400 * 365);
        $eventList->readObjects();
        foreach ($eventList as $event) {
            $objectType = ObjectTypeCache::getInstance()->getObjectType($event->objectTypeID);

            $isParticipat = false;
            switch ($objectType->objectType) {
                case 'dev.daries.rp.event.appointment':
                    if ($event->appointments) {
                        foreach ($event->appointments as $status => $userIDs) {
                            foreach ($userIDs as $userID) {
                                if ($userID === WCF::getUser()->userID) {
                                    $isParticipat = true;
                                    break 2;
                                }
                            }
                        }
                    }
                    break;
                case 'dev.daries.rp.event.raid':
                    $attendeeList = new EventRaidAttendeeList();
                    $attendeeList->getConditionBuilder()->add('event_raid_attendee.eventID = ?', [$event->eventID]);
                    $attendeeList->getConditionBuilder()->add('event_raid_attendee.characterID IS NOT NULL');
                    $attendeeList->getConditionBuilder()->add('member.userID = ?', [WCF::getUser()->userID]);

                    $attendeeList->sqlConditionJoins .= "
                        LEFT JOIN   rp" . WCF_N . "_member member
                        ON          member.characterID = event_raid_attendee.characterID";

                    if ($attendeeList->countObjects()) {
                        $isParticipat = true;
                    }
                    break;
                default:
                    $parameters = [
                        'event' => $event,
                        'isParticipat' => false,
                    ];
                    EventHandler::getInstance()->fireAction($this, 'readEvent', $parameters);
                    $isParticipat = $parameters['isParticipat'];
                    break;
            }

            if ($isParticipat) {
                $this->events[$event->eventID] = $event;
            }
        }

        $this->calculateNumberOfPages();

        $i = 0;
        foreach ($this->events as $eventID => $event) {
            $i++;

            if ($i < $this->startIndex || $i > $this->endIndex) {
                unset($this->events[$eventID]);
                continue;
            }
        }
    }
}
