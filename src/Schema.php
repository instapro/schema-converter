<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter;

interface Schema
{
    public function getType(): string;

    /** @return array<string, mixed> */
    public function toArray(): array;
}
