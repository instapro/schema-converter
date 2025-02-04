<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Test\Unit;

use Instapro\SchemaConverter\ConverterException;
use Instapro\SchemaConverter\PrimitiveConverter;
use Instapro\SchemaConverter\Schemas\SimpleSchema;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @small
 */
final class PrimitiveConverterTest extends TestCase
{
    #[Test]
    #[DataProvider('primitiveProvider')]
    public function it_should_convert_to_schema(string $type): void
    {
        $expected = new SimpleSchema($type);

        $converter = new PrimitiveConverter();
        $actual = $converter->toSchema($type);

        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function it_should_throw_an_exception_when_cannot_convert_to_schema(): void
    {
        $this->expectExceptionObject(ConverterException::toSchema('foo'));

        $converter = new PrimitiveConverter();
        $converter->toSchema('foo');
    }

    /** @return array<string, array{string}> */
    public static function primitiveProvider(): array
    {
        return [
            'string' => ['string'],
            'int' => ['int'],
            'float' => ['float'],
            'bool' => ['bool'],
            'mixed' => ['mixed'],
            'null' => ['null'],
        ];
    }

    #[Test]
    #[DataProvider('valuesProvider')]
    public function it_should_cast_value(string $type, mixed $value, mixed $expected): void
    {
        $converter = new PrimitiveConverter();
        $actual = $converter->castValue($type, $value);

        self::assertSame($expected, $actual);
    }

    #[Test]
    public function it_should_throw_an_exception_when_cannot_cast_value(): void
    {
        $type = 'foo';
        $value = 'some value';

        $this->expectExceptionObject(ConverterException::castValue($type, $value));

        $converter = new PrimitiveConverter();
        $converter->castValue($type, $value);
    }

    /** @return array<string, array{string}> */
    public static function valuesProvider(): array
    {
        return [
            'string/integer' => ['string', 123, '123'],
            'string/float' => ['string', 123, '123'],
            'int/string' => ['int', '543', 543],
            'int/float' => ['int', 1123.5, 1123],
            'float/int' => ['float', 123, 123.0],
            'float/string' => ['float', '3.14', 3.14],
            'bool/true' => ['bool', 1, true],
            'bool/false' => ['bool', 0, false],
            'bool/empty string' => ['bool', '', false],
        ];
    }
}
