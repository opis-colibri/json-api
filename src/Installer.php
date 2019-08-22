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

use Opis\Colibri\Installer as AbstractInstaller;
use function Opis\Colibri\Functions\app;

class Installer extends AbstractInstaller
{
    public function enable()
    {
        $collector = app()->getCollector();
        $collector->register(Collectors\SchemaFilterCollector::NAME, Collectors\SchemaFilterCollector::class,
            'Collect json schema filters');
        $collector->register(Collectors\SchemaResolverCollector::NAME, Collectors\SchemaResolverCollector::class,
            'Collect json schema resolvers');
    }

    public function disable()
    {
        $collector = app()->getCollector();
        $collector->unregister(Collectors\SchemaFilterCollector::NAME);
        $collector->unregister(Collectors\SchemaResolverCollector::NAME);
    }
}