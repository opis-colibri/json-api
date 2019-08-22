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

namespace Opis\Colibri\Modules\JsonAPI\Middleware;

use Opis\Colibri\Routing\{
    HttpRoute,
    Middleware
};
use Opis\Http\{
    Request,
    Response
};

class CORSMiddleware extends Middleware
{
    public function __invoke(Request $request, HttpRoute $route)
    {
        $headers = [
            'Access-Control-Allow-Origin' => $request->getHeader('Origin') ?? '*',
            'Access-Control-Allow-Credentials' => 'true',
        ];

        if ($request->getMethod() === 'OPTIONS') {
            $headers['Access-Control-Allow-Methods'] = implode(', ', $route->get('method', ['GET']));
            $headers['Access-Control-Max-Age'] = 24 * 3600;

            if (null !== $allowHeaders = $request->getHeader('Access-Control-Request-Headers')) {
                $headers['Access-Control-Allow-Headers'] = $allowHeaders;
            }

            return new Response(200, $headers);
        }

        return $this->next()->modify(function (Response $response) use ($headers) {
            $response->addHeaders($headers);
        });
    }
}
