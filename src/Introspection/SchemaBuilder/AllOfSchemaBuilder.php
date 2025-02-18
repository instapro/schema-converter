<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Introspection\SchemaBuilder;

use Instapro\SchemaConverter\Converter;
use Instapro\SchemaConverter\Introspection\SchemaBuilder;
use Instapro\SchemaConverter\Schema;
use Instapro\SchemaConverter\Schemas\AllOfSchema;
use ReflectionIntersectionType;
use ReflectionParameter;
use ReflectionType;

final readonly class AllOfSchemaBuilder implements SchemaBuilder
{
    public function __construct(
        private SchemaBuilder $next,
    ) {
    }

    public function build(Converter $converter, ReflectionParameter $parameter, ?ReflectionType $type): Schema
    {
        if (!$type instanceof ReflectionIntersectionType) {
            return $this->next->build($converter, $parameter, $type);
        }

        return new AllOfSchema(...array_map(
            fn (ReflectionType $subType) => $this->next->build($converter, $parameter, $subType),
            $type->getTypes(),
        ));
    }
}
