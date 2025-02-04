<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Test\Fixtures\Objects;

class WithNestedParameters
{
    public function __construct(
        public Level1 $parameter,
    ) {
    }
}
