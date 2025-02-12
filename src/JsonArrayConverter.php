<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter;

use Instapro\SchemaConverter\Schemas\SimpleSchema;
use function is_string;

final class JsonArrayConverter implements Converter
{
    public function toSchema(string $type): Schema
    {
        if ($type !== 'array') {
            throw ConverterException::toSchema($type);
        }

        return new SimpleSchema('json');
    }

    public function castValue(string $type, mixed $value): mixed
    {
        if ($type !== 'array') {
            throw ConverterException::castValue($type, $value);
        }

        if (!is_string($value) || !json_validate($value)) {
            throw ConverterException::castValue($type, $value);
        }

        return json_decode($value, true);
    }
}
