<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Test\Fixtures\Objects;

class Level2
{
    public function __construct(
        WithOptionalParameter $parameter1,
        WithNamedType $parameter2,
    ) {
    }
}
