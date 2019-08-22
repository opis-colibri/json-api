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

abstract class AbstractFileSchemaResolver implements ISchemaResolver
{
    const DYNAMIC_SYMBOL = '$';
    const DYNAMIC_SEPARATOR = '/';

    /**
     * @inheritDoc
     */
    public function resolve(string $path)
    {
        if (isset($path[0]) && $path[0] === self::DYNAMIC_SYMBOL) {
            $schema_path = substr($path, strlen(self::DYNAMIC_SYMBOL));
            if (strpos($schema_path, self::DYNAMIC_SEPARATOR) === false) {
                $type = $schema_path;
                $name = '';
            } else {
                list($type, $name) = explode(self::DYNAMIC_SEPARATOR, $schema_path, 2);
            }
            unset($schema_path);
            $schema = $this->resolveDynamic($type, $name);
            if ($schema !== null) {
                return $schema;
            }
            unset($type, $name, $schema);
        }
        $path = $this->fullPath($path);
        if ($path === null || !file_exists($path)) {
            return null;
        }
        return json_decode(file_get_contents($path), false);
    }

    /**
     * @param string $type
     * @param string $name
     * @return \stdClass|boolean|null
     */
    protected function resolveDynamic(
        /** @noinspection PhpUnusedParameterInspection */
        string $type,
        string $name
    ) {
        return null;
    }

    /**
     * @param string $path
     * @return string|null
     */
    abstract protected function fullPath(string $path);
}