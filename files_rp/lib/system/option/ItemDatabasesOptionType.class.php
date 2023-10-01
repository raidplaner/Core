<?php

namespace rp\system\option;

use wcf\data\option\Option;
use wcf\system\exception\UserInputException;
use wcf\system\option\AbstractOptionType;
use wcf\system\WCF;


/**
 * Option type implementation for item database selection.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class ItemDatabasesOptionType extends AbstractOptionType
{
    /**
     * list of available item databases
     */
    protected static ?array $databases = null;

    /**
     * @inheritDoc
     */
    public function getData(Option $option, $newValue): string
    {
        if (!\is_array($newValue)) {
            return '';
        }

        return \implode(',', $newValue);
    }

    /**
     * Returns the list of available item databases.
     */
    protected static function getDatabases(): array
    {
        if (self::$databases === null) {
            self::$databases = [];
            $sql = "SELECT  databaseName
                    FROM    rp" . WCF_N . "_item_database";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute();
            self::$databases = $statement->fetchAll(\PDO::FETCH_COLUMN);
        }

        return self::$databases;
    }

    /**
     * @inheritDoc
     */
    public function getFormElement(Option $option, $value): string
    {
        $databases = self::getDatabases();
        if ($option->issortable && $value) {
            $sortedDatabases = \explode(',', $value);

            // remove old databases
            $sortedDatabases = \array_intersect($sortedDatabases, $databases);

            // append the non-checked databases after the checked and sorted databases
            $databases = \array_merge($sortedDatabases, \array_diff($databases, $sortedDatabases));
        }

        WCF::getTPL()->assign([
            'option' => $option,
            'value' => \explode(',', $value),
            'availableDatabases' => $databases,
        ]);

        return WCF::getTPL()->fetch('itemDatabaseType', 'rp');
    }

    /**
     * @inheritDoc
     */
    public function validate(Option $option, $newValue): void
    {
        if (!\is_array($newValue)) {
            $newValue = [];
        }

        foreach ($newValue as $databaseName) {
            if (!\in_array($databaseName, self::getDatabases())) {
                throw new UserInputException($option->optionName, 'validationFailed');
            }
        }
    }
}
