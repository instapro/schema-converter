<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter;

use Instapro\SchemaConverter\Schemas\EnumSchema;
use ReflectionClassConstant;
use ReflectionEnum;
use UnitEnum;
use function is_string;

final class EnumConverter implements Converter
{
    public function toSchema(string $type): Schema
    {
        if (!is_subclass_of($type, UnitEnum::class)) {
            throw ConverterException::toSchema($type);
        }

        return new EnumSchema(...array_map(
            static fn (ReflectionClassConstant $case) => $case->name,
            (new ReflectionEnum($type))->getCases(),
        ));
    }

    public function castValue(string $type, mixed $value): mixed
    {
        if (!is_subclass_of($type, UnitEnum::class)) {
            throw ConverterException::castValue($type, $value);
        }

        if (!is_string($value)) {
            throw ConverterException::castValue($type, $value);
        }

        return (new ReflectionEnum($type))->getCase($value)->getValue();
    }
}
