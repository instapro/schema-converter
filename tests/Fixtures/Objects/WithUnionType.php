<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Test\Fixtures\Objects;

final class WithUnionType
{
    public function __construct(
        public string|int $parameter1,
        public bool|float $parameter2,
    ) {
    }
}
