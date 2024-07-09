<?php

namespace Skop\Models\Domain;

use Skop\Core\DomainObject;

final class TheaterSeatType extends DomainObject
{
    public string $name;
    public int $price_adult;
    public ?int $price_child;
}
