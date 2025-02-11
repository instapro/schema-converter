<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Test\Unit;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Instapro\SchemaConverter\ConverterException;
use Instapro\SchemaConverter\DateTimeConverter;
use Instapro\SchemaConverter\Schemas\SimpleSchema;
use Instapro\SchemaConverter\Test\Fixtures\DateTimes\CustomDateTime;
use Instapro\SchemaConverter\Test\Fixtures\DateTimes\DateTimeWithPrivateConstructor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @small
 */
final class DateTimeConverterTest extends TestCase
{
    #[Test]
    public function it_should_convert_to_schema_with_date_time(): void
    {
        $type = DateTime::class;
        $expected = new SimpleSchema('datetime');

        $converter = new DateTimeConverter();
        $actual = $converter->toSchema($type);

        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function it_should_convert_to_schema_with_date_time_immutable(): void
    {
        $type = DateTimeImmutable::class;
        $expected = new SimpleSchema('datetime');

        $converter = new DateTimeConverter();
        $actual = $converter->toSchema($type);

        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function it_should_throw_an_exception_when_cannot_convert_to_schema(): void
    {
        $type = 'non-date-time-type';

        $this->expectExceptionObject(ConverterException::toSchema($type));

        $converter = new DateTimeConverter();
        $converter->toSchema($type);
    }

    #[Test]
    public function it_should_cast_value_using_date_time_immutable(): void
    {
        $expected = new DateTimeImmutable();

        $type = DateTimeImmutable::class;
        $value = $expected->format(DateTimeImmutable::ATOM);

        $converter = new DateTimeConverter();
        $actual = $converter->castValue($type, $value);

        self::assertInstanceOf(DateTimeInterface::class, $actual);
        self::assertDateTimeEquals($expected, $actual);
    }

    #[Test]
    #[DataProvider('dateTimeProvider')]
    public function it_should_cast_value_using_date_time(DateTimeInterface $expected): void
    {
        $type = $expected::class;
        $value = $expected->format(DateTime::ATOM);

        $converter = new DateTimeConverter();
        $actual = $converter->castValue($type, $value);

        self::assertInstanceOf(DateTimeInterface::class, $actual);
        self::assertDateTimeEquals($expected, $actual);
    }

    /** @return array<string, array{DateTimeInterface}> */
    public static function dateTimeProvider(): array
    {
        return [
            'DateTimeImmutable' => [new DateTimeImmutable()],
            'DateTime' => [new DateTime()],
            'CustomDateTime' => [new CustomDateTime()],
            'DateTimeWithPrivateConstructor' => [DateTimeWithPrivateConstructor::fromString('2025-02-17T15:43:40+00:00')],
        ];
    }

    #[Test]
    public function it_should_throw_an_exception_when_cannot_cast_value_with_non_date_time_type(): void
    {
        $type = 'not-date-time';
        $value = 'some value';

        $this->expectExceptionObject(ConverterException::castValue($type, $value));

        $converter = new DateTimeConverter();
        $converter->castValue($type, $value);
    }

    #[Test]
    public function it_should_throw_an_exception_when_cannot_cast_value_with_a_valid_time_type(): void
    {
        $type = DateTimeImmutable::class;
        $value = 'some value';

        $this->expectExceptionObject(ConverterException::castValue($type, $value));

        $converter = new DateTimeConverter();
        $converter->castValue($type, $value);
    }

    private function assertDateTimeEquals(DateTimeInterface $expected, DateTimeInterface $actual): void
    {
        self::assertSame($expected::class, $actual::class);
        self::assertSame($expected->format(DateTimeInterface::ATOM), $actual->format(DateTimeInterface::ATOM));
    }
}
