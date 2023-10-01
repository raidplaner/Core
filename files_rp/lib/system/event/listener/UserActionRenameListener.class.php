<?php

namespace rp\system\event\listener;

use wcf\system\event\listener\AbstractUserActionRenameListener;


/**
 * Updates the stored username on user rename.
 * 
 * @author  Marco Daries
 * @package     Daries\RP\System\Event\Listener
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
