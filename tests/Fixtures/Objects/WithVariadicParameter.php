<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Test\Fixtures\Objects;

final class WithVariadicParameter
{
    /** @var array<string> */
    public array $parameter;

    public function __construct(string ...$parameter)
    {
        $this->parameter = $parameter;
    }
}
