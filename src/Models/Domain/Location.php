<?php

namespace Skop\Models\Domain;

use Skop\Core\Serializable;

final class Location
{
    use Serializable;

    public int $id;
    public string $city;
    public string $address;
}
