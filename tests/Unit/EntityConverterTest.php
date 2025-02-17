<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Test\Unit;

use Instapro\SchemaConverter\ConverterException;
use Instapro\SchemaConverter\EntityConverter;
use Instapro\SchemaConverter\Schemas\SimpleSchema;
use Instapro\SchemaConverter\Test\Fixtures\Entities\FakeEntity;
use Instapro\SchemaConverter\Test\Fixtures\Entities\RealEntity;
use Instapro\SchemaConverter\Test\TestFramework\EntityManagerFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @small
 */
final class EntityConverterTest extends TestCase
{
    #[Test]
    public function it_should_convert_to_schema(): void
    {
        $expected = new SimpleSchema('identifier');
        $type = RealEntity::class;

        $converter = new EntityConverter(EntityManagerFactory::create());
        $actual = $converter->toSchema($type);

        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function it_should_throw_an_exception_when_cannot_convert_to_schema(): void
    {
        $type = FakeEntity::class;

        $this->expectExceptionObject(ConverterException::toSchema($type));

        $converter = new EntityConverter(EntityManagerFactory::create());
        $converter->toSchema($type);
    }

    #[Test]
    public function it_should_cast_value(): void
    {
        $type = RealEntity::class;
        $value = 123;
        $expected = new RealEntity($value);

        $entityManager = EntityManagerFactory::create();
        $entityManager->persist($expected);
        $entityManager->flush();

        $converter = new EntityConverter($entityManager);
        $actual = $converter->castValue($type, $value);

        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function it_should_throw_an_exception_when_type_is_invalid(): void
    {
        $type = FakeEntity::class;
        $value = 123;

        $this->expectExceptionObject(ConverterException::castValue($type, $value));

        $converter = new EntityConverter(EntityManagerFactory::create());
        $converter->castValue($type, $value);
    }

    #[Test]
    public function it_should_throw_an_exception_when_cannot_find_entity(): void
    {
        $type = RealEntity::class;
        $value = 123;

        $this->expectExceptionObject(ConverterException::castValue($type, $value));

        $converter = new EntityConverter(EntityManagerFactory::create());
        $converter->castValue($type, $value);
    }
}
