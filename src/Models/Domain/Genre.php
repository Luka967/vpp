<?php

namespace Skop\Models\Domain;

use Skop\Core\DomainObject;

final class Genre
{
    use DomainObject;

    public int $id;
    public string $name;
    public string $description;
}
