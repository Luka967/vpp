<?php

namespace Skop\Models\Domain;

use Skop\Core\Serializable;

final class TheaterSeatType
{
    use Serializable;

    public int $id;
    public string $name;
    public int $price_adult;
    public ?int $price_child;
}
