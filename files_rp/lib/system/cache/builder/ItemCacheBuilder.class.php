<?php

namespace rp\system\cache\builder;

use rp\data\item\ItemList;
use wcf\system\cache\builder\AbstractCacheBuilder;


/**
 * Caches all items.
 *
 * @author  Marco Daries
 * @package     Daries\RP\System\Cache\Builder
 */
class ItemCacheBuilder extends AbstractCacheBuilder
{

    /**
     * @inheritDoc
     */
    public function rebuild(array $parameters): array
    {
        $data = [
            'itemNames' => [],
            'items' => [],
        ];

        $itemList = new ItemList();
        $itemList->readObjects();
        foreach ($itemList as $item) {
            $data['items'][$item->itemID] = $item;
            $data['itemNames'][\base64_encode($item->itemName)] = $item->itemID;
        }

        return $data;
    }
}
