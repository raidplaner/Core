<?php

namespace wcf\system\package\plugin;

use rp\data\game\GameEditor;
use wcf\system\devtools\pip\IIdempotentPackageInstallationPlugin;
use wcf\system\WCF;

/**
 * Installs, updates and deletes games.
 * 
 * @author  Marco Daries
 * @package     WoltLabSuite\Core\System\Package\Plugin
 */
class RPGamePackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin implements IIdempotentPackageInstallationPlugin
{
    /**
     * @inheritDoc
     */
    public $application = 'rp';

    /**
     * @inheritDoc
     */
    public $className = GameEditor::class;

    /**
     * @inheritDoc
     */
    public $tableName = 'game';

    /**
     * @inheritDoc
     */
    public $tagName = 'game';

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
            $this->installation->getPackageID()
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
        return 'rpGame.xml';
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
        return [
            'identifier' => $data['attributes']['identifier'],
        ];
    }
}
