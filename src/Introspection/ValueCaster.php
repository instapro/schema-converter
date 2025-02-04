<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Introspection;

use Instapro\SchemaConverter\Converter;
use ReflectionParameter;
use ReflectionType;

interface ValueCaster
{
    /** @return array<int, mixed> */
    public function cast(Converter $converter, mixed $value, ReflectionParameter $parameter, ?ReflectionType $type): array;
}
