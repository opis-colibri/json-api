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

use stdClass;
use Opis\Colibri\Modules\JsonAPI\Traits\ResponseTrait;
use Opis\Colibri\Modules\JsonAPI\Traits\ValidationTrait;
use Opis\Http\Response;

class TestController
{
    use ResponseTrait;
    use ValidationTrait;

    static $data = [
        'a' => [
            'id' => 'a',
            'name' => 'Item a',
            'value' => 1
        ],
        'b' => [
            'id' => 'b',
            'name' => 'Item b',
            'value' => 2
        ],
        'c' => [
            'id' => 'c',
            'name' => 'Item c',
            'value' => 3
        ]
    ];

    protected $items;

    public function __construct()
    {
        $this->items = &self::$data;
    }

    /**
     * @inheritDoc
     */
    public function actionGetCollection(): Response
    {
        return $this->http200(array_keys($this->items));
    }

    /**
     * @inheritDoc
     */
    public function actionCreateInstance(stdClass $data): Response
    {
        $result = $this->validate($data, 'json-schema://test/create.json#');
        if ($result->hasErrors()) {
            return $this->http422($this->formatErrors($result));
        }
        unset($result);

        $id = uniqid();
        $this->items[$id] = [
            'id' => $id,
            'name' => $data->name,
            'value' => $data->value,
        ];

        if (property_exists($data, 'tag')) {
            $this->items[$id]['tag'] = $data->tag;
        }

        return $this->http201($id);
    }

    /**
     * @inheritDoc
     */
    public function actionGetInstance(string $id): Response
    {
        if (!isset($this->items[$id])) {
            return $this->http404();
        }

        return $this->http200($this->items[$id]);
    }

    /**
     * @inheritDoc
     */
    public function actionUpdateInstance(string $id, stdClass $data): Response
    {
        if (!isset($this->items[$id])) {
            return $this->http404();
        }

        $result = $this->validate($data, 'json-schema://test/update.json#');
        if ($result->hasErrors()) {
            return $this->http422($this->formatErrors($result));
        }
        unset($result);

        if (isset($data->value)) {
            $this->items[$id]['value'] = $data->value;
        }

        if (isset($data->tag)) {
            $this->items[$id]['tag'] = $data->tag;
        }

        return $this->http200($this->items[$id]);
    }

    /**
     * @inheritDoc
     */
    public function actionDeleteInstance(string $id): Response
    {
        if (!isset($this->items[$id])) {
            return $this->http404();
        }

        unset($this->items[$id]);

        return $this->http204();
    }
}