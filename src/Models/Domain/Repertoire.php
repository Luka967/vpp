<?php

namespace Skop\Models\Domain;

use Skop\Core\DomainObject;
use DateTime;

final class Repertoire
{
    use DomainObject;

    public int $id;
    public int $movie_id;
    public int $theater_id;
    public DateTime $screening_start;
}
