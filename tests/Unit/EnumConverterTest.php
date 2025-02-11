<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Test\Unit;

use Instapro\SchemaConverter\ConverterException;
use Instapro\SchemaConverter\EnumConverter;
use Instapro\SchemaConverter\Schemas\EnumSchema;
use Instapro\SchemaConverter\Test\Fixtures\Enums\Backed;
use Instapro\SchemaConverter\Test\Fixtures\Enums\Basic;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @small
 */
final class EnumConverterTest extends TestCase
{
    #[Test]
    public function it_should_convert_to_schema_with_basic_enum(): void
    {
        $type = Basic::class;
        $expected = new EnumSchema('First', 'Second', 'Third');

        $converter = new EnumConverter();
        $actual = $converter->toSchema($type);

        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function it_should_convert_to_schema_with_backed_enum(): void
    {
        $type = Backed::class;
        $expected = new EnumSchema('First', 'Second', 'Third');

        $converter = new EnumConverter();
        $actual = $converter->toSchema($type);

        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function it_should_throw_an_exception_when_cannot_convert_to_schema(): void
    {
        $type = 'not-an-enum';

        $this->expectExceptionObject(ConverterException::toSchema($type));

        $converter = new EnumConverter();
        $converter->toSchema($type);
    }

    #[Test]
    public function it_should_cast_value_using_basic_value(): void
    {
        $type = Basic::class;
        $value = Basic::First->name;
        $expected = Basic::First;

        $converter = new EnumConverter();
        $actual = $converter->castValue($type, $value);

        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function it_should_cast_value_using_backed_value(): void
    {
        $type = Backed::class;
        $value = Backed::Second->name;
        $expected = Backed::Second;

        $converter = new EnumConverter();
        $actual = $converter->castValue($type, $value);

        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function it_should_throw_an_exception_when_cannot_cast_value(): void
    {
        $value = 123;
        $type = 'not-an-enum';

        $this->expectExceptionObject(ConverterException::castValue($type, $value));

        $converter = new EnumConverter();
        $converter->castValue($type, $value);
    }
}
