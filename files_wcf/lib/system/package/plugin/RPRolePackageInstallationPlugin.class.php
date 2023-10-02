<?php

namespace wcf\system\package\plugin;

use rp\data\role\RoleEditor;
use wcf\system\devtools\pip\IIdempotentPackageInstallationPlugin;
use wcf\system\exception\SystemException;
use wcf\system\WCF;

/**
 * Installs, updates and deletes roles.
 * 
 * @author  Marco Daries
 * @package     WoltLabSuite\Core\System\Package\Plugin
 */
class RPRolePackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin implements IIdempotentPackageInstallationPlugin
{
    /**
     * @inheritDoc
     */
    public $application = 'rp';

    /**
     * @inheritDoc
     */
    public $className = RoleEditor::class;

    /**
     * @inheritDoc
     */
    public $tableName = 'role';

    /**
     * @inheritDoc
     */
    public $tagName = 'role';

    /**
     * @inheritDoc
     */
    protected function findExistingItem(array $data): array
    {
        $sql = "SELECT	*
                FROM	" . $this->application . WCF_N . "_" . $this->tableName . "
                WHERE	identifier = ?
                    AND packageID = ?";
        $parameters = [
            $data['identifier'],
            $this->installation->getPackageID(),
        ];

        return [
            'sql' => $sql,
            'parameters' => $parameters,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getDefaultFilename(): string
    {
        return 'rpRole.xml';
    }

    /**
     * @inheritDoc
     */
    public static function getSyncDependencies(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    protected function handleDelete(array $items): void
    {
        $sql = "DELETE FROM " . $this->application . WCF_N . "_" . $this->tableName . "
                WHERE       identifier = ?
                    AND     packageID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);

        WCF::getDB()->beginTransaction();
        foreach ($items as $item) {
            $statement->execute([
                $item['attributes']['identifier'],
                $this->installation->getPackageID(),
            ]);
        }
        WCF::getDB()->commitTransaction();
    }

    /**
     * @inheritDoc
     */
    protected function prepareImport(array $data): array
    {
        $sql = "SELECT  gameID
                FROM    " . $this->application . WCF_N . "_game
                WHERE   identifier = ?";
        $statement = WCF::getDB()->prepareStatement($sql, 1);
        $statement->execute([$data['elements']['game']]);
        $row = $statement->fetchSingleRow();
        if ($row === false) {
            throw new SystemException("Unable to find game '" . $data['elements']['game'] . "' for role '" . $data['attributes']['identifier'] . "'");
        }
        $gameID = $row['gameID'];

        return [
            'gameID' => $gameID,
            'icon' => $data['elements']['icon'] ?? '',
            'identifier' => $data['attributes']['identifier'],
        ];
    }
}
