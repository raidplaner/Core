<?php

namespace rp\system\option;

use rp\data\game\Game;
use rp\data\game\GameCache;
use wcf\data\option\Option;
use wcf\system\option\SelectOptionType;


/**
 * Option type implementation for game select lists.
 * 
 * @author  Marco Daries
 * @package     Daries\RP\System\Option
 */
class GameSelectOptionType extends SelectOptionType
{

    /**
     * @inheritDoc
     * @return  Game[]
     */
    protected function getSelectOptions(Option $option): array
    {
        $games = GameCache::getInstance()->getGames();

        \uasort($games, function (Game $a, Game $b) {
            return \strcmp(
            $a->getTitle(),
            $b->getTitle()
            );
        });

        return $games;
    }
}
