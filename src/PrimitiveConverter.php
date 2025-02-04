<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter;

use Instapro\SchemaConverter\Schemas\SimpleSchema;
use function in_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_string;

final class PrimitiveConverter implements Converter
{
    public function toSchema(string $type): Schema
    {
        if (!in_array($type, ['string', 'int', 'float', 'bool', 'mixed', 'null'])) {
            throw ConverterException::toSchema($type);
        }

        return new SimpleSchema($type);
    }

    public function castValue(string $type, mixed $value): mixed
    {
        return match ($type) {
            'string' => is_string($value) ? $value : (string) $value, // @phpstan-ignore cast.string
            'int', 'integer' => is_int($value) ? $value : (int) $value, // @phpstan-ignore cast.int
            'float', 'double' => is_float($value) ? $value : (float) $value, // @phpstan-ignore cast.double
            'bool', 'boolean' => is_bool($value) ? $value : (bool) ((int) $value), // @phpstan-ignore cast.int
            'mixed' => $value,
            'null' => null,
            default => throw ConverterException::castValue($type, $value)
        };
    }
}
