<?php

namespace rp\system\importer;

use wcf\system\importer\AbstractCommentResponseImporter;


/**
 * Imports event comment response.
 *
 * @author  Marco Daries
 * @package     Daries\RP\System\Importer
 */
class EventCommentResponseImporter extends AbstractCommentResponseImporter
{
    /**
     * @inheritDoc
     */
    protected $objectTypeName = 'dev.daries.rp.event.comment';

}
