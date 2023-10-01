<?php

namespace rp\system;

use rp\page\CalendarPage;
use rp\system\character\CharacterHandler;
use rp\system\character\point\CharacterPointHandler;
use rp\system\menu\character\profile\CharacterProfileMenu;
use wcf\system\application\AbstractApplication;
use wcf\system\request\route\StaticRequestRoute;
use wcf\system\request\RouteHandler;


/**
 * This class extends the main WCF class by raidplaner specific functions.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class RPCore extends AbstractApplication
{
    /**
     * @inheritDoc
     */
    protected $primaryController = CalendarPage::class;

    /**
     * @inheritDoc
     */
    public function __run(): void
    {
        $route = new StaticRequestRoute();
        $route->setStaticController('rp', 'Calendar');
        $route->setBuildSchema('/{controller}/{year}/{month}/');
        $route->setPattern('~^/?(?P<controller>[^/]+)/(?P<year>\d{4})(?:/(?P<month>\d{1,2}))?~x');
        $route->setRequiredComponents(['year' => '~^\d{4}$~']);
        $route->setMatchController(true);
        RouteHandler::getInstance()->addRoute($route);
    }

    /**
     * Returns the character handler of the current user
     */
    public function getCharacter(): CharacterHandler
    {
        return CharacterHandler::getInstance();
    }

    /**
     * Returns the character point handler
     */
    public function getCharacterPointHandler(): CharacterPointHandler
    {
        return CharacterPointHandler::getInstance();
    }

    /**
     * Returns the character profile menu
     */
    public function getCharacterProfileMenu(): CharacterProfileMenu
    {
        return CharacterProfileMenu::getInstance();
    }
}
