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

use Opis\Colibri\Collector as AbstractCollector;
use Opis\Colibri\ItemCollectors\{ContractCollector, RouterGlobalsCollector};
use Opis\JsonSchema\IValidator;

class Collector extends AbstractCollector
{
    /**
     * @return array
     */
    public function __invoke(): array
    {
        return [
            'routerGlobals' => 100,
        ];
    }

    /**
     * @param ContractCollector $contracts
     */
    public function contracts(ContractCollector $contracts)
    {
        $contracts->singleton(IValidator::class, Validator::class);
    }

    /**
     * @param RouterGlobalsCollector $global
     */
    public function routerGlobals(RouterGlobalsCollector $global)
    {
        $global->mixin('opis-colibri/json-api', Mixins::class . '::main');
    }
}