<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Introspection\ValueCaster;

use Instapro\SchemaConverter\Converter;
use Instapro\SchemaConverter\Introspection\ValueCaster;
use ReflectionIntersectionType;
use ReflectionParameter;
use ReflectionType;

final readonly class IntersectionTypeValueCaster implements ValueCaster
{
    public function __construct(
        private ValueCaster $next,
    ) {
    }

    /** @return array<int, mixed> */
    public function cast(Converter $converter, mixed $value, ReflectionParameter $parameter, ?ReflectionType $type): array
    {
        if (!$type instanceof ReflectionIntersectionType) {
            return $this->next->cast($converter, $value, $parameter, $type);
        }

        $arguments = [$value];
        foreach ($type->getTypes() as $subType) {
            $arguments = $this->next->cast($converter, $value, $parameter, $subType);
        }

        return $arguments;
    }
}
