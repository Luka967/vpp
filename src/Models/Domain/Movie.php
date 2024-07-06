<?php

namespace Skop\Models\Domain;

use Skop\Core\Serializable;
use DateTime;

final class Movie
{
    use Serializable;

    public int $id;
    public string $title;
    public string $original_title;
    public string $producer_studio;
    public DateTime $release_date;
    public string $synopsis;
    public int $runtime;
    public string $director;
    public string $significant_cast_1;
    public string $significant_cast_2;
    public string $significant_cast_3;
}
