<?php

namespace wcf\system\package\plugin;

use rp\data\faction\Faction;
use rp\data\race\RaceEditor;
use wcf\data\IStorableObject;
use wcf\system\devtools\pip\IIdempotentPackageInstallationPlugin;
use wcf\system\exception\SystemException;
use wcf\system\WCF;
use wcf\util\ArrayUtil;

/**
 * Installs, updates and deletes races.
 * 
 * @author  Marco Daries
 * @package     WoltLabSuite\Core\System\Package\Plugin
 */
class RPRacePackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin implements IIdempotentPackageInstallationPlugin
{
    /**
     * @inheritDoc
     */
    public $application = 'rp';

    /**
     * @inheritDoc
     */
    public $className = RaceEditor::class;

    /**
     * @inheritDoc
     */
    public $tableName = 'race';

    /**
     * @inheritDoc
     */
    public $tagName = 'race';

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
        return 'rpRace.xml';
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
    protected function import(array $row, array $data): IStorableObject
    {
        $factions = $data['factions'];
        unset($data['factions']);

        $race = parent::import($row, $data);

        // remove exist
        $sql = "DELETE FROM " . $this->application . WCF_N . "_" . $this->tableName . "_to_faction
                WHERE       raceID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$race->raceID]);

        // new entry
        $sql = "INSERT INTO " . $this->application . WCF_N . "_" . $this->tableName . "_to_faction
                            (raceID, factionID)
                VALUES      (?, ?)";
        $statement = WCF::getDB()->prepareStatement($sql);

        WCF::getDB()->beginTransaction();
        foreach ($factions as $faction) {
            $statement->execute([
                $race->raceID,
                $faction->factionID,
            ]);
        }
        WCF::getDB()->commitTransaction();

        return $race;
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
                throw new SystemException("Unable to find game '" . $data['elements']['game'] . "' for race '" . $data['attributes']['identifier'] . "'");
            }

            $gameID = $row['gameID'];
        }

        if ($gameID === null) {
            throw new SystemException("The race '" . $data['attributes']['identifier'] . "' must either have an associated game.");
        }

        $factions = [];
        if (isset($data['elements']['factions'])) {
            $tmpFactions = ArrayUtil::trim(\explode(',', $data['elements']['factions']));
            foreach ($tmpFactions as $factionName) {
                $sql = "SELECT  factionID
                        FROM    " . $this->application . WCF_N . "_faction
                        WHERE   identifier = ?
                            AND gameID = ?";
                $statement = WCF::getDB()->prepareStatement($sql, 1);
                $statement->execute([
                    $factionName,
                    $gameID,
                ]);
                $row = $statement->fetchSingleRow();
                if ($row === false) {
                    throw new SystemException("Unable to find faction '" . $factionName . "' for race '" . $data['attributes']['identifier'] . "'");
                }

                $factions[] = new Faction(null, $row);
            }
        }

        return [
            'factions' => $factions,
            'gameID' => $gameID,
            'icon' => $data['elements']['icon'] ?? '',
            'identifier' => $data['attributes']['identifier'],
        ];
    }
}
