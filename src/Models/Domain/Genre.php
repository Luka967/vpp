<?php

namespace Skop\Models\Domain;

use Skop\Core\Serializable;

final class Genre
{
    use Serializable;

    public int $id;
    public string $name;
    public string $description;
}
