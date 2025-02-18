<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Schemas;

use Instapro\SchemaConverter\Schema;

final readonly class ListSchema implements Schema
{
    public function __construct(
        private Schema $schema,
    ) {
    }

    public function getType(): string
    {
        return 'list';
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return ['type' => $this->getType(), 'items' => $this->schema->toArray()];
    }
}
