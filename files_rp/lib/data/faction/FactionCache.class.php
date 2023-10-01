<?php

namespace rp\data\faction;

use rp\system\cache\builder\FactionCacheBuilder;
use wcf\system\SingletonFactory;


/**
 * Manages the faction cache.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class FactionCache extends SingletonFactory
{
    /**
     * cached faction ids with faction identifier as key
     * @var int[]
     */
    protected array $cachedIdentifier = [];

    /**
     * cached factions
     * @var Faction[]
     */
    protected array $cachedFactions = [];

    /**
     * Returns the faction with the given faction id or `null` if no such faction exists.
     */
    public function getFactionByID(int $factionID): ?Faction
    {
        return $this->cachedFactions[$factionID] ?? null;
    }

    /**
     * Returns the faction with the given faction identifier or `null` if no such faction exists.
     */
    public function getFactionByIdentifier(string $identifier): ?Faction
    {
        return $this->getFactionByID($this->cachedIdentifier[$identifier] ?? 0);
    }

    /**
     * Returns all factions.
     * 
     * @return	Faction[]
     */
    public function getFactions(): array
    {
        return $this->cachedFactions;
    }

    /**
     * Returns the factions with the given faction ids.
     * 
     * @return	Faction[]
     */
    public function getFactionsByIDs(array $factionIDs): array
    {
        $returnValues = [];

        foreach ($factionIDs as $factionID) {
            $returnValues[] = $this->getFactionByID($factionID);
        }

        return $returnValues;
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $this->cachedFactions = FactionCacheBuilder::getInstance()->getData(['gameID' => RP_DEFAULT_GAME_ID], 'faction');
        $this->cachedIdentifier = FactionCacheBuilder::getInstance()->getData(['gameID' => RP_DEFAULT_GAME_ID], 'identifier');
    }
}
