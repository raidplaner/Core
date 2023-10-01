<?php

namespace rp\data\role;

use rp\system\cache\builder\RoleCacheBuilder;
use wcf\system\SingletonFactory;


/**
 * Manages the role cache.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class RoleCache extends SingletonFactory
{
    /**
     * cached role ids with role identifier as key
     * @var int[]
     */
    protected array $cachedIdentifier = [];

    /**
     * cached roles
     * @var Role[]
     */
    protected array $cachedRoles = [];

    /**
     * Returns the role with the given role id or `null` if no such role exists.
     */
    public function getRoleByID(int $roleID): ?Role
    {
        return $this->cachedRoles[$roleID] ?? null;
    }

    /**
     * Returns the role with the given role identifier or `null` if no such role exists.
     */
    public function getRoleByIdentifier(string $identifier): ?Role
    {
        return $this->getRoleByID($this->cachedIdentifier[$identifier] ?? 0);
    }

    /**
     * Returns all roles.
     * 
     * @return	Role[]
     */
    public function getRoles(): array
    {
        return $this->cachedRoles;
    }

    /**
     * Returns the role with the given role id.
     * 
     * @return	Role[]
     */
    public function getRolesByID(array $roleIDs): array
    {
        $roles = [];

        foreach ($roleIDs as $roleID) {
            $roles[$roleID] = $this->getRoleByID($roleID);
        }

        return $roles;
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $this->cachedRoles = RoleCacheBuilder::getInstance()->getData(['gameID' => RP_DEFAULT_GAME_ID], 'role');
        $this->cachedIdentifier = RoleCacheBuilder::getInstance()->getData(['gameID' => RP_DEFAULT_GAME_ID], 'identifier');
    }
}
