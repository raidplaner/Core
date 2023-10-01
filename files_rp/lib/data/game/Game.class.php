<?php

namespace rp\data\game;

use wcf\data\DatabaseObject;
use wcf\data\ITitledObject;
use wcf\system\WCF;
use wcf\util\StringUtil;


/**
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @property-read   int         $gameID         unique id of the game
 * @property-read   int         $packageID      id of the package which delivers the game
 * @property-read   string      $identifier     unique textual identifier of the game
 */
class Game extends DatabaseObject implements ITitledObject
{

    /**
     * Returns the html code to display the icon.
     */
    public function getIcon(int $size): string
    {
        if (empty($this->icon)) return '';
        if ($size === null) $size = 128;

        return '<img src="' . StringUtil::encodeHTML($this->getIconPath()) . '" style="width: ' . $size . 'px; height: ' . $size . 'px" alt="" class="gameIcon jsTooltip" title="' . $this->getTitle() . '" loading="lazy">';
    }

    /**
     * Returns full path to icon.
     */
    public function getIconPath(): string
    {
        return WCF::getPath('rp') . 'images/' . $this->identifier . '/' . $this->identifier . '.png';
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return WCF::getLanguage()->get('rp.game.' . $this->identifier);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->getTitle();
    }
}
