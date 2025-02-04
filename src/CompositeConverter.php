<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter;

final readonly class CompositeConverter implements Converter
{
    /** @var array<Converter> */
    private array $converters;

    public function __construct(
        Converter $converter,
        Converter ...$converters,
    ) {
        $this->converters = [$converter, ...$converters];
        foreach ($this->converters as $converter) {
            if (!$converter instanceof CompositeBondConverter) {
                continue;
            }
            $converter->bindConverter($this);
        }
    }

    public function toSchema(string $type): Schema
    {
        foreach ($this->converters as $converter) {
            try {
                return $converter->toSchema($type);
            } catch (ConverterException) {
                continue;
            }
        }

        throw ConverterException::toSchema($type);
    }

    public function castValue(string $type, mixed $value): mixed
    {
        foreach ($this->converters as $converter) {
            try {
                return $converter->castValue($type, $value);
            } catch (ConverterException) {
                continue;
            }
        }

        return null;
    }
}
