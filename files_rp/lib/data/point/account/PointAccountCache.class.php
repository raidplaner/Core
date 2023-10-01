<?php

namespace rp\data\point\account;

use rp\system\cache\builder\PointAccountCacheBuilder;
use wcf\system\SingletonFactory;


/**
 * Manages the point account cache.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class PointAccountCache extends SingletonFactory
{
    /**
     * cached point accounts
     * @var PointAccount[]
     */
    protected array $cachedPointAccounts = [];

    /**
     * cached raid events
     */
    protected mixed $cachedRaidEvents = [];

    /**
     * Returns the point account with the given point account id or `null` if no such point account exists.
     * 
     * @param	int                 $pointAccountID
     * @return  PointAccount|null
     */
    public function getPointAccountByID(int $pointAccountID): ?PointAccount
    {
        return $this->cachedPointAccounts[$pointAccountID] ?? null;
    }

    /**
     * Returns all point accounts.
     * 
     * @return  PointAccount[]
     */
    public function getPointAccounts(): array
    {
        return $this->cachedPointAccounts;
    }

    /**
     * Returns the point accounts with the given point account ids.
     * 
     * @return	PointAccount[]
     */
    public function getPointAccountsByIDs(array $pointAccountIDs): array
    {
        $accounts = [];

        foreach ($pointAccountIDs as $pointAccountID) {
            $accounts[] = $this->getPointAccountByID($pointAccountID);
        }

        return $accounts;
    }

    /**
     * Returns all raid events associated with the point account ID.
     */
    public function getRaidEventsByID(int $pointAccountID): array
    {
        return $this->cachedRaidEvents[$pointAccountID] ?? [];
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $this->cachedPointAccounts = PointAccountCacheBuilder::getInstance()->getData(['gameID' => RP_DEFAULT_GAME_ID], 'accounts');
        $this->cachedRaidEvents = PointAccountCacheBuilder::getInstance()->getData(['gameID' => RP_DEFAULT_GAME_ID], 'raidEvents');
    }
}
