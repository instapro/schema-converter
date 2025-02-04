<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Test\TestFramework;

use Instapro\SchemaConverter\Converter;
use Instapro\SchemaConverter\Schema;

final class DummyConverter implements Converter
{
    /**
     * @param array<Schema> $schemas
     * @param array<mixed> $values
     */
    public function __construct(
        private array $schemas = [],
        private array $values = [],
    ) {
    }

    public function toSchema(string $type): Schema
    {
        if ($this->schemas === []) {
            return new DummySchema($type);
        }

        return array_shift($this->schemas);
    }

    public function castValue(string $type, mixed $value): mixed
    {
        if ($this->values === []) {
            return $value;
        }

        return array_shift($this->values);
    }
}
