<?php

use wcf\system\database\table\column\NotNullVarchar255DatabaseTableColumn;
use wcf\system\database\table\PartialDatabaseTable;

/**
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
return [
        PartialDatabaseTable::create('rp1_event_legend')
        ->columns([
            NotNullVarchar255DatabaseTableColumn::create('frontColor')
            ->defaultValue('')
        ])
];