<?php

namespace rp\page;

use rp\data\event\EventAction;
use rp\data\event\EventEditor;
use rp\data\event\ViewableEvent;
use rp\data\event\ViewableEventList;
use wcf\data\comment\StructuredCommentList;
use wcf\data\like\object\LikeObject;
use wcf\page\AbstractPage;
use wcf\system\comment\CommentHandler;
use wcf\system\comment\manager\ICommentManager;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\MetaTagHandler;
use wcf\system\reaction\ReactionHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;


/**
 * Shows the event page.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class EventPage extends AbstractPage
{
    /**
     * list of comments
     */
    public StructuredCommentList $commentList;

    /**
     * comment manager object
     */
    public ICommentManager $commentManager;

    /**
     * comment object type id
     */
    public int $commentObjectTypeID = 0;

    /**
     * event object
     */
    public ?ViewableEvent $event;

    /**
     * event id
     */
    public int $eventID = 0;

    /**
     * like data for the event
     * @var LikeObject[]
     */
    public array $eventLikeData = [];

    /**
     * next event
     */
    public ?ViewableEvent $nextEvent = null;

    /**
     * previous event
     */
    public ?ViewableEvent $previousEvent = null;

    /**
     * @inheritDoc
     */
    public function assignVariables(): void
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'event' => $this->event,
            'eventID' => $this->eventID,
            'eventLikeData' => $this->eventLikeData,
            'nextEvent' => $this->nextEvent,
            'previousEvent' => $this->previousEvent
        ]);
    }

    /**
     * @inheritDoc
     */
    public function checkPermissions(): void
    {
        parent::checkPermissions();

        if (!$this->event->canRead()) {
            throw new PermissionDeniedException();
        }
    }

    /**
     * @inheritDoc
     */
    public function readData(): void
    {
        parent::readData();

        // update view count
        $eventEditor = new EventEditor($this->event->getDecoratedObject());
        $eventEditor->updateCounters([
            'views' => 1,
        ]);

        // update event visit
        if ($this->event->isNew()) {
            $eventAction = new EventAction([$this->event->getDecoratedObject()], 'markAsRead', [
                'viewableEvent' => $this->event
            ]);
            $eventAction->executeAction();
        }

        // get comments
        if ($this->event->enableComments) {
            $this->commentObjectTypeID = CommentHandler::getInstance()->getObjectTypeID('dev.daries.rp.eventComment');
            $this->commentManager = CommentHandler::getInstance()->getObjectType($this->commentObjectTypeID)->getProcessor();
            $this->commentList = CommentHandler::getInstance()->getCommentList(
                $this->commentManager,
                $this->commentObjectTypeID,
                $this->event->eventID
            );
        }
        
        // fetch likes
        if (MODULE_LIKE) {
            $objectType = ReactionHandler::getInstance()->getObjectType('dev.daries.rp.likeableEvent');
            ReactionHandler::getInstance()->loadLikeObjects($objectType, [$this->event->eventID]);
            $this->eventLikeData = ReactionHandler::getInstance()->getLikeObjects($objectType);
        }

        // get next event
        $eventList = new ViewableEventList();
        $eventList->getConditionBuilder()->add('event.startTime > ?', [$this->event->startTime]);
        $eventList->sqlOrderBy = 'event.startTime ASC';
        $eventList->sqlLimit = 1;
        $eventList->readObjects();
        foreach ($eventList as $event) $this->nextEvent = $event;

        // get previous event
        $eventList = new ViewableEventList();
        $eventList->getConditionBuilder()->add('event.startTime < ?', [$this->event->startTime]);
        $eventList->sqlOrderBy = 'event.startTime DESC';
        $eventList->sqlLimit = 1;
        $eventList->readObjects();
        foreach ($eventList as $event) $this->previousEvent = $event;

        // add meta/og tags
        MetaTagHandler::getInstance()->addTag('og:title', 'og:title', $this->event->getTitle() . ' - ' . WCF::getLanguage()->get(\PAGE_TITLE), true);
        MetaTagHandler::getInstance()->addTag('og:url', 'og:url', $this->event->getLink(), true);
        MetaTagHandler::getInstance()->addTag('og:type', 'og:type', 'article', true);
        MetaTagHandler::getInstance()->addTag('og:description', 'og:description', StringUtil::decodeHTML(StringUtil::stripHTML($this->event->getExcerpt())), true);
    }

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        if (isset($_REQUEST['id'])) $this->eventID = \intval($_REQUEST['id']);
        $this->event = ViewableEvent::getEvent($this->eventID);
        if ($this->event === null) {
            throw new IllegalLinkException();
        }

        $this->canonicalURL = $this->event->getLink();
    }
}
