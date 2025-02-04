<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Schemas;

use Instapro\SchemaConverter\Schema;

final readonly class ObjectParameter
{
    public function __construct(
        public string $name,
        public Schema $schema,
        public bool $isRequired,
    ) {
    }
}
