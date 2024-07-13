<?php

namespace Skop\Models;

use DateInterval;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Skop\Core\Db;
use Skop\Core\ErrorPageException;
use Skop\Core\Model;
use Skop\Models\Domain\Repertoire;

function intervalToMinutes(DateInterval $val): int
{
    return $val->d * 60 * 24 + $val->h * 60 + $val->i;
}

class RepertoireModel extends Model
{
    protected static string $tableName = '`repertoire`';
    const CLASS_PATH = 'Skop\\Models\\Domain\\Repertoire';

    public static function all(): array
    {
        $q = Db::instance()->prepare("SELECT * FROM `repertoire`");
        $q->execute();
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH);
    }
    public static function allWithin(DateTimeInterface $from, DateTimeInterface $to): array
    {
        $q = Db::instance()->prepare("SELECT * FROM `repertoire` WHERE `screening_start` BETWEEN ? AND ? ORDER BY screening_start ASC");
        $q->execute([$from->format(DateTime::ATOM), $to->format(DateTime::ATOM)]);
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH);
    }
    public static function exact(int $movieId, int $theaterId, string $screeningStart): ?Repertoire
    {
        $q = Db::instance()->prepare("SELECT * FROM `repertoire` WHERE `movie_id` = ? AND `theater_id` = ? AND `screening_start` = ?");
        $q->execute([$movieId, $theaterId, $screeningStart]);
        if ($q->rowCount() == 0)
            return null;
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH)[0];
    }

    public static function withId(int $id): ?Repertoire
    {
        $q = Db::instance()->prepare("SELECT * FROM `repertoire` WHERE `id` = :id");
        $q->bindValue(':id', $id, \PDO::PARAM_INT);
        $q->execute();
        if ($q->rowCount() == 0)
            return null;
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH)[0];
    }

    public static function tryGenerate(?Repertoire $adding = null, bool $showNextCycle = false)
    {
        $dayTimestamps = [];
        $dayEntries = [];
        $dayEntryGaps = [];

        $nextDayInterval = DateInterval::createFromDateString('23 hours 59 minutes 59 seconds 999999 microseconds');

        $cycle = new DateTime('now', new DateTimeZone('UTC'));
        $cycle = $cycle->modify('last Wednesday');

        $theaters = [];
        foreach (TheaterModel::all() as $theater)
            if ($theater->active)
                $theaters[$theater->id] = $theater;

        $moviesSeen = [];
        $movies = [];
        $allMovies = [];
        foreach (MovieModel::all() as $movie)
        {
            $allMovies[$movie->id] = $movie;
            $movies[$movie->id] = $movie;
            $moviesSeen[$movie->id] = false;
        }

        $dayCount = $showNextCycle ? 14 : 7;
        while (count($dayTimestamps) < $dayCount)
        {
            $dayTimestamps[] = $cycle->getTimestamp();

            $cycleEnd = DateTime::createFromInterface($cycle)->add($nextDayInterval);

            $thisDayAllEntries = RepertoireModel::allWithin($cycle, $cycleEnd);
            $thisDayEntries = [];
            $thisDayGaps = [];

            foreach ($theaters as $theater)
            {
                $thisTheaterGaps = [];

                $theaterRepertoire = [];
                foreach ($thisDayAllEntries as $entry)
                {
                    if ($entry->theater_id == $theater->id)
                        $theaterRepertoire[] = $entry;
                    $moviesSeen[$entry->movie_id] = true;
                }

                // Ubaci $adding ako treba da se nalazi ovde
                if (
                    $adding != null
                    && date('Y-m-d', strtotime($adding->screening_start)) == $cycle->format('Y-m-d')
                    && $adding->theater_id == $theater->id
                ) {
                    $moviesSeen[$adding->movie_id] = true;
                    $theaterRepertoire[] = $adding;
                    // Nađi mesto za $adding. Insertion sort bi bio jednostavniji ali ovo je praktičnije
                    usort($theaterRepertoire, function (Repertoire $a, Repertoire $b)
                    {
                        $aTime = strtotime($a->screening_start);
                        $bTime = strtotime($b->screening_start);
                        if ($aTime < $bTime)
                            return -1;
                        if ($aTime > $bTime)
                            return 1;
                        return 0;
                    });
                }

                for ($i = 0; $i < count($theaterRepertoire) - 1;)
                {
                    $currEntry = $theaterRepertoire[$i];
                    $nextEntry = $theaterRepertoire[++$i];

                    $currEnd = new DateTime($currEntry->screening_start);
                    $currEnd->add(DateInterval::createFromDateString('+' . $movies[$currEntry->movie_id]->runtime . ' minutes'));
                    $nextStart = new DateTime($nextEntry->screening_start);

                    $diff = $currEnd->diff($nextStart);
                    if ($diff->invert)
                    {
                        $currEndStr = $currEnd->format('H:i');
                        $nextStartStr = $nextStart->format('H:i');
                        $timing = "end $currEndStr start $nextStartStr";
                        if ($currEntry == $adding)
                            throw new ErrorPageException(SKOP_ERROR_CONFLICTING_REPERTOIRE, "New entry collided with entry id '$nextEntry->id', $timing");
                        else if ($nextEntry == $adding)
                            throw new ErrorPageException(SKOP_ERROR_CONFLICTING_REPERTOIRE, "New entry collided with entry id '$currEntry->id', $timing");
                        else
                            throw new ErrorPageException(0, "Repertoire entries '$currEntry->id' '$nextEntry->id' collide in time intervals, $timing");
                    }

                    $thisTheaterGaps[] = intervalToMinutes($diff);
                }

                $thisDayEntries[$theater->id] = $theaterRepertoire;
                $thisDayGaps[$theater->id] = $thisTheaterGaps;
            }
            $dayEntries[] = $thisDayEntries;
            $dayEntryGaps[] = $thisDayGaps;

            $cycle->modify('+1 day');
        }

        foreach ($movies as $movie)
            if (!$moviesSeen[$movie->id])
                unset($movies[$movie->id]);

        return [
            'allMovies' => $allMovies,
            'movies' => $movies,
            'theaters' => $theaters,
            'dayTimestamps' => $dayTimestamps,
            'dayEntries' => $dayEntries,
            'dayEntryGaps' => $dayEntryGaps
        ];
    }
}
