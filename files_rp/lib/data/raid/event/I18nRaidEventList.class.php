<?php

namespace rp\data\raid\event;

use wcf\data\I18nDatabaseObjectList;


/**
 * I18n implementation of raid event list.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      RaidEvent           current()
 * @method      RaidEvent[]         getObjects()
 * @method      RaidEvent|null      getSingleObject()
 * @method      RaidEvent|null      search($objectID)
 * @property    RaidEvent[]         $objects
 */
class I18nRaidEventList extends I18nDatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $i18nFields = ['eventName' => 'eventNameI18n'];

    /**
     * @inheritDoc
     */
    public $className = RaidEvent::class;

}
