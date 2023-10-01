<?php

namespace rp\system\event\listener;

use wcf\system\event\listener\AbstractUserActionRenameListener;


/**
 * Updates the stored username on user rename.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class UserActionRenameListener extends AbstractUserActionRenameListener
{
    /**
     * @inheritDoc
     */
    protected $databaseTables = [
        'rp{WCF_N}_event',
    ];

}
