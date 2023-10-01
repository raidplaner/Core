<?php

namespace rp\data\role;

use rp\data\game\Game;
use rp\data\game\GameCache;
use wcf\data\DatabaseObject;
use wcf\data\ITitledObject;
use wcf\system\WCF;
use wcf\util\StringUtil;


/**
 * Represents a role.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @property-read   int         $roleID         unique id of the role
 * @property-read   int         $packageID      id of the package which delivers the role
 * @property-read   int         $gameID         id of the game
 * @property-read   string      $identifier     unique textual identifier of the role identifier
 * @property-read   string      $icon           icon of the role
 * @property-read   int         $isDisabled     is `1` if the role is disabled and thus not selectable, otherwise `0`
 */
class Role extends DatabaseObject implements ITitledObject
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
    public function getIcon(int $size): string
    {
        if (empty($this->icon)) return '';
        if ($size === null) $size = $this->size;

        return '<img src="' . StringUtil::encodeHTML($this->getIconPath()) . '" style="width: ' . $size . 'px; height: ' . $size . 'px" alt="" class="gameIcon jsTooltip" title="' . $this->getTitle() . '" loading="lazy">';
    }

    /**
     * Returns full path to icon.
     */
    public function getIconPath(): string
    {
        return WCF::getPath('rp') . 'images/' . $this->getGame()->identifier . '/' . $this->icon . '.png';
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return WCF::getLanguage()->get('rp.role.' . $this->getGame()->identifier . '.' . $this->identifier);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->getTitle();
    }
}
