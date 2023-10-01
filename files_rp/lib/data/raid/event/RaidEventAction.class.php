<?php

namespace rp\data\raid\event;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\package\PackageCache;
use wcf\system\language\I18nHandler;
use wcf\system\upload\UploadFile;


/**
 * Executes raid event related actions.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @method      RaidEventEditor[]   getObjects()
 * @method      RaidEventEditor     getSingleObject()
 */
class RaidEventAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = RaidEventEditor::class;

    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['admin.rp.canManageRaidEvent'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.rp.canManageRaidEvent'];

    /**
     * @inheritDoc
     */
    protected $permissionsUpdate = ['admin.rp.canManageRaidEvent'];

    /**
     * @inheritDoc
     */
    protected $requireACP = ['create', 'delete', 'update'];

    /**
     * @inheritDoc
     */
    public function create(): RaidEvent
    {
        if (!isset($this->parameters['data']['gameID'])) {
            $this->parameters['data']['gameID'] = RP_DEFAULT_GAME_ID;
        }

        // The eventName cannot be empty by design, but cannot be filled proper if the
        // multilingualism is enabled, therefore, we must fill the tilte with a dummy value.
        if (!isset($this->parameters['data']['eventName']) && isset($this->parameters['eventName_i18n'])) {
            $this->parameters['data']['eventName'] = 'wcf.global.name';
        }

        /** @var RaidEvent $raidEvent */
        $raidEvent = parent::create();
        $raidEventEditor = new RaidEventEditor($raidEvent);

        $updateData = [];

        // i18n
        if (isset($this->parameters['eventName_i18n'])) {
            I18nHandler::getInstance()->save(
                $this->parameters['eventName_i18n'],
                'rp.raid.event.name' . $raidEvent->eventID,
                'rp.raid.event',
                PackageCache::getInstance()->getPackageID('dev.daries.rp')
            );

            $updateData['eventName'] = 'rp.raid.event.name' . $raidEvent->eventID;
        }

        // image
        if (empty($raidEvent->icon) && isset($this->parameters['iconFile']) && \is_array($this->parameters['iconFile'])) {
            $iconFile = \reset($this->parameters['iconFile']);
            if (!($iconFile instanceof UploadFile)) {
                throw new \InvalidArgumentException("The parameter 'icon' is no instance of '" . UploadFile::class . "', instance of '" . \get_class($iconFile) . "' given.");
            }

            // save new image
            if (!$iconFile->isProcessed()) {
                $fileName = $iconFile->getFilename();

                \rename($iconFile->getLocation(), RP_DIR . '/images/raid/event/' . $fileName);
                $iconFile->setProcessed(RP_DIR . '/images/raid/event/' . $fileName);

                $ext = \explode('.', $filename);
                \array_pop($ext);
                $updateData['icon'] = \implode($ext);
            }
        }

        if (!empty($updateData)) {
            $raidEventEditor->update($updateData);
        }

        return $raidEvent;
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        parent::update();

        foreach ($this->getObjects() as $object) {
            $updateData = [];

            // i18n
            if (isset($this->parameters['eventName_i18n'])) {
                I18nHandler::getInstance()->save(
                    $this->parameters['eventName_i18n'],
                    'rp.raid.event.name' . $object->eventID,
                    'rp.raid.event',
                    PackageCache::getInstance()->getPackageID('dev.daries.rp')
                );

                $updateData['eventName'] = 'rp.raid.event.name' . $object->eventID;
            }

            if (!empty($updateData)) {
                $object->update($updateData);
            }
        }
    }
}
