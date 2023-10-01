<?php

namespace wcf\system\package\plugin;

use rp\data\server\ServerEditor;
use wcf\system\devtools\pip\IIdempotentPackageInstallationPlugin;
use wcf\system\exception\SystemException;
use wcf\system\WCF;

/**
 * Installs, updates and deletes servers.
 * 
 * @author  Marco Daries
 * @package     WoltLabSuite\Core\System\Package\Plugin
 */
class RPServerPackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin implements IIdempotentPackageInstallationPlugin
{
    /**
     * @inheritDoc
     */
    public $application = 'rp';

    /**
     * @inheritDoc
     */
    public $className = ServerEditor::class;

    /**
     * @inheritDoc
     */
    public $tableName = 'server';

    /**
     * @inheritDoc
     */
    public $tagName = 'server';

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
            'parameters' => $parameters,
            'sql' => $sql,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getDefaultFilename(): string
    {
        return 'rpServer.xml';
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
        $gameID = null;
        if (!empty($data['elements']['game'])) {
            $sql = "SELECT  gameID
                    FROM    " . $this->application . WCF_N . "_game
                    WHERE   identifier = ?";
            $statement = WCF::getDB()->prepareStatement($sql, 1);
            $statement->execute([$data['elements']['game']]);
            $row = $statement->fetchSingleRow();
            if ($row === false) {
                throw new SystemException("Unable to find game '" . $data['elements']['game'] . "' for server '" . $data['attributes']['identifier'] . "'");
            }

            $gameID = $row['gameID'];
        }

        if ($gameID === null) {
            throw new SystemException("The server '" . $data['attributes']['identifier'] . "' must either have an associated game.");
        }

        return [
            'gameID' => $gameID,
            'identifier' => $data['attributes']['identifier'],
            'serverGroup' => $data['elements']['servergroup'] ?? '',
            'type' => $data['elements']['type'],
        ];
    }
}
