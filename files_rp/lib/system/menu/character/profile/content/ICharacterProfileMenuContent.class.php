<?php

namespace rp\system\menu\character\profile\content;


/**
 * Default interface for character profile menu content.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
interface ICharacterProfileMenuContent
{

    /**
     * Returns content for this character profile menu item.
     */
    public function getContent(int $characterID): string;

    /**
     * Returns true if the associated menu item should be visible for the active user.
     */
    public function isVisible(int $characterID): bool;
}