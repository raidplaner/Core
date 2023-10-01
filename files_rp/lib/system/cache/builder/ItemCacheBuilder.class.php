<?php

namespace rp\system\cache\builder;

use rp\data\item\ItemList;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 *  Project:    Raidplaner: Core
 *  Package:    dev.daries.rp
 *  Link:       http://daries.dev
 *
 *  Copyright (C) 2018-2023 Daries.dev Developer Team
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Caches all items.
 *
 * @author      Marco Daries
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
