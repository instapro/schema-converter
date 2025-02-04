<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter;

interface Converter
{
    public function toSchema(string $type): Schema;

    public function castValue(string $type, mixed $value): mixed;
}
