<?php

namespace rp\data\race;

use rp\data\game\Game;
use rp\data\game\GameCache;
use wcf\data\DatabaseObject;
use wcf\data\ITitledObject;
use wcf\system\WCF;
use wcf\util\StringUtil;


/**
 * Represents a race.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @property-read   int         $raceID                 unique id of the race
 * @property-read   int         $packageID              id of the package which delivers the race
 * @property-read   int         $gameID                 id of the game
 * @property-read   string      $identifier             unique textual identifier of the race identifier
 * @property-read   string      $icon                   icon of the race
 * @property-read   int         $isDisabled             is `1` if the race is disabled and thus not selectable, otherwise `0`
 */
class Race extends DatabaseObject implements ITitledObject
{

    /**
     * Returns game object.
     */
    public function getGame(): ?Game
    {
        return GameCache::getInstance()->getGameByID($this->gameID);
    }

    /**
     * Returns the html code to display the icon.
     */
    public function getIcon(int $size, string $type = ''): string
    {
        if (empty($this->icon)) return '';
        if ($size === null) $size = $this->size;

        return '<img src="' . StringUtil::encodeHTML($this->getIconPath($type)) . '" style="width: ' . $size . 'px; height: ' . $size . 'px" alt="" class="gameIcon jsTooltip" title="' . $this->getTitle() . '" loading="lazy">';
    }

    /**
     * Returns full path to icon.
     */
    public function getIconPath(string $type): string
    {
        $filename = $this->icon;
        switch ($type) {
            case 'female':
                $filename .= '_female';
                break;
            case 'male':
                $filename .= '_male';
                break;
        }

        return WCF::getPath('rp') . 'images/' . $this->getGame()->identifier . '/' . $filename . '.png';
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return WCF::getLanguage()->get('rp.race.' . $this->getGame()->identifier . '.' . $this->identifier);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->getTitle();
    }
}
