<?php

namespace rp\data\game;

use rp\system\cache\builder\GameCacheBuilder;
use wcf\system\SingletonFactory;


/**
 * Manages the game cache.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class GameCache extends SingletonFactory
{
    /**
     * cached game ids with game identifier as key
     * @var int[]
     */
    protected array $cachedIdentifier = [];

    /**
     * cached games
     * @var Game[]
     */
    protected array $cachedGames = [];

    /**
     * Returns the current selected game.
     */
    public function getCurrentGame(): Game
    {
        $game = $this->getGameByID(RP_DEFAULT_GAME_ID);

        if ($game === null) {
            // fallback to default game
            $game = new Game(null, [
                'identifier' => 'default',
            ]);
        }

        return $game;
    }

    /**
     * Returns the game with the given game id or `null` if no such game exists.
     */
    public function getGameByID(int $gameID): ?Game
    {
        return $this->cachedGames[$gameID] ?? null;
    }

    /**
     * Returns the game with the given game identifier or `null` if no such game exists.
     */
    public function getGameByIdentifier(string $identifier): ?Game
    {
        if (!isset($this->cachedIdentifier[$identifier])) return null;
        return $this->getGameByID($this->cachedIdentifier[$identifier]);
    }

    /**
     * Returns all games.
     * 
     * @return  Game[]
     */
    public function getGames(): array
    {
        return $this->cachedGames;
    }

    /**
     * Returns the game with the given game ids.
     * 
     * @return	Game[]
     */
    public function getGamesByID(array $gameIDs): array
    {
        $returnValues = [];

        foreach ($gameIDs as $gameID) {
            $returnValues[] = $this->getGameByID($gameID);
        }

        return $returnValues;
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $this->cachedGames = GameCacheBuilder::getInstance()->getData([], 'games');
        $this->cachedIdentifier = GameCacheBuilder::getInstance()->getData([], 'identifier');
    }
}
