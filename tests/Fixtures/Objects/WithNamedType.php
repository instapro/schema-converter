<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Test\Fixtures\Objects;

final class WithNamedType
{
    public function __construct(
        public bool $parameter,
    ) {
    }
}
