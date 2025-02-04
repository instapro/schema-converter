<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Test\Fixtures\Objects;

final class WithOptionalParameter
{
    public function __construct(
        public string $parameter = 'Default',
    ) {
    }
}
