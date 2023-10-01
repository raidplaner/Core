<?php

namespace rp\system\cache\builder;

use rp\data\role\Role;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;


/**
 * Caches the role.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class RoleCacheBuilder extends AbstractCacheBuilder
{

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters): array
    {
        $data = [
            'identifier' => [],
            'role' => [],
        ];

        // get game role
        $sql = "SELECT  *
                FROM    rp" . WCF_N . "_role
                WHERE   isDisabled = ?
                    AND gameID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
            0,
            $parameters['gameID'],
        ]);

        /** @var Role $object */
        while ($object = $statement->fetchObject(Role::class)) {
            $data['role'][$object->roleID] = $object;
            $data['identifier'][$object->identifier] = $object->roleID;
        }

        return $data;
    }
}
