<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Test\TestFramework;

use Instapro\SchemaConverter\Schema;

final readonly class DummySchema implements Schema
{
    /** @param array<string, mixed> $metadata */
    public function __construct(
        private string $name,
        private array $metadata = [],
    ) {
    }

    public function getType(): string
    {
        return $this->name;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->metadata;
    }
}
