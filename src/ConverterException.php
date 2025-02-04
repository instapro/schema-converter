<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter;

use LogicException;
use function sprintf;

final class ConverterException extends LogicException
{
    public static function toSchema(string $type): self
    {
        return new self(sprintf('Could not convert "%s" to schema', $type));
    }

    public static function castValue(string $type, mixed $value): self
    {
        return new self(sprintf('Could convert "%s" into "%s"', $type, print_r($value, true)));
    }
}
