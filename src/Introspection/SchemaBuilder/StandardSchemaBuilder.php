<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Introspection\SchemaBuilder;

use Instapro\SchemaConverter\Converter;
use Instapro\SchemaConverter\Introspection\SchemaBuilder;
use Instapro\SchemaConverter\Schema;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;

final readonly class StandardSchemaBuilder implements SchemaBuilder
{
    public function build(Converter $converter, ReflectionParameter $parameter, ?ReflectionType $type): Schema
    {
        if (!$type instanceof ReflectionNamedType) {
            return $converter->toSchema('mixed');
        }

        return $converter->toSchema($type->getName());
    }
}
