<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Introspection\ValueCaster;

use Instapro\SchemaConverter\Converter;
use Instapro\SchemaConverter\Introspection\ValueCaster;
use ReflectionParameter;
use ReflectionType;
use function is_array;

final readonly class VariadicValueCaster implements ValueCaster
{
    public function __construct(
        private ValueCaster $next,
    ) {
    }

    /** @return array<int, mixed> */
    public function cast(Converter $converter, mixed $value, ReflectionParameter $parameter, ?ReflectionType $type): array
    {
        if (!$parameter->isVariadic()) {
            return $this->next->cast($converter, $value, $parameter, $type);
        }

        if (!is_array($value)) {
            return [$value];
        }

        /** @var array<int, mixed> $result */
        $result = array_reduce(
            $value,
            fn (array $carry, mixed $item) => [...$carry, ...$this->next->cast($converter, $item, $parameter, $type)],
            [],
        );

        return $result;
    }
}
