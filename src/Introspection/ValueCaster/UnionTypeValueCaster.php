<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Introspection\ValueCaster;

use Instapro\SchemaConverter\Converter;
use Instapro\SchemaConverter\Introspection\ValueCaster;
use InvalidArgumentException;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;
use Throwable;
use function sprintf;

final readonly class UnionTypeValueCaster implements ValueCaster
{
    public function __construct(
        private ValueCaster $next,
    ) {
    }

    /** @return array<int, mixed> */
    public function cast(Converter $converter, mixed $value, ReflectionParameter $parameter, ?ReflectionType $type): array
    {
        if (!$type instanceof ReflectionUnionType) {
            return $this->next->cast($converter, $value, $parameter, $type);
        }

        foreach ($type->getTypes() as $subType) {
            try {
                return $this->next->cast($converter, $value, $parameter, $subType);
            } catch (Throwable) {
                continue;
            }
        }

        throw new InvalidArgumentException(
            sprintf('Parameter "%s" does not match any of the expected types', $parameter->getName()),
        );
    }
}
