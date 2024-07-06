<?php

namespace Skop\Models\Domain;

use Skop\Core\Serializable;

final class ScreeningFeature
{
    use Serializable;

    public int $id;
    public string $description;
}
