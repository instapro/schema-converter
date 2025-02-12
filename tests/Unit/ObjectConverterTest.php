<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Test\Unit;

use ArrayIterator;
use Countable;
use Instapro\SchemaConverter\ConverterException;
use Instapro\SchemaConverter\ObjectConverter;
use Instapro\SchemaConverter\Schemas\AllOfSchema;
use Instapro\SchemaConverter\Schemas\ListSchema;
use Instapro\SchemaConverter\Schemas\ObjectParameter;
use Instapro\SchemaConverter\Schemas\ObjectSchema;
use Instapro\SchemaConverter\Schemas\OneOfSchema;
use Instapro\SchemaConverter\Schemas\SimpleSchema;
use Instapro\SchemaConverter\Test\Fixtures\Objects\WithIntersectionType;
use Instapro\SchemaConverter\Test\Fixtures\Objects\WithNamedType;
use Instapro\SchemaConverter\Test\Fixtures\Objects\WithNullableParameter;
use Instapro\SchemaConverter\Test\Fixtures\Objects\WithOptionalParameter;
use Instapro\SchemaConverter\Test\Fixtures\Objects\WithoutConstructor;
use Instapro\SchemaConverter\Test\Fixtures\Objects\WithoutType;
use Instapro\SchemaConverter\Test\Fixtures\Objects\WithSeveralTypes;
use Instapro\SchemaConverter\Test\Fixtures\Objects\WithUnionType;
use Instapro\SchemaConverter\Test\Fixtures\Objects\WithVariadicParameter;
use Instapro\SchemaConverter\Test\TestFramework\DummyConverter;
use Instapro\SchemaConverter\Test\TestFramework\DummySchema;
use Instapro\SchemaConverter\Test\TestFramework\Introspection\DummySchemaBuilder;
use Instapro\SchemaConverter\Test\TestFramework\Introspection\DummyValueCaster;
use Iterator;
use LogicException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use const INF;

/**
 * @internal
 * @small
 */
final class ObjectConverterTest extends TestCase
{
    #[Test]
    #[DataProvider('toSchemaProvider')]
    public function it_should_convert_object_without_constructor_to_schema(string $className, ObjectSchema $expected): void
    {
        $converter = new ObjectConverter();
        $converter->bindConverter(new DummyConverter());

        $actual = $converter->toSchema($className);

        self::assertEquals($expected, $actual);
    }

    /** @return array<string, array{class-string, ObjectSchema}> */
    public static function toSchemaProvider(): array
    {
        return [
            'without constructor' => [
                WithoutConstructor::class,
                new ObjectSchema(),
            ],
            'without type' => [
                WithoutType::class,
                new ObjectSchema(new ObjectParameter('parameter', new DummySchema('mixed'), true)),
            ],
            'with type' => [
                WithNamedType::class,
                new ObjectSchema(new ObjectParameter('parameter', new DummySchema('bool'), true)),
            ],
            'with union type' => [
                WithUnionType::class,
                new ObjectSchema(
                    new ObjectParameter(
                        'parameter1',
                        new OneOfSchema(new DummySchema('string'), new DummySchema('int')),
                        true,
                    ),
                    new ObjectParameter(
                        'parameter2',
                        new OneOfSchema(new DummySchema('float'), new DummySchema('bool')),
                        true,
                    ),
                ),
            ],
            'with intersection type' => [
                WithIntersectionType::class,
                new ObjectSchema(
                    new ObjectParameter(
                        'parameter',
                        new AllOfSchema(
                            new DummySchema(Iterator::class),
                            new DummySchema(Countable::class),
                        ),
                        true,
                    ),
                ),
            ],
            'with optional parameter' => [
                WithOptionalParameter::class,
                new ObjectSchema(
                    new ObjectParameter('parameter', new DummySchema('string'), false),
                ),
            ],
            'with nullable parameter' => [
                WithNullableParameter::class,
                new ObjectSchema(
                    new ObjectParameter(
                        'parameter',
                        new DummySchema('int'),
                        false,
                    ),
                ),
            ],
            'with variadic parameter' => [
                WithVariadicParameter::class,
                new ObjectSchema(
                    new ObjectParameter('parameter', new ListSchema(new DummySchema('string')), false),
                ),
            ],
            'with several types' => [
                WithSeveralTypes::class,
                new ObjectSchema(
                    new ObjectParameter('array', new DummySchema('array'), true),
                    new ObjectParameter('string', new DummySchema('string'), true),
                    new ObjectParameter('int', new DummySchema('int'), true),
                    new ObjectParameter('float', new DummySchema('float'), true),
                    new ObjectParameter('bool', new DummySchema('bool'), true),
                    new ObjectParameter('mixed', new DummySchema('mixed'), true),
                    new ObjectParameter('null', new DummySchema('null'), false),
                ),
            ],
        ];
    }

