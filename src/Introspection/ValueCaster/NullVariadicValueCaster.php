<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Introspection\ValueCaster;

use Instapro\SchemaConverter\Converter;
use Instapro\SchemaConverter\Introspection\ValueCaster;
use ReflectionParameter;
use ReflectionType;

final readonly class NullVariadicValueCaster implements ValueCaster
{
    public function __construct(
        private ValueCaster $next,
    ) {
    }

    /** @return array<int, mixed> */
    public function cast(Converter $converter, mixed $value, ReflectionParameter $parameter, ?ReflectionType $type): array
    {
        if ($value === null && $parameter->isVariadic()) {
            return [];
        }

        return $this->next->cast($converter, $value, $parameter, $type);
    }
}
