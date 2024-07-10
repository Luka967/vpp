<?php

namespace Skop\Models;

use Skop\Core\Db;
use Skop\Core\Model;
use Skop\Models\Domain\ScreeningFeature;

class ScreeningFeatureModel extends Model
{
    protected static string $tableName = '`screening_features`';
    const CLASS_PATH = 'Skop\\Models\\Domain\\ScreeningFeature';

    public static function all(): array
    {
        $q = Db::instance()->prepare("SELECT * FROM `screening_features`");
        $q->execute();
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH);
    }

    public static function withId(int $id): ?ScreeningFeature
    {
        $q = Db::instance()->prepare("SELECT * FROM `screening_features` WHERE `id` = :id");
        $q->bindValue(':id', $id, \PDO::PARAM_INT);
        $q->execute();
        if ($q->rowCount() == 0)
            return null;
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH)[0];
    }
    public static function withDescription(string $name): ?ScreeningFeature
    {
        $q = Db::instance()->prepare("SELECT * FROM `screening_features` WHERE `name` = :name");
        $q->bindValue(':name', $name, \PDO::PARAM_STR);
        $q->execute();
        if ($q->rowCount() == 0)
            return null;
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH)[0];
    }
}
