<?php

namespace rp\data\game;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\option\OptionEditor;
use wcf\system\WCF;


/**
 * Executes game related actions.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @method      GameEditor[]    getObjects()
 * @method      GameEditor      getSingleObject()
 */
class GameAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = GameEditor::class;

    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['admin.rp.canManageGame'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.rp.canManageGame'];

    /**
     * @inheritDoc
     */
    protected $permissionsUpdate = ['admin.rp.canManageGame'];

    /**
     * @inheritDoc
     */
    protected $requireACP = ['create', 'delete', 'update'];

    public function delete()
    {
        $return = parent::delete();

        // set default game
        $sql = "SELECT  gameID
                FROM    rp" . WCF_N . "_game
                WHERE   identifier = ?";
        $statement = WCF::getDB()->prepareStatement($sql, 1);
        $statement->execute(['default']);
        $gameID = $statement->fetchSingleColumn();

        $sql = "UPDATE  wcf" . WCF_N . "_option
                SET     optionValue = ?
                WHERE   optionName = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
            $gameID,
            'rp_default_game_id',
        ]);

        // update options.inc.php
        OptionEditor::resetCache();
        
        return $return;
    }
}
