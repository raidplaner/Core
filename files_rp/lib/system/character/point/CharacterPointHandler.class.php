<?php

namespace rp\system\character\point;

use rp\data\character\CharacterProfile;
use rp\system\cache\builder\CharacterPointCacheBuilder;
use rp\util\RPUtil;
use wcf\system\SingletonFactory;


/**
 * Handles character points.
 *
 * @author  Marco Daries
 * @package     Daries\RP\System\Character\Point
 */
class CharacterPointHandler extends SingletonFactory
{
    /**
     * loaded character points
     */
    protected array $characterPoints = [];

    public function getPoints(CharacterProfile $characterProfile): array
    {
        $primaryCharacter = $characterProfile->getPrimaryCharacter();
        $this->loadCharacterPoints($primaryCharacter);

        if (RP_SHOW_TWINKS) $datas = $this->characterPoints[$characterProfile->characterID] ?? [];
        else $datas = $this->characterPoints[$primaryCharacter->characterID] ?? [];
        
        return $datas;
    }

    /**
     * Loads primary character points and twinks.
     */
    protected function loadCharacterPoints(CharacterProfile $primaryCharacter): void
    {
        $primaryCharacterID = $primaryCharacter->characterID;

        if (!isset($this->characterPoints[$primaryCharacterID])) {
            $datas = CharacterPointCacheBuilder::getInstance()->getData(['primaryCharacterID' => $primaryCharacterID]);

            if (RP_SHOW_TWINKS) $this->characterPoints = $datas;
            else {
                $newDatas = [];
                foreach ($datas as $characterID => $pointAccounts) {
                    foreach ($pointAccounts as $pointAccountID => $data) {
                        if (!isset($newDatas[$pointAccountID])) {
                            $newDatas[$pointAccountID] = $data;
                            continue;
                        }

                        $newDatas[$pointAccountID]['received']['points'] += $data['received']['points'];
                        if ($newDatas[$pointAccountID]['received']['points'] > 0) $newDatas[$pointAccountID]['received']['color'] = 'green';
                        
                        $newDatas[$pointAccountID]['adjustments']['points'] += $data['adjustments']['points'];
                        if ($newDatas[$pointAccountID]['adjustments']['points'] > 0) $newDatas[$pointAccountID]['adjustments']['color'] = 'red';

                        if ($data['issued']['points'] > 0) {
                            $newDatas[$pointAccountID]['issued']['points'] += $data['issued']['points'];
                            $newDatas[$pointAccountID]['issued']['color'] = 'red';
                        }

                        $current = $newDatas[$pointAccountID]['received']['points'] - $newDatas[$pointAccountID]['issued']['points'];
                        $newDatas[$pointAccountID]['current']['points'] = $current;

                        if ($newDatas[$pointAccountID]['current']['points'] < 0) $newDatas[$pointAccountID]['current']['color'] = 'red';
                        elseif ($newDatas[$pointAccountID]['current']['points'] > 0) $newDatas[$pointAccountID]['current']['color'] = 'green';
                    }
                }

                $this->characterPoints[$primaryCharacterID] = $newDatas;
            }
        }
    }
}
