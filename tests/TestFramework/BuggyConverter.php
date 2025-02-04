<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Test\TestFramework;

use Instapro\SchemaConverter\Converter;
use Instapro\SchemaConverter\ConverterException;
use Instapro\SchemaConverter\Schema;

final readonly class BuggyConverter implements Converter
{
    public function __construct(
        public ConverterException $exception = new ConverterException('Failed'),
    ) {
    }

    public function toSchema(string $type): Schema
    {
        throw $this->exception;
    }

    public function castValue(string $type, mixed $value): mixed
    {
        throw $this->exception;
    }
}
