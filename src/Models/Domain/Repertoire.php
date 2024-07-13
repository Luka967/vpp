<?php

namespace Skop\Models\Domain;

use DateTime;
use Error;
use Skop\Core\DomainObject;
use Skop\Models\MovieModel;
use Skop\Models\ScreeningFeatureModel;
use Skop\Models\TheaterModel;

final class Repertoire extends DomainObject
{
    public int $movie_id;
    public int $theater_id;
    public string $screening_start;

    public function screeningTimestamp(): int
    {
        $valString = $this->screening_start;
        $val = strtotime($valString);
        if ($val == false)
            throw new Error("screening_start value '$valString' could not be converted to timestamp");
        return $val;
    }
    public function movie(): Movie
    {
        return MovieModel::withId($this->movie_id);
    }
    public function theater(): Theater
    {
        return TheaterModel::withId($this->theater_id);
    }
    public function screeningFeatures(): array
    {
        return ScreeningFeatureModel::ofRepertoireEntry($this);
    }

    public function hasStarted(): bool
    {
        $diff = new DateTime();
        $diff = $diff->diff(new DateTime($this->screening_start));
        return $diff->invert;
    }
    public function hasReservationsOnline(): bool
    {
        $diff = new DateTime();
        $diff = $diff->diff(new DateTime($this->screening_start));
        $minutes = $diff->d * 60 * 24 + $diff->h * 60 + $diff->i;
        return !$diff->invert && $minutes >= SKOP_CONFIG['minsReceptionOnly'];
    }

    public static array $columnTraits = [
        'id'                => ['type' => 'int'         , 'editable' => false, 'partial' => true , 'min' => 0, 'max' => BIGINT_U_MAX],
        'movie_id'          => ['type' => 'id|movie'    , 'editable' => false, 'partial' => false, 'min' => 0, 'max' => INT_U_MAX],
        'theater_id'        => ['type' => 'id|theater'  , 'editable' => false, 'partial' => false, 'min' => 0, 'max' => SMALLINT_U_MAX],
        'screening_start'   => ['type' => 'datetime'    , 'editable' => false, 'partial' => false, 'min' => 0, 'max' => SMALLINT_U_MAX],
    ];
}
