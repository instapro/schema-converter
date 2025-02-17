<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Instapro\SchemaConverter\Schemas\SimpleSchema;
use Throwable;

final readonly class EntityConverter implements Converter
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function toSchema(string $type): Schema
    {
        try {
            $this->getRepository($type);
        } catch (Throwable) {
            throw ConverterException::toSchema($type);
        }

        return new SimpleSchema('identifier');
    }

    public function castValue(string $type, mixed $value): mixed
    {
        try {
            $entity = $this->getRepository($type)->find($value);
        } catch (Throwable) {
            throw ConverterException::castValue($type, $value);
        }

        if ($entity === null) {
            throw ConverterException::castValue($type, $value);
        }

        return $entity;
    }

    // @phpstan-ignore-next-line
    private function getRepository(string $internalType): EntityRepository
    {
        // @phpstan-ignore-next-line
        return $this->entityManager->getRepository($internalType);
    }
}
