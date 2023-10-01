<?php

namespace rp\system\cache\builder;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\io\HttpFactory;
use wcf\util\Url;


/**
 * Cache the server status from the URL for 30 minutes.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class ServerStatusCacheBuilder extends AbstractCacheBuilder
{
    /**
     * @inheritDoc
     */
    protected $maxLifetime = 300;

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters): array
    {
        if (!Url::is($parameters['url'])) {
            throw new \InvalidArgumentException('Given URL "' . $parameters['url'] . '" is not a valid URL.');
        }

        $client = HttpFactory::makeClientWithTimeout(5);
        $request = new Request('GET', $parameters['url']);

        try {
            $response = $client->send($request);

            if ($response->getStatusCode() !== 200) {
                $content = null;
            } else {
                $content = (string)$response->getBody();
            }
        } catch (ClientExceptionInterface $e) {
            $content = null;
        }

        return ['content' => $content];
    }
}
