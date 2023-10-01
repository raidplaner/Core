<?php

namespace rp\data\rank;

use rp\system\cache\builder\RankCacheBuilder;
use wcf\system\SingletonFactory;


/**
 * Manages the rank cache.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class RankCache extends SingletonFactory
{
    /**
     * cached default rank
     */
    protected int $cachedDefault = 0;

    /**
     * cached ranks
     * @var Rank[]
     */
    protected array $cachedRanks = [];

    /**
     * Retrieves the default rank.
     * May return `null` if there is no default rank.
     */
    public function getDefaultRank(): ?Rank
    {
        return $this->getRankByID($this->cachedDefault);
    }

    /**
     * Returns the rank with the given rank id or `null` if no such rank exists.
     */
    public function getRankByID(int $rankID): ?Rank
    {
        return $this->cachedRanks[$rankID] ?? null;
    }

    /**
     * Returns all ranks.
     * 
     * @return  Rank[]
     */
    public function getRanks(): array
    {
        return $this->cachedRanks;
    }

    /**
     * Returns the rank with the given rank id.
     */
    public function getRanksByID(array $rankIDs): array
    {
        $returnValues = [];

        foreach ($rankIDs as $rankID) {
            $returnValues[] = $this->getRankByID($rankID);
        }

        return $returnValues;
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $this->cachedDefault = RankCacheBuilder::getInstance()->getData(['gameID' => RP_DEFAULT_GAME_ID], 'default');
        $this->cachedRanks = RankCacheBuilder::getInstance()->getData(['gameID' => RP_DEFAULT_GAME_ID], 'ranks');
    }
}
