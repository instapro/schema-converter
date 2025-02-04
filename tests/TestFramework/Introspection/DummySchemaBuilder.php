<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Test\TestFramework\Introspection;

use Instapro\SchemaConverter\Converter;
use Instapro\SchemaConverter\Introspection\SchemaBuilder;
use Instapro\SchemaConverter\Schema;
use Instapro\SchemaConverter\Schemas\SimpleSchema;
use ReflectionParameter;
use ReflectionType;

final class DummySchemaBuilder implements SchemaBuilder
{
    /** @var array<Schema> */
    private array $schemas;

    public function __construct(Schema ...$schemas)
    {
        $this->schemas = $schemas;
    }

    public function build(Converter $converter, ReflectionParameter $parameter, ?ReflectionType $type): Schema
    {
        if ($this->schemas === []) {
            return new SimpleSchema('dummy');
        }

        return array_shift($this->schemas);
    }
}
