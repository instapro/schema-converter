<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Introspection\ValueCaster;

use Instapro\SchemaConverter\Converter;
use Instapro\SchemaConverter\Introspection\ValueCaster;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;

final readonly class StandardValueCaster implements ValueCaster
{
    /** @return array<int, mixed> */
    public function cast(Converter $converter, mixed $value, ReflectionParameter $parameter, ?ReflectionType $type): array
    {
        if (!$type instanceof ReflectionNamedType) {
            return [$value];
        }

        return [$converter->castValue($type->getName(), $value)];
    }
}
