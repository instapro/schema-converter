<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Introspection;

use Instapro\SchemaConverter\Converter;
use Instapro\SchemaConverter\Schema;
use ReflectionParameter;
use ReflectionType;

interface SchemaBuilder
{
    public function build(Converter $converter, ReflectionParameter $parameter, ?ReflectionType $type): Schema;
}
