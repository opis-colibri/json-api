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

use Opis\Colibri\Modules\JsonAPI\Formats\{
    DateTime, Time
};
use Opis\JsonSchema\Validator as BaseValidator;

class Validator extends BaseValidator
{
    /**
     * Validator constructor.
     * @param SchemaLoader|null $loader
     */
    public function __construct(?SchemaLoader $loader = null)
    {
        parent::__construct(null, $loader ?? new SchemaLoader(), null, new FilterContainer(), null);
        $formats = $this->getFormats();
        $formats->add('string', 'time', new Time());
        $formats->add('string', 'date-time', new DateTime());
    }
}