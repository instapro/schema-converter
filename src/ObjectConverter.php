<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter;

use Instapro\SchemaConverter\Introspection\SchemaBuilder;
use Instapro\SchemaConverter\Introspection\ValueCaster;
use Instapro\SchemaConverter\Schemas\ObjectParameter;
use Instapro\SchemaConverter\Schemas\ObjectSchema;
use InvalidArgumentException;
use LogicException;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use ReflectionType;
use Throwable;
use function is_array;
use function sprintf;

final class ObjectConverter implements Converter, CompositeBondConverter
{
    private Converter $converter;

    public function __construct(
        private readonly ValueCaster $valueCaster = new ValueCaster\OptionalValueCaster(
            new ValueCaster\StandardValueCaster(),
        ),
        private readonly SchemaBuilder $schemaBuilder = new SchemaBuilder\StandardSchemaBuilder(),
    ) {
    }

    public function bindConverter(Converter $converter): void
    {
        $this->converter = $converter;
    }

    public function toSchema(string $type): Schema
    {
        try {
            /** @var class-string $type */
            $reflection = new ReflectionClass($type);
        } catch (ReflectionException) {
            throw ConverterException::toSchema($type);
        }

        return new ObjectSchema(...array_map(
            fn (ReflectionParameter $parameter) => $this->createParameter($parameter, $parameter->getType()),
            $reflection->getConstructor()?->getParameters() ?? [],
        ));
    }

    public function castValue(string $type, mixed $value): mixed
    {
        try {
            /** @var class-string $type */
            $reflection = new ReflectionClass($type);
        } catch (ReflectionException) {
            throw ConverterException::castValue($type, $value);
        }

        if (!is_array($value)) {
            throw new InvalidArgumentException(sprintf('Conversion to "%s" requires %%s to be an array', $type));
        }

        $constructor = $reflection->getConstructor();
        if ($constructor === null) {
            return $reflection->newInstance();
        }

        $arguments = [];
        foreach ($constructor->getParameters() as $parameter) {
            try {
                $arguments = [
                    ...$arguments,
                    ...$this->valueCaster->cast($this->converter, $value[$parameter->getName()] ?? null, $parameter, $parameter->getType()),
                ];
            } catch (Throwable $throwable) {
                throw new LogicException(sprintf('Failed to process parameter "%s"', $parameter->getName()), 0, $throwable);
            }
        }

        // Creates object without calling its constructor to avoid calling a non-public constructor.
        $instance = $reflection->newInstanceWithoutConstructor();
        $constructor->invokeArgs($instance, $arguments);

        return $instance;
    }

    private function createParameter(ReflectionParameter $parameter, ?ReflectionType $type): ObjectParameter
    {
        $schema = $this->schemaBuilder->build($this->converter, $parameter, $type);

        return new ObjectParameter(
            $parameter->getName(),
            $schema,
            $schema->getType() === 'mixed' || !($parameter->isOptional() || $parameter->allowsNull()),
        );
    }
}
