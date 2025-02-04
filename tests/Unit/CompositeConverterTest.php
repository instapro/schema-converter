<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Test\Unit;

use Instapro\SchemaConverter\CompositeConverter;
use Instapro\SchemaConverter\ConverterException;
use Instapro\SchemaConverter\Test\TestFramework\BuggyConverter;
use Instapro\SchemaConverter\Test\TestFramework\DummyConverter;
use Instapro\SchemaConverter\Test\TestFramework\DummySchema;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @small
 */
final class CompositeConverterTest extends TestCase
{
    #[Test]
    public function it_should_convert_to_schema_using_the_first_possible_converter(): void
    {
        $expected = new DummySchema(uniqid());

        $converter = new CompositeConverter(
            new DummyConverter(schemas: [$expected]),
            new DummyConverter(),
            new DummyConverter(),
        );
        $actual = $converter->toSchema('something');

        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function it_should_convert_to_schema_skipping_failed_converter(): void
    {
        $innerConverter = new DummyConverter();

        $type = 'bar';
        $expected = $innerConverter->toSchema($type);

        $converter = new CompositeConverter(new BuggyConverter(), new BuggyConverter(), $innerConverter);
        $actual = $converter->toSchema($type);

        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function it_should_throw_an_exception_when_cannot_find_a_suitable_converter(): void
    {
        $type = 'bar';

        $this->expectExceptionObject(ConverterException::toSchema($type));

        $converter = new CompositeConverter(new BuggyConverter());
        $converter->toSchema($type);
    }

    #[Test]
    public function it_should_cast_value_using_the_first_possible_converter(): void
    {
        $expected = uniqid();

        $converter = new CompositeConverter(
            new DummyConverter(values: [$expected]),
            new DummyConverter(),
            new DummyConverter(),
        );
        $actual = $converter->castValue('foo', 'qux');

        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function it_should_cast_value_skipping_failed_converters(): void
    {
        $innerConverter = new DummyConverter();

        $type = 'foo';
        $value = 'qux';
        $expected = $innerConverter->castValue($type, $value);

        $converter = new CompositeConverter(new BuggyConverter(), $innerConverter);
        $actual = $converter->castValue($type, $value);

        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function it_should_cast_value_to_null_when_cannot_find_a_suitable_converter(): void
    {
        $type = 'foo';
        $value = 'qux';

        $converter = new CompositeConverter(new BuggyConverter());

        $actual = $converter->castValue($type, $value);

        self::assertNull($actual);
    }
}
