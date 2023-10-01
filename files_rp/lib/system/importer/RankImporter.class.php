<?php

namespace rp\system\importer;

use rp\data\rank\Rank;
use rp\data\rank\RankEditor;
use wcf\system\importer\AbstractImporter;
use wcf\system\importer\ImportHandler;


/**
 * Imports ranks.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class RankImporter extends AbstractImporter
{
    /**
     * @inheritDoc
     */
    protected $className = Rank::class;

    /**
     * @inheritDoc
     */
    public function import($oldID, array $data, array $additionalData = []): mixed
    {
        $data['gameID'] ??= RP_DEFAULT_GAME_ID;

        // create rank
        $rank = RankEditor::create($data);

        // save mapping
        ImportHandler::getInstance()->saveNewID('dev.daries.rp.rank', $oldID, $rank->rankID);

        return $rank->rankID;
    }
}
