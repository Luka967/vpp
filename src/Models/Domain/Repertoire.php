<?php

namespace Skop\Models\Domain;

use Skop\Core\DomainObject;

final class Repertoire extends DomainObject
{
    public int $movie_id;
    public int $theater_id;
    public string $screening_start;

    public static array $columnTraits = [
        'id'                => ['type' => 'int' , 'editable' => false , 'partial' => true , 'min' => 0, 'max' => BIGINT_U_MAX],
        'movie_id'          => ['type' => 'int' , 'editable' => false , 'partial' => false, 'min' => 0, 'max' => INT_U_MAX],
        'theater_id'        => ['type' => 'int' , 'editable' => false , 'partial' => false, 'min' => 0, 'max' => SMALLINT_U_MAX],
        'theater_id'        => ['type' => 'int' , 'editable' => false , 'partial' => false, 'min' => 0, 'max' => SMALLINT_U_MAX],
        'screening_start'   => ['type' => 'date', 'editable' => false , 'partial' => false, 'min' => 0, 'max' => SMALLINT_U_MAX],
    ];
}
