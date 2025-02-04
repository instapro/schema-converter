<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Test\Fixtures\Objects;

class Level1
{
    public function __construct(
        Level2 $parameter1,
        WithNullableParameter $parameter2,
    ) {
    }
}
