<?php

declare(strict_types=1);

namespace Instapro\SchemaConverter\Test\Fixtures\Entities;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class RealSecondEntity
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column]
        public int $id,
    ) {
    }
}
