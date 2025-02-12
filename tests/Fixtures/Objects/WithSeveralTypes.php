<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Test\Fixtures\Objects;

final class WithSeveralTypes
{
    public function __construct(
        public array $array,
        public string $string,
        public int $int,
        public float $float,
        public bool $bool,
        public mixed $mixed,
        public null $null,
    ) {
    }
}
