<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Introspection\SchemaBuilder;

use Instapro\SchemaConverter\Converter;
use Instapro\SchemaConverter\Introspection\SchemaBuilder;
use Instapro\SchemaConverter\Schema;
use Instapro\SchemaConverter\Schemas\ListSchema;
use ReflectionParameter;
use ReflectionType;

final readonly class ListSchemaBuilder implements SchemaBuilder
{
    public function __construct(
        private SchemaBuilder $next,
    ) {
    }

    public function build(Converter $converter, ReflectionParameter $parameter, ?ReflectionType $type): Schema
    {
        if (!$parameter->isVariadic()) {
            return $this->next->build($converter, $parameter, $type);
        }

        return new ListSchema($this->next->build($converter, $parameter, $type));
    }
}
