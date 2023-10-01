<?php

namespace rp\system\item\database;

use wcf\data\language\Language;


/**
 * Default interface for item database.
 *
 * @author  Marco Daries
 * @package     Daries\RP\System\Item\Database
 */
interface IItemDatabase
{

    /**
     * Return $item array, with all information about an item.
     */
    public function getItemData(string|int $itemID, ?Language $language = null, string $type = 'items'): ?array;

    /**
     * Searches an item id for an item name.
     */
    public function searchItemID(string $itemName, ?Language $language = null): array;
}
