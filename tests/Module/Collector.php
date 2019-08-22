<?php
/* ============================================================================
 * Copyright 2019 Zindex Software
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================================ */

namespace Opis\Colibri\Modules\JsonAPI\Test\Module;

use Opis\Colibri\Modules\JsonAPI\Collectors\SchemaResolverCollector;
use Opis\Colibri\Collector as AbstractCollector;
use Opis\Colibri\ItemCollectors\RouteCollector;
use function Opis\Colibri\Functions\controller;

class Collector extends AbstractCollector
{
    /**
     * @param RouteCollector $route
     */
    public function routes(RouteCollector $route)
    {
        $route->group(function (RouteCollector $route) {
            $ctrl = controller('@controller', '@action');

            $route('/{controller}', $ctrl, ['GET', 'POST', 'OPTIONS'])
                ->implicit('actionScope', 'collection');

            $route('/{controller}/{id}', $ctrl, ['GET', 'PUT', 'DELETE', 'OPTIONS'])
                ->where('id', '[a-z0-9]+')
                ->implicit('actionScope', 'instance');

        }, '/api')
            ->where('controller', 'test')
            ->mixin('opis-colibri/json-api', ['resolver' => APIResolver::class, 'middleware' => AuthMiddleware::class]);
    }

    /**
     * @param SchemaResolverCollector $schemas
     */
    public function jsonSchemaResolvers(SchemaResolverCollector $schemas)
    {
        $schemas->register('test', SchemaResolver::class);
    }
}