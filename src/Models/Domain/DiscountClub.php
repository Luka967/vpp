<?php

namespace Skop\Models\Domain;

use Skop\Core\DomainObject;

final class DiscountClub
{
    use DomainObject;

    public int $id;
    public string $name;
    public int $discount;
}
