<?php

namespace rp\system\user\notification\event;

use rp\system\cache\runtime\ViewableEventRuntimeCache;
use wcf\data\user\UserProfile;
use wcf\system\cache\runtime\CommentRuntimeCache;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\email\Email;
use wcf\system\user\notification\event\AbstractSharedUserNotificationEvent;
use wcf\system\user\notification\event\ITestableUserNotificationEvent;
use wcf\system\user\notification\event\TTestableCommentResponseUserNotificationEvent;
use wcf\system\user\notification\object\CommentResponseUserNotificationObject;


/**
 * User notification event for event comment responses.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      CommentResponseUserNotificationObject   getUserNotificationObject()
 */
class EventCommentResponseOwnerUserNotificationEvent extends AbstractSharedUserNotificationEvent implements ITestableUserNotificationEvent
{
    use TTestableCommentResponseUserNotificationEvent;
    use TTestableEventCommentUserNotificationEvent;
    /**
     * @inheritDoc
     */
    protected $stackable = true;

    /**
     * Returns the comment authors profile.
     */
    private function getCommentAuthorProfile(): UserProfile
    {
        $comment = CommentRuntimeCache::getInstance()->getObject($this->getUserNotificationObject()->commentID);

        if ($comment->userID) {
            return UserProfileRuntimeCache::getInstance()->getObject($comment->userID);
        } else {
            return UserProfile::getGuestUserProfile($comment->username);
        }
    }

    /**
     * @inheritDoc
     */
    public function getEmailMessage($notificationType = 'instant'): array
    {
        $messageID = '<dev.daries.rp.eventComment.notification/' . $this->getUserNotificationObject()->commentID . '@' . Email::getHost() . '>';

        return [
            'template' => 'email_notification_commentResponseOwner',
            'in-reply-to' => [$messageID],
            'references' => [$messageID],
            'application' => 'wcf',
            'variables' => [
                'commentAuthor' => $this->getCommentAuthorProfile(),
                'commentID' => $this->getUserNotificationObject()->commentID,
                'eventObj' => ViewableEventRuntimeCache::getInstance()
                    ->getObject($this->additionalData['objectID']),
                'languageVariablePrefix' => 'rp.user.notification.eventComment.responseOwner',
                'responseID' => $this->getUserNotificationObject()->responseID,
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getEventHash(): string
    {
        return \sha1($this->eventID . '-' . $this->notification->objectID);
    }

    /**
     * @inheritDoc
     */
    public function getLink(): string
    {
        return ViewableEventRuntimeCache::getInstance()->getObject($this->additionalData['objectID'])->getLink() . '#comment' . $this->getUserNotificationObject()->commentID . '/response' . $this->getUserNotificationObject()->responseID;
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): string
    {
        $authors = $this->getAuthors();
        if (\count($authors) > 1) {
            if (isset($authors[0])) {
                unset($authors[0]);
            }
            $count = \count($authors);

            return $this->getLanguage()->getDynamicVariable(
                    'rp.user.notification.eventComment.responseOwner.message.stacked',
                    [
                        'author' => $this->author,
                        'authors' => \array_values($authors),
                        'commentAuthor' => $this->getCommentAuthorProfile(),
                        'commentID' => $this->getUserNotificationObject()->commentID,
                        'count' => $count,
                        'event' => ViewableEventRuntimeCache::getInstance()
                            ->getObject($this->additionalData['objectID']),
                        'guestTimesTriggered' => $this->notification->guestTimesTriggered,
                        'others' => $count - 1,
                        'responseID' => $this->getUserNotificationObject()->responseID,
                    ]
            );
        }

        return $this->getLanguage()->getDynamicVariable('rp.user.notification.eventComment.responseOwner.message', [
                'author' => $this->author,
                'commentAuthor' => $this->getCommentAuthorProfile(),
                'commentID' => $this->getUserNotificationObject()->commentID,
                'event' => ViewableEventRuntimeCache::getInstance()
                    ->getObject($this->additionalData['objectID']),
                'responseID' => $this->getUserNotificationObject()->responseID,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        $count = \count($this->getAuthors());
        if ($count > 1) {
            return $this->getLanguage()->getDynamicVariable(
                    'rp.user.notification.eventComment.responseOwner.title.stacked',
                    [
                        'count' => $count,
                        'timesTriggered' => $this->notification->timesTriggered,
                    ]
            );
        }

        return $this->getLanguage()->get('rp.user.notification.eventComment.responseOwner.title');
    }

    /**
     * @inheritDoc
     */
    protected function prepare(): void
    {
        CommentRuntimeCache::getInstance()->cacheObjectID($this->getUserNotificationObject()->commentID);
    }
}
