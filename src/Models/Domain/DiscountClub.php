<?php

namespace Skop\Models\Domain;

use Skop\Core\Serializable;

final class DiscountClub
{
    use Serializable;

    public int $id;
    public string $name;
    public int $discount;
}
