<?php

namespace Skop\Models\Domain;

use Skop\Core\DomainObject;
use DateTime;

final class Movie
{
    use DomainObject;

    public int $id;
    public string $title;
    /** `ENUM('A', '13', 'R')` */
    public string $rating;
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
