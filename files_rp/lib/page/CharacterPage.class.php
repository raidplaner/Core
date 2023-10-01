<?php

namespace rp\page;

use rp\data\character\CharacterEditor;
use rp\data\character\CharacterProfile;
use rp\system\cache\runtime\CharacterProfileRuntimeCache;
use rp\system\menu\character\profile\CharacterProfileMenu;
use wcf\page\AbstractPage;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\MetaTagHandler;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;


/**
 * Shows the character profile page.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class CharacterPage extends AbstractPage
{
    /**
     * character id
     */
    public int $characterID = 0;

    /**
     * character object
     */
    public ?CharacterProfile $character;

    /**
     * profile content for active menu item
     */
    public string $profileContent = '';

    /**
     * @inheritDoc
     */
    public function assignVariables(): void
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'character' => $this->character,
            'characterID' => $this->characterID,
            'profileContent' => $this->profileContent,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function readData(): void
    {
        parent::readData();

        $activeMenuItem = CharacterProfileMenu::getInstance()->getActiveMenuItem($this->character->characterID);
        $contentManager = $activeMenuItem->getContentManager();
        $this->profileContent = $contentManager->getContent($this->character->characterID);

        MetaTagHandler::getInstance()->addTag(
            'og:url',
            'og:url',
            LinkHandler::getInstance()->getLink(
                'Character',
                [
                    'application' => 'rp',
                    'object' => $this->character->getDecoratedObject()
                ]
            ),
            true
        );
        MetaTagHandler::getInstance()->addTag('og:type', 'og:type', 'profile', true);
        MetaTagHandler::getInstance()->addTag('profile:username', 'profile:username', $this->character->characterName, true);
        MetaTagHandler::getInstance()->addTag(
            'og:title',
            'og:title',
            $this->character->characterName . ' - ' . WCF::getLanguage()->get('rp.character.characters') . ' - ' . WCF::getLanguage()->get(\PAGE_TITLE),
            true
        );
        MetaTagHandler::getInstance()->addTag('og:image', 'og:image', $this->character->getAvatar()->getURL(), true);
    }

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        if (isset($_REQUEST['id'])) {
            $this->characterID = \intval($_REQUEST['id']);
        }
        $this->character = CharacterProfileRuntimeCache::getInstance()->getObject($this->characterID);
        if ($this->character === null) {
            throw new IllegalLinkException();
        }

        if ($this->character->userID != WCF::getUser()->userID && !WCF::getSession()->getPermission('user.rp.canViewCharacterProfile')) {
            throw new PermissionDeniedException();
        }

        $this->canonicalURL = $this->character->getLink();
    }

    /**
     * @inheritDoc
     */
    public function show()
    {
        // update profile hits
        if ($this->character->userID != WCF::getUser()->userID && !WCF::getSession()->spiderID) {
            $editor = new CharacterEditor($this->character->getDecoratedObject());
            $editor->updateCounters(['profileHits' => 1]);
        }

        parent::show();
    }
}
