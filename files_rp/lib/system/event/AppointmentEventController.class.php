<?php

namespace rp\system\event;

use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\WCF;


/**
 * Appointment event implementation for event controllers.
 *
 * @author  Marco Daries
 * @package     Daries\RP\System\Event
 */
class AppointmentEventController extends DefaultEventController
{
    /**
     * @inheritDoc
     */
    protected string $eventNodesPosition = 'right';

    /**
     * @inheritDoc
     */
    protected string $objectTypeName = 'dev.daries.rp.event.appointment';

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        $accepted = $canceled = $maybe = [];

        if ($this->getEvent()->appointments) {
            foreach ($this->getEvent()->appointments as $status => $userIDs) {
                foreach ($userIDs as $userID) {
                    switch ($status) {
                        case 'accepted':
                            $accepted[] = UserProfileRuntimeCache::getInstance()->getObject($userID);
                            break;
                        case 'canceled':
                            $canceled[] = UserProfileRuntimeCache::getInstance()->getObject($userID);
                            break;
                        case 'maybe':
                            $maybe[] = UserProfileRuntimeCache::getInstance()->getObject($userID);
                            break;
                    }
                }
            }
        }

        WCF::getTPL()->assign([
            'accepted' => $accepted,
            'canceled' => $canceled,
            'maybe' => $maybe
        ]);

        return WCF::getTPL()->fetch('eventAppointment', 'rp');
    }

    /**
     * @inheritDoc
     */
    public function hasLogin(): bool
    {
        if ($this->getEvent()->appointments) {
            foreach ($this->getEvent()->appointments as $userIDs) {
                if (\in_array(WCF::getUser()->userID, $userIDs)) return true;
            }
        }

        return false;
    }

    public function isExpired(): bool
    {
        if ($this->getEvent()->startTime < TIME_NOW) return true;
        return false;
    }
}
