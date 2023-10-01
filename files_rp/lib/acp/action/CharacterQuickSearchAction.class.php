<?php

namespace rp\acp\action;

use Laminas\Diactoros\Response\RedirectResponse;
use wcf\action\AbstractAction;
use wcf\data\search\SearchEditor;
use wcf\system\exception\NamedUserException;
use wcf\system\menu\acp\ACPMenu;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;


/**
 * Provides special search options.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class CharacterQuickSearchAction extends AbstractAction
{
    /**
     * results per page
     */
    public int $itemsPerPage = 50;

    /**
     * matches
     * @var int[]
     */
    public array $matches = [];

    /**
     * number of results
     */
    public int $maxResults = 2000;

    /**
     * search mode
     */
    public string $mode = '';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.rp.canSearchCharacter'];

    /**
     * sort field
     */
    public string $sortField = 'characterName';

    /**
     * sort order
     */
    public string $sortOrder = 'ASC';

    /**
     * @inheritDoc
     */
    public function execute(): RedirectResponse
    {
        ACPMenu::getInstance()->setActiveMenuItem('rp.acp.menu.link.character.search');

        parent::execute();

        switch ($this->mode) {
            case 'disabled':
                $this->sortField = 'created';
                $this->sortOrder = 'DESC';
                $sql = "SELECT      characterID
                        FROM        rp" . WCF_N . "_member
                        WHERE       isDisabled = ?
                        ORDER BY    created DESC";
                $statement = WCF::getDB()->prepareStatement($sql, $this->maxResults);
                $statement->execute([1]);
                $this->matches = $statement->fetchAll(\PDO::FETCH_COLUMN);
                break;
        }

        if (empty($this->matches)) {
            throw new NamedUserException(WCF::getLanguage()->get('rp.acp.character.search.error.noMatches'));
        }

        // store search result in database
        $data = \serialize([
            'itemsPerPage' => $this->itemsPerPage,
            'matches' => $this->matches,
        ]);

        $search = SearchEditor::create([
                'searchData' => $data,
                'searchTime' => TIME_NOW,
                'searchType' => 'characters',
                'userID' => WCF::getUser()->userID,
        ]);
        $this->executed();

        // forward to result page
        $url = LinkHandler::getInstance()->getLink(
            'CharacterList',
            [
                'application' => 'rp',
                'id' => $search->searchID,
            ],
            'sortField=' . \rawurlencode($this->sortField) . '&sortOrder=' . \rawurlencode($this->sortOrder)
        );

        return new RedirectResponse(
            $url
        );
    }

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        if (isset($_REQUEST['mode'])) {
            $this->mode = $_REQUEST['mode'];
        }
    }
}
