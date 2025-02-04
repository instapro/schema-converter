<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Schemas;

use Instapro\SchemaConverter\Schema;

final readonly class ObjectSchema implements Schema
{
    /** @var array<ObjectParameter> */
    private array $parameters;

    public function __construct(ObjectParameter ...$properties)
    {
        $this->parameters = $properties;
    }

    public function getType(): string
    {
        return 'object';
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        if ($this->parameters === []) {
            return ['type' => $this->getType()];
        }

        $parameters = [];
        foreach ($this->parameters as $parameter) {
            $parameters[$parameter->name] = ['required' => $parameter->isRequired] + $parameter->schema->toArray();
        }

        return ['type' => $this->getType(), 'parameters' => $parameters];
    }
}
