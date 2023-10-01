<?php

namespace rp\data\rank;

use rp\system\cache\builder\RankCacheBuilder;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\ISortableAction;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;


/**
 * Executes rank related actions.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 * 
 * @method	RankEditor[]    getObjects()
 * @method	RankEditor      getSingleObject()
 */
class RankAction extends AbstractDatabaseObjectAction implements ISortableAction
{
    /**
     * @inheritDoc
     */
    protected $className = RankEditor::class;

    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['admin.rp.canManageRank'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.rp.canManageRank'];

    /**
     * @inheritDoc
     */
    protected $permissionsUpdate = ['admin.rp.canManageRank'];

    /**
     * @inheritDoc
     */
    protected $requireACP = ['create', 'delete', 'setAsDefault', 'update', 'updatePosition'];

    /**
     * @inheritDoc
     */
    public function create(): Rank
    {
        if (!isset($this->parameters['data']['gameID'])) {
            $this->parameters['data']['gameID'] = RP_DEFAULT_GAME_ID;
        }

        if (isset($this->parameters['data']['showOrder']) && $this->parameters['data']['showOrder'] !== null) {
            $sql = "UPDATE  rp" . WCF_N . "_rank
                    SET     showOrder = showOrder + 1
                    WHERE   showOrder >= ?
                        AND gameID = ?";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute([
                $this->parameters['data']['showOrder'],
                $this->parameters['data']['gameID']
            ]);
        }

        return parent::create();
    }

    /**
     * @inheritDoc
     */
    public function delete(): int
    {
        $returnValues = parent::delete();

        $sql = "UPDATE  rp" . WCF_N . "_rank
                SET     showOrder = showOrder - 1
                WHERE   showOrder > ?
                    AND gameID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        foreach ($this->getObjects() as $object) {
            $statement->execute([
                $object->showOrder,
                $object->gameID
            ]);
        }

        return $returnValues;
    }

    /**
     * Sets rank as default
     */
    public function setAsDefault(): void
    {
        $rankEditor = \current($this->objects);
        $rankEditor->setAsDefault();
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        parent::update();

        foreach ($this->getObjects() as $object) {
            // update show order
            if (isset($this->parameters['data']['showOrder']) && $this->parameters['data']['showOrder'] !== null) {
                $sql = "UPDATE  rp" . WCF_N . "_rank
                        SET     showOrder = showOrder + 1
                        WHERE   showOrder >= ?
                            AND rankID <> ?
                            AND gameID = ?";
                $statement = WCF::getDB()->prepareStatement($sql);
                $statement->execute([
                    $this->parameters['data']['showOrder'],
                    $object->rankID,
                    $object->gameID
                ]);

                $sql = "UPDATE  rp" . WCF_N . "_rank
                        SET     showOrder = showOrder - 1
                        WHERE   showOrder > ?
                            AND gameID = ?";
                $statement = WCF::getDB()->prepareStatement($sql);
                $statement->execute([
                    $object->showOrder,
                    $object->gameID
                ]);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function updatePosition(): void
    {
        $ranks = RankCache::getInstance()->getRanks();

        $i = $this->parameters['data']['offset'];
        WCF::getDB()->beginTransaction();
        foreach ($this->parameters['data']['structure'][0] as $rankID) {
            if (!isset($ranks[$rankID])) continue;

            $editor = new RankEditor($ranks[$rankID]);
            $editor->update(['showOrder' => $i++]);
        }
        WCF::getDB()->commitTransaction();

        RankCacheBuilder::getInstance()->reset();
    }

    /**
     * Validates parameters to assign a new default rank.
     */
    public function validateSetAsDefault(): void
    {
        if (!WCF::getSession()->getPermission('admin.rp.canManageRank')) {
            throw new PermissionDeniedException();
        }

        if (empty($this->objects)) {
            $this->readObjects();
            if (empty($this->objects)) {
                throw new UserInputException('objectIDs');
            }
        }

        if (\count($this->objects) > 1) {
            throw new UserInputException('objectIDs');
        }
    }

    /**
     * @inheritDoc
     */
    public function validateUpdatePosition(): void
    {
        // validate permissions
        if (\is_array($this->permissionsUpdate) && \count($this->permissionsUpdate)) {
            WCF::getSession()->checkPermissions($this->permissionsUpdate);
        } else {
            throw new PermissionDeniedException();
        }

        if (!isset($this->parameters['data']['structure'])) {
            throw new UserInputException('structure');
        }

        $this->readInteger('offset', true, 'data');
        $this->readInteger('gameID');
    }
}
