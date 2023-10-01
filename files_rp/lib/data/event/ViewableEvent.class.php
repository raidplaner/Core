<?php

namespace rp\data\event;

use rp\data\modification\log\ViewableEventModificationLog;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\user\UserProfile;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\visitTracker\VisitTracker;
use wcf\system\WCF;


/**
 * Represents a viewable event.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method          Event       getDecoratedObject()
 * @mixin           Event
 */
class ViewableEvent extends DatabaseObjectDecorator
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Event::class;

    /**
     * effective visit time
     */
    protected ?int $effectiveVisitTime = null;

    /**
     * number of unseen events
     */
    protected static ?int $unreadEvents = null;

    /**
     * user profile object
     */
    protected ?UserProfile $userProfile = null;

    /**
     * Returns delete note if applicable.
     */
    public function getDeleteNote(): string
    {
        if ($this->logEntry === null || $this->logEntry->action != 'trash') {
            return '';
        }

        return WCF::getLanguage()->getDynamicVariable('rp.event.deleted', ['event' => $this]);
    }

    /**
     * Returns a specific event decorated as viewable event or `null` if it does not exist.
     */
    public static function getEvent(int $eventID): ?ViewableEvent
    {
        $list = new ViewableEventList();
        $list->setObjectIDs([$eventID]);
        $list->readObjects();

        return $list->getSingleObject();
    }

    /**
     * Returns modification log entry.
     */
    public function getLogEntry(): ViewableEventModificationLog
    {
        return $this->logEntry;
    }

    /**
     * Returns the number of unseen events.
     */
    public static function getUnreadEvents(): int
    {
        if (self::$unreadEvents === null) {
            self::$unreadEvents = 0;

            if (WCF::getUser()->userID) {
                $unreadEvents = UserStorageHandler::getInstance()->getField('rpUnreadEvents');

                // cache does not exist or is outdated
                if ($unreadEvents === null) {
                    $conditionBuilder = new PreparedStatementConditionBuilder();
                    $conditionBuilder->add(
                        'event.created > ?',
                        [VisitTracker::getInstance()->getVisitTime('dev.daries.rp.event')]
                    );
                    $conditionBuilder->add('(event.created > tracked_visit.visitTime OR tracked_visit.visitTime IS NULL)');

                    $sql = "SELECT      COUNT(*)
                            FROM        rp" . WCF_N . "_event event
                            LEFT JOIN   wcf" . WCF_N . "_tracked_visit tracked_visit
                            ON          tracked_visit.objectTypeID = " . VisitTracker::getInstance()->getObjectTypeID('dev.daries.rp.event') . "
                                    AND tracked_visit.objectID = event.eventID
                                    AND tracked_visit.userID = " . WCF::getUser()->userID . "
                            " . $conditionBuilder;
                    $statement = WCF::getDB()->prepareStatement($sql);
                    $statement->execute($conditionBuilder->getParameters());
                    self::$unreadEvents = $statement->fetchSingleColumn();

                    // update storage unread events
                    UserStorageHandler::getInstance()->update(
                        WCF::getUser()->userID,
                        'rpUnreadEvents',
                        self::$unreadEvents
                    );
                } else {
                    self::$unreadEvents = $unreadEvents;
                }
            }
        }

        return self::$unreadEvents;
    }

    /**
     * Returns the user profile object.
     */
    public function getUserProfile(): UserProfile
    {
        if ($this->userProfile === null) {
            if ($this->userID) {
                $this->userProfile = UserProfileRuntimeCache::getInstance()->getObject($this->userID);
            } else {
                $this->userProfile = UserProfile::getGuestUserProfile($this->username);
            }
        }

        return $this->userProfile;
    }

    /**
     * Returns the effective visit time.
     */
    public function getVisitTime(): int
    {
        if ($this->effectiveVisitTime === null) {
            if (WCF::getUser()->userID) {
                $this->effectiveVisitTime = \max(
                    $this->visitTime,
                    VisitTracker::getInstance()->getVisitTime('dev.daries.rp.event')
                );
            } else {
                $this->effectiveVisitTime = \max(
                    VisitTracker::getInstance()->getObjectVisitTime(
                        'dev.daries.rp.event',
                        $this->eventID
                    ),
                    VisitTracker::getInstance()->getVisitTime('dev.daries.rp.event')
                );
            }
            if ($this->effectiveVisitTime === null) {
                $this->effectiveVisitTime = 0;
            }
        }

        return $this->effectiveVisitTime;
    }

    /**
     * Returns true if this event is new for the active user.
     */
    public function isNew(): bool
    {
        return $this->created > $this->getVisitTime();
    }

    /**
     * Sets modification log entry.
     */
    public function setLogEntry(ViewableEventModificationLog $logEntry): void
    {
        $this->logEntry = $logEntry;
    }
}
