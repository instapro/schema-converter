<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Test\TestFramework\Introspection;

use Instapro\SchemaConverter\Converter;
use Instapro\SchemaConverter\Introspection\ValueCaster;
use ReflectionParameter;
use ReflectionType;

final class DummyValueCaster implements ValueCaster
{
    /** @var array<array<mixed>> */
    private array $values = [];

    /** @param array<mixed> ...$value */
    public function __construct(array ...$value)
    {
        $this->values = $value;
    }

    /** @return array<int, mixed> */
    public function cast(Converter $converter, mixed $value, ReflectionParameter $parameter, ?ReflectionType $type): array
    {
        if ($this->values === []) {
            return [$value];
        }

        /** @var array<int, mixed> $result */
        $result = array_shift($this->values);

        return $result;
    }
}
