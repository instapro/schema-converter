<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Test\Fixtures\Objects;

use Countable;
use Iterator;

final class WithIntersectionType
{
    public function __construct(
        public Iterator&Countable $parameter,
    ) {
    }
}
