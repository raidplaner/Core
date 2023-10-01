<?php

namespace rp\data\role;

use rp\system\cache\builder\RoleCacheBuilder;
use wcf\system\SingletonFactory;

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
 * Manages the role cache.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Role
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
