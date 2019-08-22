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

namespace Opis\Colibri\Modules\JsonAPI;

use RuntimeException;
use Opis\Colibri\Routing\HttpRoute;
use Opis\Colibri\Modules\JsonAPI\Middleware\CORSMiddleware;

class Mixins
{
    /**
     * @param HttpRoute $route
     * @param array|null $config
     */
    public static function main(HttpRoute $route, array $config = null)
    {
        if (!isset($config['resolver']) || !is_string($config['resolver'])) {
            throw new RuntimeException("Resolver class was not provided");
        }

        $class = $config['resolver'];

        if (!class_exists($class)) {
            throw new RuntimeException("Resolver class {$class} does not exists");
        }

        if (!is_subclass_of($class, ControllerResolver::class)) {
            throw new RuntimeException("Resolver class {$class} must extend " . ControllerResolver::class);
        }

        $middleware = ($config['default-middleware'] ?? true === false) ? [] : [
            CORSMiddleware::class,
        ];

        if (isset($config['middleware'])) {
            if (is_array($config['middleware'])) {
                $middleware = array_merge($middleware, $config['middleware']);
            } else {
                $middleware[] = $config['middleware'];
            }
            $middleware = array_unique($middleware);
        }

        $route
            ->bind('controller', $class . '::bindController')
            ->bind('action', $class . '::bindAction')
            ->bind('data', $class . '::bindData')
            ->middleware(...$middleware);
    }
}