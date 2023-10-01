<?php

namespace rp\data\event\raid\attendee;

use wcf\data\DatabaseObjectEditor;


/**
 * Provides functions to edit event raid attendee.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @method static   EventRaidAttendee       create(array $parameters = [])
 * @method          EventRaidAttendee       getDecoratedObject()
 * @mixin           EventRaidAttendee
 */
class EventRaidAttendeeEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = EventRaidAttendee::class;

}
