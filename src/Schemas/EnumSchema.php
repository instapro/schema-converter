<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Schemas;

use Instapro\SchemaConverter\Schema;

final readonly class EnumSchema implements Schema
{
    /** @var array<string> */
    private array $allowedValues;

    public function __construct(
        string $allowedValue,
        string ...$allowedValues,
    ) {
        $this->allowedValues = [$allowedValue, ...$allowedValues];
    }

    public function getType(): string
    {
        return 'enum';
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return ['type' => $this->getType(), 'allowedValues' => $this->allowedValues];
    }
}
