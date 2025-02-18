<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Introspection\SchemaBuilder;

use Instapro\SchemaConverter\Converter;
use Instapro\SchemaConverter\Introspection\SchemaBuilder;
use Instapro\SchemaConverter\Schema;
use Instapro\SchemaConverter\Schemas\OneOfSchema;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;

final readonly class OneOfSchemaBuilder implements SchemaBuilder
{
    public function __construct(
        private SchemaBuilder $next,
    ) {
    }

    public function build(Converter $converter, ReflectionParameter $parameter, ?ReflectionType $type): Schema
    {
        if (!$type instanceof ReflectionUnionType) {
            return $this->next->build($converter, $parameter, $type);
        }

        return new OneOfSchema(...array_map(
            fn (ReflectionType $subType) => $this->next->build($converter, $parameter, $subType),
            $type->getTypes(),
        ));
    }
}
