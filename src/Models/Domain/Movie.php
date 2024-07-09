<?php

namespace Skop\Models\Domain;

use Skop\Core\DomainObject;

final class Movie extends DomainObject
{
    public string $title;
    /** `ENUM('A', '13', 'R')` */
    public string $rating;
    public string $original_title;
    public string $producer_studio;
    public string $release_date;
    public string $synopsis;
    public int $runtime;
    public string $director;
    public string $significant_cast_1;
    public string $significant_cast_2;
    public string $significant_cast_3;

    public static array $columnTraits = [
        'id'                    => ['type' => 'int'               , 'editable' => false, 'partial' => true , 'min' => 0, 'max' => INT_U_MAX],
        'title'                 => ['type' => 'string'            , 'editable' => true , 'partial' => false, 'min' => 0, 'max' => 127],
        'rating'                => ['type' => 'string|movierating', 'editable' => true , 'partial' => false],
        'original_title'        => ['type' => 'string'            , 'editable' => true , 'partial' => false, 'min' => 0, 'max' => 127],
        'producer_studio'       => ['type' => 'string'            , 'editable' => true , 'partial' => false, 'min' => 0, 'max' => 127],
        'release_date'          => ['type' => 'date'              , 'editable' => true , 'partial' => false],
        'synopsis'              => ['type' => 'string'            , 'editable' => true , 'partial' => false, 'min' => 0, 'max' => 65535],
        'runtime'               => ['type' => 'int'               , 'editable' => true , 'partial' => false, 'min' => 0, 'max' => SMALLINT_U_MAX],
        'director'              => ['type' => 'string'            , 'editable' => true , 'partial' => false, 'min' => 0, 'max' => 127],
        'significant_cast_1'    => ['type' => 'string'            , 'editable' => true , 'partial' => false, 'min' => 0, 'max' => 127],
        'significant_cast_2'    => ['type' => 'string'            , 'editable' => true , 'partial' => false, 'min' => 0, 'max' => 127],
        'significant_cast_3'    => ['type' => 'string'            , 'editable' => true , 'partial' => false, 'min' => 0, 'max' => 127],
    ];
}
