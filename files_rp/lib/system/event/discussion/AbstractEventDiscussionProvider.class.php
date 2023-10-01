<?php

namespace rp\system\event\discussion;

use rp\data\event\Event;


/**
 * Default implementation for discussion provider for events. Any actual implementation
 * should derive from this class for forwards-compatibility.
 *
 * @author  Marco Daries
 * @package     Daries\RP\System\Event\Discussion
 */
abstract class AbstractEventDiscussionProvider implements IEventDiscussionProvider
{
    /**
     * event object
     */
    protected Event $event;

    /**
     * AbstractEventDiscussionProvider constructor.
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }
}
