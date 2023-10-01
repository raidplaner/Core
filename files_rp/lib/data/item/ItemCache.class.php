<?php

namespace rp\data\item;

use rp\system\cache\builder\ItemCacheBuilder;
use wcf\system\SingletonFactory;


/**
 * Manages the item cache.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class ItemCache extends SingletonFactory
{
    /**
     * cached item by item name
     * @var int[]
     */
    protected array $cachedItemNames = [];

    /**
     * cached items
     * @var Item[]
     */
    protected array $cachedItems = [];

    /**
     * Returns the item with the given item id or `null` if no such item exists.
     */
    public function getItemByID(int $itemID): ?Item
    {
        return $this->cachedItems[$itemID] ?? null;
    }

    /**
     * Returns the item with the given item name or `null` if no such item exists.
     */
    public function getItemByName(string $name): ?Item
    {
        $itemID = $this->cachedItemNames[\base64_encode($name)] ?? 0;
        return $this->getItemByID($itemID);
    }

    /**
     * Returns all items.
     * 
     * @return	Item[]
     */
    public function getItems(): array
    {
        return $this->cachedItems;
    }

    /**
     * Returns the items with the given item ids.
     * 
     * @return	Item[]
     */
    public function getItemsByIDs(array $itemIDs): array
    {
        $returnValues = [];

        foreach ($itemIDs as $itemID) {
            $returnValues[] = $this->getItemByID($itemID);
        }

        return $returnValues;
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $this->cachedItemNames = ItemCacheBuilder::getInstance()->getData([], 'itemNames');
        $this->cachedItems = ItemCacheBuilder::getInstance()->getData([], 'items');
    }
}
