<?php

namespace rp\system\event\discussion;

use rp\data\event\Event;


/**
 * Represents a non-existing discussion provider and is used when there is no other
 * type of discussion being available. This provider is always being evaluated last.
 *
 * @author  Marco Daries
 * @package     Daries\RP\System\Event\Discussion
 */
class VoidEventDiscussionProvider extends AbstractEventDiscussionProvider
{

    /**
     * @inheritDoc
     */
    public function getDiscussionCount(): int
    {
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function getDiscussionCountPhrase(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getDiscussionLink(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public static function isResponsible(Event $event): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function renderDiscussions(): string
    {
        return '';
    }
}
