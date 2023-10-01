<?php

namespace rp\form;

use rp\data\character\CharacterList;
use rp\system\condition\ICondition;
use wcf\data\object\type\ObjectType;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\search\SearchEditor;
use wcf\form\AbstractForm;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\event\EventHandler;
use wcf\system\exception\UserInputException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\container\TabFormContainer;
use wcf\system\form\builder\container\TabMenuFormContainer;
use wcf\system\form\builder\data\processor\VoidFormDataProcessor;
use wcf\system\page\PageLocationManager;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;


/**
 * Shows the character search form.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class CharacterSearchForm extends AbstractFormBuilderForm
{
    /**
     * list with searched characters
     */
    public CharacterList $characterList;

    /**
     * list of grouped character group assignment condition object types
     * @var ObjectType[][]
     */
    public array $conditions = [];

    /**
     * search id
     * @var int
     */
    public $searchID = 0;

    /**
     * number of results
     * @var int
     */
    public $maxResults = 1000;

    /**
     * @inheritDoc
     */
    public function createForm(): void
    {
        $objectTypes = ObjectTypeCache::getInstance()->getObjectTypes('dev.daries.rp.condition.characterSearch');
        foreach ($objectTypes as $objectType) {
            if (!$objectType->conditiongroup) continue;
            if (!isset($this->conditions[$objectType->conditiongroup])) $this->conditions[$objectType->conditiongroup] = [];

            $this->conditions[$objectType->conditiongroup][$objectType->objectTypeID] = $objectType;
        }

        parent::createForm();

        $tabMenu = TabMenuFormContainer::create('characterSearchTabMenu');
        $this->form->appendChild($tabMenu);

        foreach ($this->conditions as $conditionGroup => $conditionObjectTypes) {
            $tab = TabFormContainer::create('character_' . $conditionGroup . 'Tab');
            $tab->label('rp.acp.character.condition.conditionGroup.' . $conditionGroup);
            $tabMenu->appendChild($tab);

            $container = FormContainer::create('character_' . $conditionGroup);
            $tab->appendChild($container);

            foreach ($conditionObjectTypes as $condition) {
                $container->appendChild($condition->getProcessor()->getFormField());
                $this->form->getDataHandler()->addProcessor(new VoidFormDataProcessor($condition->getProcessor()->getID()));
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function readData(): void
    {
        parent::readData();

        PageLocationManager::getInstance()->addParentLocation('dev.daries.rp.CharactersList');
    }

    /**
     * @inheritDoc
     */
    public function save(): void
    {
        AbstractForm::save();

        // store search result in database
        $data = \serialize([
            'matches' => $this->characterList->getObjectIDs(),
        ]);

        $search = SearchEditor::create([
                'searchData' => $data,
                'searchTime' => TIME_NOW,
                'searchType' => 'characters',
                'userID' => WCF::getUser()->userID ?? null,
        ]);

        //get new search id
        $this->searchID = $search->searchID;
        $this->saved();

        // forward to result page
        HeaderUtil::redirect(LinkHandler::getInstance()->getLink(
                'CharactersList',
                [
                    'application' => 'rp',
                    'id' => $this->searchID
                ],
            )
        );
        exit;
    }

    /**
     * Search for characters which fit to the search values.
     */
    protected function search(): void
    {
        $this->characterList = new CharacterList();
        $this->characterList->sqlLimit = $this->maxResults;

        EventHandler::getInstance()->fireAction($this, 'search');

        // read character ids
        foreach ($this->conditions as $groupedObjectTypes) {
            /** @var ObjectType $objectType */
            foreach ($groupedObjectTypes as $objectType) {
                /** @var ICondition $processor */
                $processor = $objectType->getProcessor();
                $processor->addObjectListCondition($this->characterList, $this->form);
            }
        }
        $this->characterList->readObjectIDs();
    }

    /**
     * @inheritDoc
     */
    public function validate(): void
    {
        parent::validate();

        $this->search();

        if (!\count($this->characterList->getObjectIDs())) {
            throw new UserInputException('search', 'noMatches');
        }
    }
}
