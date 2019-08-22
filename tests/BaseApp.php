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

use Opis\Http\{Request, Response};
use Opis\Colibri\Testing\{ApplicationTestCase, Builders\ApplicationBuilder};
use function Opis\Colibri\Functions\make;

class BaseApp extends ApplicationTestCase
{
    /**
     * @inheritDoc
     */
    protected static function vendorDir(): string
    {
        return __DIR__ . '/../vendor';
    }

    /**
     * @inheritDoc
     */
    protected static function applicationSetup(ApplicationBuilder $builder)
    {
        $builder->addUninstalledModuleFromPath(__DIR__ . '/../');

        $ns = '\\Opis\\Colibri\\Modules\\JsonAPI\\Test\\Module\\';

        $builder->createUninstalledTestModule('test/module', $ns, __DIR__ . '/Module', [
            'collector' => $ns . 'Collector',
        ], ['opis-colibri/json-api']);

        $builder->addDependencies('test/module');
    }

    /**
     * @inheritDoc
     */
    protected function execRequest(Request $request, bool $clearCache = true): Response
    {
        print_r($this->app()->getSession());
        $this->app()->getSession()->set('is_logged_in', 'yes');

        $response = parent::execRequest($request, $clearCache);

        $this->app()->clearCachedObjects();

        return $response;
    }
}