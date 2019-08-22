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

use Opis\JsonSchema\{
    IFilter,
    FilterContainer as BaseContainer
};
use Opis\Colibri\Serializable\AdvancedClassList;
use function Opis\Colibri\Functions\collect;

class FilterContainer extends BaseContainer
{
    /** @var bool */
    protected $resolved = false;

    /**
     * @inheritDoc
     */
    public function get(string $type, string $name)
    {
        $this->resolve();
        return parent::get($type, $name);
    }

    /**
     * @inheritDoc
     */
    public function hasType(string $type): bool
    {
        $this->resolve();
        return parent::hasType($type);
    }

    /**
     * Resolves filters
     */
    protected function resolve()
    {
        if ($this->resolved) {
            return;
        }
        $this->resolved = true;

        /** @var AdvancedClassList $list */
        $list = collect(Collectors\SchemaFilterCollector::NAME);

        foreach ($list->getNames() as $vendor) {

            /** @var ISchemaFilters $filter */
            $container = $list->get($vendor);

            foreach ($container->filters() as $type => $filters) {
                foreach ($filters as $name => $filter) {
                    if (!($filter instanceof IFilter)) {
                        if (!is_callable($filter)) {
                            continue;
                        }
                        $filter = new FilterProxy($filter);
                    }
                    $this->add($type, $vendor . '::' . $name, $filter);
                }
            }
        }
    }
}