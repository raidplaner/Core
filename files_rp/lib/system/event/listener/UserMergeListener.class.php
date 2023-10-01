<?php

namespace rp\system\event\listener;

use wcf\system\event\listener\AbstractUserMergeListener;


/**
 * Updates events and members during user merging.
 * 
 * @author  Marco Daries
 * @package     Daries\RP\System\Event\Listener
 */
class UserMergeListener extends AbstractUserMergeListener
{
    /**
     * @inheritDoc
     */
    protected $databaseTables = [
        'rp{WCF_N}_event',
        'rp{WCF_N}_member',
        [
            'name' => 'rp{WCF_N}_member',
            'username' => null,
        ],
    ];

}
