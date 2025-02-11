<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Test\Fixtures\Enums;

enum Backed: int
{
    case First = 1;
    case Second = 2;
    case Third = 3;
}
