<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Test\Functional;

use DateTime;
use Instapro\SchemaConverter\CompositeConverter;
use Instapro\SchemaConverter\DateTimeConverter;
use Instapro\SchemaConverter\EntityConverter;
use Instapro\SchemaConverter\ObjectConverter;
use Instapro\SchemaConverter\PrimitiveConverter;
use Instapro\SchemaConverter\Test\Fixtures\Entities\RealEntity;
use Instapro\SchemaConverter\Test\Fixtures\Objects\Level1;
use Instapro\SchemaConverter\Test\Fixtures\Objects\Level2;
use Instapro\SchemaConverter\Test\Fixtures\Objects\WithIntersectionType;
use Instapro\SchemaConverter\Test\Fixtures\Objects\WithNamedType;
use Instapro\SchemaConverter\Test\Fixtures\Objects\WithNestedParameters;
use Instapro\SchemaConverter\Test\Fixtures\Objects\WithNullableParameter;
use Instapro\SchemaConverter\Test\Fixtures\Objects\WithOptionalParameter;
use Instapro\SchemaConverter\Test\Fixtures\Objects\WithoutConstructor;
use Instapro\SchemaConverter\Test\Fixtures\Objects\WithoutType;
use Instapro\SchemaConverter\Test\Fixtures\Objects\WithSeveralTypes;
use Instapro\SchemaConverter\Test\Fixtures\Objects\WithUnionType;
use Instapro\SchemaConverter\Test\Fixtures\Objects\WithVariadicParameter;
use Instapro\SchemaConverter\Test\TestFramework\EntityManagerFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @small
 */
final class AllConvertersTest extends TestCase
{
    /** @param array<string, mixed> $expected */
    #[Test]
    #[DataProvider('schemasProvider')]
    public function it_should_convert_to_schema(string $type, array $expected): void
    {
        $converter = $this->converter();

        $actual = $converter->toSchema($type)->toArray();

        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function it_should_cast_nested_object(): void
    {
        $value = [
            'parameter' => [
                'parameter1' => [
                    'parameter1' => ['parameter' => 'value'],
                    'parameter2' => ['parameter' => true],
                ],
                'parameter2' => ['parameter' => 42],
            ],
        ];

        $expected = new WithNestedParameters(
            new Level1(
                new Level2(
                    new WithOptionalParameter($value['parameter']['parameter1']['parameter1']['parameter']),
                    new WithNamedType($value['parameter']['parameter1']['parameter2']['parameter']),
                ),
                new WithNullableParameter($value['parameter']['parameter2']['parameter']),
            ),
        );

        $converter = $this->converter();
        $actual = $converter->castValue($expected::class, $value);

        self::assertEquals($expected, $actual);
    }

    /** @return array<string, array{string, array<string, mixed>}> */
    public static function schemasProvider(): array
    {
        return [
            'string' => ['string', ['type' => 'string']],
            'object int' => ['int', ['type' => 'int']],
            'float' => ['float', ['type' => 'float']],
            'bool' => ['bool', ['type' => 'bool']],
            'entity' => [RealEntity::class, ['type' => 'identifier']],
            'object without constructor' => [WithoutConstructor::class, ['type' => 'object']],
            'object without type' => [
                WithoutType::class,
                [
                    'type' => 'object',
                    'parameters' => [
                        'parameter' => ['type' => 'mixed', 'required' => true],
                    ],
                ],
            ],
            'object with type' => [
                WithNamedType::class,
                [
                    'type' => 'object',
                    'parameters' => [
                        'parameter' => ['type' => 'bool', 'required' => true],
                    ],
                ],
            ],
            'object with union type' => [
                WithUnionType::class,
                [
                    'type' => 'object',
                    'parameters' => [
                        'parameter1' => [
                            'type' => 'oneOf',
                            'options' => [
                                ['type' => 'string'],
                                ['type' => 'int'],
                            ],
                            'required' => true,
                        ],
                        'parameter2' => [
                            'type' => 'oneOf',
                            'options' => [
                                ['type' => 'float'],
                                ['type' => 'bool'],
                            ],
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'object with intersection type' => [
                WithIntersectionType::class,
                [
                    'type' => 'object',
                    'parameters' => [
                        'parameter' => [
                            'type' => 'allOf',
                            'options' => [
                                ['type' => 'object'],
                                ['type' => 'object'],
                            ],
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'object with optional parameter' => [
                WithOptionalParameter::class,
                [
                    'type' => 'object',
                    'parameters' => [
                        'parameter' => ['type' => 'string', 'required' => false],
                    ],
                ],
            ],
            'object with nullable parameter' => [
                WithNullableParameter::class,
                [
                    'type' => 'object',
                    'parameters' => [
                        'parameter' => ['type' => 'int', 'required' => false],
                    ],
                ],
            ],
            'object with variadic parameter' => [
                WithVariadicParameter::class,
                [
                    'type' => 'object',
                    'parameters' => [
                        'parameter' => [
                            'type' => 'list',
                            'items' => ['type' => 'string'],
                            'required' => false,
                        ],
                    ],
                ],
            ],
            'object with several types' => [
                WithSeveralTypes::class,
                [
                    'type' => 'object',
                    'parameters' => [
                        'string' => ['type' => 'string', 'required' => true],
                        'int' => ['type' => 'int', 'required' => true],
                        'float' => ['type' => 'float', 'required' => true],
                        'bool' => ['type' => 'bool', 'required' => true],
                        'mixed' => ['type' => 'mixed', 'required' => true],
                        'null' => ['type' => 'null', 'required' => false],
                    ],
                ],
            ],
            'datetime' => [DateTime::class, ['type' => 'datetime']],
        ];
    }

    private function converter(): CompositeConverter
    {
        return new CompositeConverter(
            new PrimitiveConverter(),
            new DateTimeConverter(),
            new EntityConverter(EntityManagerFactory::create()),
            new ObjectConverter(),
        );
    }
}
