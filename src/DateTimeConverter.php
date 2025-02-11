<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter;

use DateMalformedStringException;
use DateTimeInterface;
use Instapro\SchemaConverter\Schemas\SimpleSchema;
use ReflectionClass;

final class DateTimeConverter implements Converter
{
    public function toSchema(string $type): Schema
    {
        if (!is_subclass_of($type, DateTimeInterface::class)) {
            throw ConverterException::toSchema($type);
        }

        return new SimpleSchema('datetime');
    }

    public function castValue(string $type, mixed $value): mixed
    {
        if (!is_subclass_of($type, DateTimeInterface::class)) {
            throw ConverterException::castValue($type, $value);
        }

        $reflection = new ReflectionClass($type);
        $instance = $reflection->newInstanceWithoutConstructor();

        $constructor = $reflection->getConstructor();
        if ($constructor === null) {
            throw ConverterException::castValue($type, $value);
        }

        try {
            $constructor->invoke($instance, $value);
        } catch (DateMalformedStringException) {
            throw ConverterException::castValue($type, $value);
        }

        return $instance;
    }
}
