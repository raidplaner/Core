<?php

namespace rp\system\importer;

use rp\data\character\avatar\CharacterAvatar;
use rp\data\character\avatar\CharacterAvatarEditor;
use wcf\system\exception\SystemException;
use wcf\system\importer\AbstractImporter;
use wcf\system\importer\ImportHandler;
use wcf\system\WCF;
use wcf\util\FileUtil;
use const WCF_N;

/**
 *  Project:    Raidplaner: Core
 *  Package:    dev.daries.rp
 *  Link:       http://daries.dev
 *
 *  Copyright (C) 2018-2023 Daries.dev Developer Team
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Imports character avatars.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Importer
 */
class CharacterAvatarImporter extends AbstractImporter
{
    /**
     * @inheritDoc
     */
    protected $className = CharacterAvatar::class;

    /**
     * @inheritDoc
     */
    public function import($oldID, array $data, array $additionalData = []): mixed
    {
        // check file location
        if (!@\file_exists($additionalData['fileLocation'])) {
            return 0;
        }

        // get image size
        $imageData = @\getimagesize($additionalData['fileLocation']);
        if ($imageData === false) {
            return 0;
        }
        $data['width'] = $imageData[0];
        $data['height'] = $imageData[1];

        // check image type
        if ($imageData[2] != \IMAGETYPE_GIF && $imageData[2] != \IMAGETYPE_JPEG && $imageData[2] != \IMAGETYPE_PNG) {
            return 0;
        }

        // get file hash
        if (empty($data['fileHash'])) {
            $data['fileHash'] = \sha1_file($additionalData['fileLocation']);
        }

        // get character id
        $data['characterID'] = ImportHandler::getInstance()->getNewID('dev.daries.rp.character', $data['characterID']);
        if (!$data['characterID']) {
            return 0;
        }

        // save character
        $avatar = CharacterAvatarEditor::create($data);

        // check avatar directory
        // and create subdirectory if necessary
        $dir = \dirname($avatar->getLocation());
        if (!@\file_exists($dir)) {
            FileUtil::makePath($dir);
        }

        // copy file
        try {
            if (!\copy($additionalData['fileLocation'], $avatar->getLocation())) {
                throw new SystemException();
            }

            // update owner
            $sql = "UPDATE  rp" . WCF_N . "_member
                    SET     avatarID = ?
                    WHERE   characterID = ?";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute([$avatar->avatarID, $data['characterID']]);

            return $avatar->avatarID;
        } catch (SystemException $e) {
            // copy failed; delete avatar
            $editor = new CharacterAvatarEditor($avatar);
            $editor->delete();
        }

        return 0;
    }
}
