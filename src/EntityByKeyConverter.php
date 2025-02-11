<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter;

use Doctrine\ORM\EntityManagerInterface;
use Instapro\SchemaConverter\Schemas\SimpleSchema;
use function sprintf;

final readonly class EntityByKeyConverter implements Converter
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private string $schemaType,
        private string $entityClass,
        private string $entityProperty,
    ) {
    }

    public function toSchema(string $type): Schema
    {
        if (!$this->isConvertable($type)) {
            throw ConverterException::toSchema($type);
        }

        return new SimpleSchema($this->schemaType);
    }

    public function castValue(string $type, mixed $value): mixed
    {
        if (!$this->isConvertable($type)) {
            throw ConverterException::castValue($type, $value);
        }

        // @phpstan-ignore-next-line
        $entity = $this->entityManager->getRepository($type)->findOneBy([$this->entityProperty => $value]);
        if ($entity === null) {
            throw new ConverterException(sprintf('Could not find entity by "%s" with "%s"', $this->entityProperty, print_r($value, true)));
        }

        return $entity;
    }

    private function isConvertable(string $internalType): bool
    {
        return is_subclass_of($internalType, $this->entityClass) || $internalType === $this->entityClass;
    }
}
