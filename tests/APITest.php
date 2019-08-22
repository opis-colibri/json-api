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

namespace Opis\Colibri\Modules\JsonAPI\Test;

use Opis\Http\Request;
use Opis\Stream\Stream;

class APITest extends BaseApp
{
    public function testCollection()
    {
        $response = $this->execGET('/api/test');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['a', 'b', 'c'], json_decode($response->getBody()));
    }

    public function testInstance()
    {
        $response = $this->execGET('/api/test/a');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['id' => 'a', 'name' => 'Item a', 'value' => 1], json_decode($response->getBody(), true));

        $response = $this->execGET('/api/test/b');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['id' => 'b', 'name' => 'Item b', 'value' => 2], json_decode($response->getBody(), true));

        $response = $this->execGET('/api/test/c');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['id' => 'c', 'name' => 'Item c', 'value' => 3], json_decode($response->getBody(), true));

        $response = $this->execGET('/api/test/d');
        $this->assertEquals(404, $response->getStatusCode());

        $response = $this->execGET('/api/test/aa');
        $this->assertEquals(404, $response->getStatusCode());

        // Route id is [a-z0-9]

        $response = $this->execGET('/api/test/okTest');
        $this->assertEquals(404, $response->getStatusCode());

        $response = $this->execGET('/api/test/not-ok');
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testCreate()
    {
        $response = $this->execRequest($this->createPost([
            'name' => 'test',
            'value' => 18,
        ]));

        $this->assertEquals(201, $response->getStatusCode());

        $id = json_decode($response->getBody())->id ?? null;

        $response = $this->execGET('/api/test');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['a', 'b', 'c', $id], json_decode($response->getBody()));

        $response = $this->execRequest($this->createPost([
            'name' => 'test',
            'value' => 18,
            'tag' => 'tag1'
        ]));

        $this->assertEquals(201, $response->getStatusCode());

        $id2 = json_decode($response->getBody())->id ?? null;

        $response = $this->execGET('/api/test');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['a', 'b', 'c', $id, $id2], json_decode($response->getBody()));
    }

    public function testUpdate()
    {
        $v = rand(100, 300);
        $response = $this->execRequest($this->createPut([
            'value' => $v
        ], '/api/test/c'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($v, json_decode($response->getBody())->value ?? null);

        $response = $this->execGET('/api/test/c');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($v, json_decode($response->getBody())->value ?? null);
    }

    public function testDelete()
    {
        $response = $this->execRequest(new Request('DELETE', '/api/test/a'));
        $this->assertEquals(204, $response->getStatusCode());

        $response = $this->execGET('/api/test/a');
        $this->assertEquals(404, $response->getStatusCode());

        $response = $this->execRequest(new Request('DELETE', '/api/test/b'));
        $this->assertEquals(204, $response->getStatusCode());

        $response = $this->execGET('/api/test/b');
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testCreateInvalid()
    {
        $response = $this->execRequest($this->createPost([
            'name' => 'test',
            'value' => 18.5,
        ]));

        $this->assertEquals(422, $response->getStatusCode());

        $response = $this->execRequest($this->createPost([
            'name' => 'test',
            'value' => 18,
            'other' => 123
        ]));

        $this->assertEquals(422, $response->getStatusCode());

        $response = $this->execRequest($this->createPost([
            'name' => 'test',
            'value' => 18,
            'tag' => 'tag4'
        ]));

        $this->assertEquals(422, $response->getStatusCode());
    }

    public function testNotAuth()
    {
        $request = new Request('GET', '/api/test/c');
        $response = $this->app()->run($request);
        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * @param $json
     * @param string $url
     * @return Request
     */
    protected function createPost($json, string $url = '/api/test'): Request
    {
        $json = 'data://application/json;base64,' . base64_encode(json_encode($json));
        return new Request('POST', $url, 'HTTP/1.1', false, [], [], new Stream($json));
    }

    /**
     * @param $json
     * @param string $url
     * @return Request
     */
    protected function createPut($json, string $url): Request
    {
        $json = 'data://application/json;base64,' . base64_encode(json_encode($json));
        return new Request('PUT', $url, 'HTTP/1.1', false, [], [], new Stream($json));
    }
}