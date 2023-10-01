<?php

namespace rp\system\importer;

use wcf\system\importer\AbstractCommentResponseImporter;


/**
 * Imports event comment response.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class EventCommentResponseImporter extends AbstractCommentResponseImporter
{
    /**
     * @inheritDoc
     */
    protected $objectTypeName = 'dev.daries.rp.event.comment';

}
