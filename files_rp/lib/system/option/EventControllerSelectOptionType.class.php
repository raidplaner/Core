<?php

namespace rp\system\option;

use rp\data\game\Game;
use wcf\data\object\type\ObjectType;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\option\Option;
use wcf\system\option\SelectOptionType;
use wcf\system\WCF;


/**
 * Option type implementation for event controller select lists.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class EventControllerSelectOptionType extends SelectOptionType
{

    /**
     * @inheritDoc
     * @return  Game[]
     */
    protected function getSelectOptions(Option $option): array
    {
        $availableEventControllers = ObjectTypeCache::getInstance()->getObjectTypes('dev.daries.rp.eventController');

        \uasort($availableEventControllers, function (ObjectType $a, ObjectType $b) {
            return \strcmp(
            WCF::getLanguage()->get('rp.event.controller.' . $a->objectType),
            WCF::getLanguage()->get('rp.event.controller.' . $b->objectType)
            );
        });

        $eventControllers = [];
        /** @var ObjectType $eventController */
        foreach ($availableEventControllers as $eventController) {
            $eventControllers[$eventController->objectType] = WCF::getLanguage()->get('rp.event.controller.' . $eventController->objectType);
        }

        return $eventControllers;
    }
}
