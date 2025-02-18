<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Schemas;

use Instapro\SchemaConverter\Schema;

final readonly class OneOfSchema implements Schema
{
    /** @var array<Schema> */
    private array $options;

    public function __construct(
        Schema $schema1,
        Schema $schema2,
        Schema ...$schemas,
    ) {
        $this->options = [$schema1, $schema2, ...$schemas];
    }

    public function getType(): string
    {
        return 'oneOf';
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
            'options' => array_map(static fn (Schema $type) => $type->toArray(), $this->options),
        ];
    }
}
