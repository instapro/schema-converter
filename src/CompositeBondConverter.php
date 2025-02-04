<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter;

/** This interface can be useful when you have converters that deal with recursive data. */
interface CompositeBondConverter
{
    public function bindConverter(Converter $converter): void;
}
