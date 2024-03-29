<?php

namespace rp\system\cache\builder;

use rp\data\character\profile\menu\item\CharacterProfileMenuItemList;
use wcf\system\cache\builder\AbstractCacheBuilder;


/**
 * Caches the character profile menu items.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class CharacterProfileMenuCacheBuilder extends AbstractCacheBuilder
{

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters): array
    {
        $itemList = new CharacterProfileMenuItemList();
        $itemList->sqlOrderBy = "member_profile_menu_item.showOrder ASC";
        $itemList->readObjects();

        return $itemList->getObjects();
    }
}
