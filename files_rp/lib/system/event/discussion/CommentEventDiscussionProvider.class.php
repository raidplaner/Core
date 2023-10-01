<?php

namespace rp\system\event\discussion;

use rp\data\event\Event;
use wcf\system\comment\CommentHandler;
use wcf\system\WCF;


/**
 * The built-in discussion provider using the native comment system.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class CommentEventDiscussionProvider extends AbstractEventDiscussionProvider
{

    /**
     * @inheritDoc
     */
    public function getDiscussionCount(): int
    {
        return $this->event->comments;
    }

    /**
     * @inheritDoc
     */
    public function getDiscussionCountPhrase(): string
    {
        return WCF::getLanguage()->getDynamicVariable('rp.event.eventComments', [
                'event' => $this->event,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getDiscussionLink(): string
    {
        return $this->event->getLink() . '#comments';
    }

    /**
     * @inheritDoc
     */
    public static function isResponsible(Event $event): bool
    {
        return !!$event->enableComments;
    }

    /**
     * @inheritDoc
     */
    public function renderDiscussions(): string
    {
        $commentCanAdd = WCF::getSession()->getPermission('user.rp.canAddComment');
        $commentObjectTypeID = CommentHandler::getInstance()->getObjectTypeID('dev.daries.rp.eventComment');
        $commentManager = CommentHandler::getInstance()->getObjectType($commentObjectTypeID)->getProcessor();
        $commentList = CommentHandler::getInstance()->getCommentList(
            $commentManager,
            $commentObjectTypeID,
            $this->event->eventID
        );

        WCF::getTPL()->assign([
            'commentCanAdd' => $commentCanAdd,
            'commentList' => $commentList,
            'commentObjectTypeID' => $commentObjectTypeID,
            'lastCommentTime' => $commentList->getMinCommentTime(),
            'likeData' => (MODULE_LIKE) ? $commentList->getLikeData() : [],
        ]);

        return WCF::getTPL()->fetch('eventComments', 'rp');
    }
}
