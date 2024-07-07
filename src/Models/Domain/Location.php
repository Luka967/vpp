<?php

namespace Skop\Models\Domain;

use Skop\Core\DomainObject;

final class Location
{
    use DomainObject;

    public int $id;
    public string $city;
    public string $address;
}
