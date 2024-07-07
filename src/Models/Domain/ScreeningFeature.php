<?php

namespace Skop\Models\Domain;

use Skop\Core\DomainObject;

final class ScreeningFeature
{
    use DomainObject;

    public int $id;
    public string $description;
}
