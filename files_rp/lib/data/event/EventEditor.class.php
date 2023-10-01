<?php

namespace rp\data\event;

use wcf\data\DatabaseObjectEditor;


/**
 * Provides functions to edit events.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @method static   Event       create(array $parameters = [])
 * @method          Event       getDecoratedObject()
 * @mixin           Event
 */
class EventEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Event::class;

}
