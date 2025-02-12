<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Test\Unit;

use Instapro\SchemaConverter\ConverterException;
use Instapro\SchemaConverter\JsonArrayConverter;
use Instapro\SchemaConverter\Schemas\SimpleSchema;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @small
 */
final class JsonArrayConverterTest extends TestCase
{
    #[Test]
    public function it_should_convert_to_schema(): void
    {
        $type = 'array';
        $expected = new SimpleSchema('json');

        $converter = new JsonArrayConverter();
        $actual = $converter->toSchema($type);

        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function it_should_throw_an_exception_when_cannot_convert_to_schema(): void
    {
        $type = 'foo';

        $this->expectExceptionObject(ConverterException::toSchema($type));

        $converter = new JsonArrayConverter();
        $converter->toSchema($type);
    }

    /** @param array<mixed> $expected */
    #[Test]
    #[DataProvider('jsonProvider')]
    public function it_should_cast_value(string $value, array $expected): void
    {
        $converter = new JsonArrayConverter();
        $actual = $converter->castValue('array', $value);

        self::assertSame($expected, $actual);
    }

    #[Test]
    public function it_should_throw_an_exception_when_cannot_cast_value_because_json_is_invalid(): void
    {
        $type = 'array';
        $value = '[1, 2, 3, d: ]';

        $this->expectExceptionObject(ConverterException::castValue($type, $value));

        $converter = new JsonArrayConverter();
        $converter->castValue($type, $value);
    }

    /** @return array<string, array{string, array<mixed>}> */
    public static function jsonProvider(): array
    {
        return [
            'empty' => ['[]', []],
            'list' => ['[1,2,3]', [1, 2, 3]],
            'associative' => ['{"foo":1,"bar":true}', ['foo' => 1, 'bar' => true]],
        ];
    }
}
