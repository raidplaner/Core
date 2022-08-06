<?php

namespace rp\system\cache\builder;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\io\HttpFactory;
use wcf\util\Url;

/**
 *  Project:    Raidplaner: Core
 *  Package:    info.daries.rp
 *  Link:       http://daries.info
 *
 *  Copyright (C) 2018-2022 Daries.info Developer Team
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Cache the server status from the URL for 30 minutes.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\System\Cache\Builder
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
