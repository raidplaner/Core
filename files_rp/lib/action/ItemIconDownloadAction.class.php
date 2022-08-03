<?php

namespace rp\action;

use rp\data\item\Item;
use rp\data\item\ItemEditor;
use wcf\action\AbstractAction;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\SystemException;
use wcf\system\WCF;
use wcf\util\FileUtil;
use wcf\util\HTTPRequest;

/**
 *  Project:    Raidplaner: Core
 *  Package:    info.daries.rp
 *  Link:       http://daries.info
 *
 *  Copyright (C) 2018-2022 Daries.info Developer Team
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
 * Downloads and caches item icons.
 *
 * @author      Marco Daries
 * @package     Daries\RP\Action
 */
class ItemIconDownloadAction extends AbstractAction
{
    /**
     * item object
     */
    public Item $item;

    /**
     * item id
     */
    public int $itemID = 0;

    /**
     * @inheritDoc
     */
    public function execute(): void
    {
        parent::execute();

        $fileExtension = ($this->item->itemIconFileExtension ?: $this->item->iconExtension);

        // try to use cached item icon
        $cachedFilename = \sprintf(
            Item::ITEM_ICON_CACHE_LOCATION,
            \md5(\mb_strtolower($this->item->icon)),
            $fileExtension
        );
        if (\file_exists(RP_DIR . $cachedFilename) && \filemtime(RP_DIR . $cachedFilename) > (TIME_NOW - (Item::ITEM_ICON_CACHE_EXPIRE * 86400))) {
            @\header('Content-Type: image/png');
            @\readfile(RP_DIR . $cachedFilename);
            exit;
        }

        // try to download new version
        $iconURL = $this->item->iconURL . $this->item->icon . '.' . $this->item->iconExtension;
        try {
            $request = new HTTPRequest($iconURL);
            $request->execute();
            $reply = $request->getReply();

            // get mime type and file extension
            $fileExtension = 'png';
            $mimeType = 'image/png';
            if (isset($reply['headers']['Content-Type'])) {
                switch ($reply['headers']['Content-Type']) {
                    case 'image/jpeg':
                        $mimeType = 'image/jpeg';
                        $fileExtension = 'jpg';
                        break;
                    case 'image/gif':
                        $mimeType = 'image/gif';
                        $fileExtension = 'gif';
                        break;
                }
            }

            // save file
            $cachedFilename = \sprintf(
                Item::ITEM_ICON_CACHE_LOCATION,
                \md5(\mb_strtolower($this->item->icon)),
                $fileExtension
            );

            \file_put_contents(RP_DIR . $cachedFilename, $reply['body']);
            FileUtil::makeWritable(RP_DIR . $cachedFilename);

            // update file extension
            if ($fileExtension != $this->item->itemIconFileExtension) {
                $additionalData = $this->item->additionalData;
                $additionalData['itemIconFileExtension'] = $fileExtension;

                $editor = new ItemEditor($this->item);
                $editor->update([
                    'additionalData' => \serialize($additionalData),
                ]);
            }

            @\header('Content-Type: ' . $mimeType);
            @\readfile(RP_DIR . $cachedFilename);
            exit;
        } catch (SystemException $e) {
            
        }

        // fallback to default item icon
        @\header('Content-Type: image/png');
        @\readfile(WCF::getPath() . 'images/placeholderTiny.png');
        exit;
    }

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        if (isset($_REQUEST['itemID'])) $this->itemID = \intval($_REQUEST['itemID']);
        $this->item = new Item($this->itemID);
        if (!$this->item->itemID) {
            throw new IllegalLinkException();
        }
    }
}
