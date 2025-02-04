<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Test\Fixtures\Objects;

final class WithNullableParameter
{
    public function __construct(
        public ?int $parameter,
    ) {
    }
}
