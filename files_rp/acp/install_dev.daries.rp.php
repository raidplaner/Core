<?php

use rp\data\game\GameCache;
use wcf\data\option\OptionEditor;
use wcf\system\WCF;

/**
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
// set default game
$sql = "SELECT  gameID
        FROM    rp1_game
        WHERE   identifier = ?";
$statement = WCF::getDB()->prepare($sql, 1);
$statement->execute(['default']);
$gameID = $statement->fetchSingleColumn();

$sql = "UPDATE  wcf1_option
        SET     optionValue = ?
        WHERE   optionName = ?";
$statement = WCF::getDB()->prepare($sql);
$statement->execute([
    $gameID,
    'rp_default_game_id',
]);

// update options.inc.php
OptionEditor::resetCache();

// Default Rank
$sql = "INSERT INTO rp1_rank
                    (rankName, gameID, showOrder, isDefault)
        VALUES      (?, ?, ?, ?)";
$statement = WCF::getDB()->prepare($sql);
$statement->execute([
    'Default',
    GameCache::getInstance()->getGameByIdentifier('default')->gameID,
    1,
    1,
]);

// Default Point Account
$sql = "INSERT INTO rp1_point_account
                    (pointAccountName, description, gameID, showOrder)
        VALUES      (?, ?, ?, ?)";
$statement = WCF::getDB()->prepare($sql);
$statement->execute([
    'Default',
    'Default-Pool',
    GameCache::getInstance()->getGameByIdentifier('default')->gameID,
    1,
]);
