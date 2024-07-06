<?php

namespace Skop\Models\Domain;

use Skop\Core\Serializable;
use DateTime;

final class Repertoire
{
    use Serializable;

    public int $id;
    public int $movie_id;
    public int $theater_id;
    public DateTime $screening_start;
}
