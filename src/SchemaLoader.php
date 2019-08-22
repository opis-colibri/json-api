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

use Opis\Cache\CacheInterface;
use Opis\JsonSchema\Loaders\Memory as BaseLoader;
use function Opis\Colibri\Functions\collect;

class SchemaLoader extends BaseLoader
{
    const NAME = 'json-schema';

    /** @var null|CacheInterface */
    protected $cache = null;

    /** @var int */
    protected $ttl = 0;

    /**
     * SchemaLoader constructor.
     * @param null|CacheInterface $cache
     * @param int $ttl
     */
    public function __construct(?CacheInterface $cache = null, int $ttl = 0)
    {
        $this->cache = $cache;
        $this->ttl = $ttl;
    }

    /**
     * @inheritDoc
     */
    public function loadSchema(string $uri)
    {
        if (array_key_exists($uri, $this->schemas)) {
            return $this->schemas[$uri];
        }
        if (strpos($uri, self::NAME . '://') !== 0) {
            return null;
        }
        $path = substr($uri, strlen(self::NAME) + 3);
        $path = ltrim($path, '/');

        if ($this->cache === null) {
            $schema = $this->resolvePath($path);
        } else {
            $schema = $this->cache->load($path, [$this, 'resolvePath'], $this->ttl);
        }
        unset($path);

        if ($schema === null) {
            $this->schemas[$uri] = null;
            return null;
        }

        $this->add($schema, $uri);

        return $this->schemas[$uri];
    }

    /**
     * @param string $path
     * @return null|bool|\stdClass
     */
    public function resolvePath(string $path)
    {
        if (strpos($path, '/') === false) {
            $name = $path;
            $type = '';
        } else {
            list($name, $type) = explode('/', $path, 2);
        }
        unset($path);

        $resolver = $this->getResolver($name);

        if ($resolver === null) {
            return null;
        }

        $schema = $resolver->resolve($type);

        if (is_string($schema)) {
            if (!is_file($schema)) {
                return null;
            }
            $schema = @json_decode(file_get_contents($schema), false);
            if (!is_object($schema) || !is_bool($schema)) {
                return null;
            }
        }

        return $schema;
    }

    /**
     * @param string $name
     * @return ISchemaResolver|null
     */
    protected function getResolver(string $name): ?ISchemaResolver
    {
        return collect(Collectors\SchemaResolverCollector::NAME)->get($name);
    }
}
