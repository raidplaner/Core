<?php

namespace rp\data\character;


/**
 * Represents a list of character profiles.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 *
 * @method      CharacterProfile        current()
 * @method      CharacterProfile[]      getObjects()
 * @method      CharacterProfile|null   search($objectID)
 * @property    CharacterProfile[]      $objects
 */
class CharacterProfileList extends CharacterList
{
    /**
     * @inheritDoc
     */
    public $sqlOrderBy = 'characterName';

    /**
     * @inheritDoc
     */
    public $decoratorClassName = CharacterProfile::class;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();

        if (!empty($this->sqlSelects)) {
            $this->sqlSelects .= ',';
        }
        $this->sqlSelects .= "member_avatar.*";
        $this->sqlJoins .= "
            LEFT JOIN   rp" . WCF_N . "_member_avatar member_avatar
            ON          member_avatar.avatarID = member.avatarID";

        if (RP_ENABLE_RANK) {
            $this->sqlSelects .= ",rank.*";
            $this->sqlJoins .= "
                LEFT JOIN   rp" . WCF_N . "_rank rank
                ON          rank.rankID = member.rankID";
        }
    }

    /**
     * @inheritDoc
     */
    public function readObjects(): void
    {
        if ($this->objectIDs === null) {
            $this->readObjectIDs();
        }

        parent::readObjects();
    }
}