    #[Test]
    public function it_should_convert_object_parameters_constructor_to_schema_using_a_custom_schema_builder(): void
    {
        $type = WithNamedType::class;
        $schema = new SimpleSchema(uniqid());

        $expected = new ObjectSchema(new ObjectParameter('parameter', $schema, true));

        $schemaBuilder = new DummySchemaBuilder($schema);

        $converter = new ObjectConverter(schemaBuilder: $schemaBuilder);
        $converter->bindConverter(new DummyConverter());

        $actual = $converter->toSchema($type);

        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function it_should_throw_an_exception_when_cannot_convert_to_schema(): void
    {
        $this->expectExceptionObject(ConverterException::toSchema('foo'));

        $converter = new ObjectConverter();
        $converter->toSchema('foo');
    }

    /** @param array<string, mixed> $value */
    #[Test]
    #[DataProvider('toValueProvider')]
    public function it_should_cast_value(string $type, array $value, object $expected): void
    {
        $converter = new ObjectConverter();
        $converter->bindConverter(new DummyConverter());

        self::assertEquals($expected, $converter->castValue($type, $value));
    }

    /** @return array<string, array{class-string, array<string, mixed>, object}> */
    public static function toValueProvider(): array
    {
        return [
            'without constructor' => [
                WithoutConstructor::class,
                [],
                new WithoutConstructor(),
            ],
            'without type/filled' => [
                WithoutType::class,
                ['parameter' => 'value'],
                new WithoutType('value'),
            ],
            'without type/missing' => [
                WithoutType::class,
                [],
                new WithoutType(null),
            ],
            'with parameter with named type' => [
                WithNamedType::class,
                ['parameter' => true],
                new WithNamedType(true),
            ],
            'with parameter with union type/1' => [
                WithUnionType::class,
                ['parameter1' => 'string', 'parameter2' => 1.0],
                new WithUnionType('string', 1.0),
            ],
            'with parameter with union type/2' => [
                WithUnionType::class,
                ['parameter1' => 42, 'parameter2' => false],
                new WithUnionType(42, false),
            ],
            'with parameter with intersection type' => [
                WithIntersectionType::class,
                ['parameter' => new ArrayIterator([])],
                new WithIntersectionType(new ArrayIterator([])),
            ],
            'with optional parameter/missing' => [
                WithOptionalParameter::class,
                [],
                new WithOptionalParameter(),
            ],
            'with optional parameter/filled' => [
                WithOptionalParameter::class,
                ['parameter' => 'value'],
                new WithOptionalParameter('value'),
            ],
            'with nullable parameter/missing' => [
                WithNullableParameter::class,
                [],
                new WithNullableParameter(null),
            ],
            'with nullable parameter/null' => [
                WithNullableParameter::class,
                ['parameter' => null],
                new WithNullableParameter(null),
            ],
            'with nullable parameter/filled' => [
                WithNullableParameter::class,
                ['parameter' => 42],
                new WithNullableParameter(42),
            ],
            'with variadic parameter/missing' => [
                WithVariadicParameter::class,
                [],
                new WithVariadicParameter(),
            ],
            'with variadic parameter/filled' => [
                WithVariadicParameter::class,
                ['parameter' => ['value1', 'value2']],
                new WithVariadicParameter('value1', 'value2'),
            ],
            'with several types' => [
                WithSeveralTypes::class,
                [
                    'array' => [1, 2, 3],
                    'string' => 'string',
                    'int' => 42,
                    'float' => 1.2,
                    'bool' => true,
                    'mixed' => INF,
                    'null' => null,
                ],
                new WithSeveralTypes(
                    array: [1, 2, 3],
                    string: 'string',
                    int: 42,
                    float: 1.2,
                    bool: true,
                    mixed: INF,
                    null: null,
                ),
            ],
        ];
    }

    #[Test]
    public function it_should_cast_parameters_using_a_custom_value_caster(): void
    {
        $type = WithOptionalParameter::class;
        $castedValue = [uniqid()];

        $expected = new WithOptionalParameter(parameter: $castedValue[0]);

        $valueCaster = new DummyValueCaster($castedValue);

        $converter = new ObjectConverter(valueCaster: $valueCaster);
        $converter->bindConverter(new DummyConverter());

        $actual = $converter->castValue($type, ['parameter' => 'value']);

        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function it_should_throw_an_exception_when_cannot_cast_value(): void
    {
        $value = 'some value';

        $this->expectExceptionObject(ConverterException::castValue('foo', $value));

        $converter = new ObjectConverter();
        $converter->castValue('foo', $value);
    }

    #[Test]
    public function it_should_throw_an_exception_when_cannot_cast_value_to_a_property(): void
    {
        $value = [];

        $this->expectExceptionObject(new LogicException('Failed to process parameter "parameter"'));

        $converter = new ObjectConverter();
        $converter->castValue(WithNamedType::class, $value);
    }
}
