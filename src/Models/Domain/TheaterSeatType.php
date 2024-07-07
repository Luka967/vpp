<?php

namespace Skop\Models\Domain;

use Skop\Core\DomainObject;

final class TheaterSeatType
{
    use DomainObject;

    public int $id;
    public string $name;
    public int $price_adult;
    public ?int $price_child;
}
