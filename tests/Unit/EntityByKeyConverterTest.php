<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Test\Unit;

use Instapro\SchemaConverter\ConverterException;
use Instapro\SchemaConverter\EntityByKeyConverter;
use Instapro\SchemaConverter\Schemas\SimpleSchema;
use Instapro\SchemaConverter\Test\Fixtures\Entities\FakeEntity;
use Instapro\SchemaConverter\Test\Fixtures\Entities\RealEntity;
use Instapro\SchemaConverter\Test\TestFramework\EntityManagerFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use function sprintf;

/**
 * @internal
 * @small
 */
final class EntityByKeyConverterTest extends TestCase
{
    #[Test]
    public function it_should_convert_to_schema(): void
    {
        $schemaType = 'real_entity';
        $entityClass = RealEntity::class;
        $entityProperty = 'id';

        $type = $entityClass;
        $expected = new SimpleSchema($schemaType);

        $converter = new EntityByKeyConverter(EntityManagerFactory::create(), $schemaType, $entityClass, $entityProperty);
        $actual = $converter->toSchema($type);

        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function it_should_throw_an_exception_when_cannot_convert_to_schema(): void
    {
        $entityClass = RealEntity::class;
        $schemaType = 'real_entity';
        $entityProperty = 'id';

        $type = FakeEntity::class;

        $this->expectExceptionObject(ConverterException::toSchema($type));

        $converter = new EntityByKeyConverter(EntityManagerFactory::create(), $schemaType, $entityClass, $entityProperty);
        $converter->toSchema($type);
    }

    #[Test]
    public function it_should_cast_value(): void
    {
        $schemaType = 'real_entity';
        $entityClass = RealEntity::class;
        $entityProperty = 'id';

        $type = $entityClass;
        $value = 123;
        $expected = new RealEntity(id: $value);

        $entityManager = EntityManagerFactory::create();
        $entityManager->persist($expected);
        $entityManager->flush();

        $converter = new EntityByKeyConverter($entityManager, $schemaType, $entityClass, $entityProperty);
        $actual = $converter->castValue($type, $value);

        self::assertInstanceOf(RealEntity::class, $expected);
        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function it_should_throw_an_exception_when_type_is_incorrect(): void
    {
        $entityClass = RealEntity::class;
        $schemaType = 'real_entity';
        $entityProperty = 'id';

        $type = 'wrong-type';
        $value = 123;

        $this->expectExceptionObject(ConverterException::castValue($type, $value));

        $converter = new EntityByKeyConverter(EntityManagerFactory::create(), $schemaType, $entityClass, $entityProperty);
        $converter->castValue($type, $value);
    }

    #[Test]
    public function it_should_throw_an_exception_when_internal_type_is_incorrect(): void
    {
        $entityClass = RealEntity::class;
        $schemaType = 'real_entity';
        $entityProperty = 'id';

        $type = FakeEntity::class;
        $value = 123;

        $this->expectExceptionObject(ConverterException::castValue($type, $value));

        $converter = new EntityByKeyConverter(EntityManagerFactory::create(), $schemaType, $entityClass, $entityProperty);
        $converter->castValue($type, $value);
    }

    #[Test]
    public function it_should_throw_an_exception_when_cannot_convert_find_entity(): void
    {
        $schemaType = 'real_entity';
        $entityClass = RealEntity::class;
        $entityProperty = 'id';

        $type = $entityClass;
        $value = 123;

        $this->expectExceptionObject(new ConverterException(sprintf('Could not find entity by "%s" with "%s"', $entityProperty, $value)));

        $converter = new EntityByKeyConverter(EntityManagerFactory::create(), $schemaType, $entityClass, $entityProperty);
        $converter->castValue($type, $value);
    }
}
