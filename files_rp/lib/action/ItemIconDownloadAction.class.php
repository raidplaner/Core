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
 * Downloads and caches item icons.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
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
