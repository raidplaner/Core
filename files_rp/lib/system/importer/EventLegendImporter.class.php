<?php

namespace rp\system\importer;

use rp\data\event\legend\EventLegend;
use rp\data\event\legend\EventLegendEditor;
use wcf\system\importer\AbstractImporter;
use wcf\system\importer\ImportHandler;


/**
 * Imports event legends.
 * 
 * @author  Marco Daries
 * @package     Daries\RP\System\Importer
 */
class EventLegendImporter extends AbstractImporter
{
    /**
     * @inheritDoc
     */
    protected $className = EventLegend::class;

    /**
     * @inheritDoc
     */
    public function import($oldID, array $data, array $additionalData = []): mixed
    {
        // create legend
        $legend = EventLegendEditor::create($data);

        // save mapping
        ImportHandler::getInstance()->saveNewID('dev.daries.rp.event.legend', $oldID, $legend->legendID);

        return $legend->legendID;
    }
}
