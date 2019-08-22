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

namespace Opis\Colibri\Modules\JsonAPI\Traits;

use stdClass;
use Opis\JsonSchema\{
    IValidator, ValidationError, ValidationResult, Validator,
    Exception\AbstractSchemaException
};
use function Opis\Colibri\Functions\make;

trait ValidationTrait
{
    /**
     * @param $data
     * @param stdClass|boolean|string $schema
     * @param array $global_data
     * @param boolean $safe
     * @return ValidationResult
     */
    protected function validate($data, $schema, array $global_data = [], bool $safe = true): ValidationResult
    {
        /** @var IValidator|Validator $validator */
        $validator = make(IValidator::class);

        $global = $validator->getGlobalVars();
        $validator->setGlobalVars($global_data);
        unset($global_data);

        if ($safe) {
            try {
                if (is_string($schema)) {
                    $result = $validator->uriValidation($data, $schema);
                } else {
                    $result = $validator->dataValidation($data, $schema);
                }
            } catch (AbstractSchemaException $e) {
                $result = new ValidationResult(1);
                $result->addError($this->getErrorForException($e));
            }
        } else {
            if (is_string($schema)) {
                $result = $validator->uriValidation($data, $schema);
            } else {
                $result = $validator->dataValidation($data, $schema);
            }
        }

        // Restore globals
        $validator->setGlobalVars($global);

        return $result;
    }

    /**
     * @param AbstractSchemaException $e
     * @return ValidationError
     */
    protected function getErrorForException(AbstractSchemaException $e): ValidationError
    {
        // TODO:
        return new ValidationError(null, [], [], false, 'exception', [
            'message' => $e->getMessage(),
        ]);
    }

    /**
     * @param ValidationError $error
     * @return array
     */
    protected function formatError(ValidationError $error)
    {
        // TODO:
        return [
            'pointer' => $error->dataPointer(),
            'message' => 'Error ' . $error->keyword(),
            "args" => $error->keywordArgs(),
            'schema' => $error->schema(),
            'sub' => array_map([$this, 'formatError'], $error->subErrors())
        ];
    }

    /**
     * @param ValidationResult $result
     * @return array
     */
    protected function formatErrors(ValidationResult $result): array
    {
        return array_map([$this, 'formatError'], $result->getErrors());
    }
}