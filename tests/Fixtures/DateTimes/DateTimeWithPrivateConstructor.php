<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Test\Fixtures\DateTimes;

use DateTimeImmutable;

final class DateTimeWithPrivateConstructor extends DateTimeImmutable
{
    private function __construct(string $dateTime)
    {
        parent::__construct($dateTime);
    }

    public static function fromString(string $dateTime): self
    {
        return new self($dateTime);
    }
}
