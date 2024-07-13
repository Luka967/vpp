<?php

namespace Skop\Models;

use Skop\Core\Db;
use Skop\Core\Model;
use Skop\Models\Domain\Repertoire;
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
        $q = Db::instance()->prepare("SELECT * FROM `screening_features` WHERE `description` = :description");
        $q->bindValue(':description', $name, \PDO::PARAM_STR);
        $q->execute();
        if ($q->rowCount() == 0)
            return null;
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH)[0];
    }

    public static function ofRepertoireEntry(Repertoire $entry): array
    {
        $q = Db::instance()
            ->prepare(
                "SELECT `screening_features`.* FROM `repertoire_features`
                    INNER JOIN `screening_features` ON `screening_features`.`id` = `repertoire_features`.`feature_id`
                WHERE `repertoire_features`.`screening_id` = ?"
            );
        $q->execute([$entry->id]);
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH);
    }
    public static function setForRepertoireEntry(Repertoire $entry, array $features)
    {
        Db::instance()->beginTransaction();
        Db::instance()
            ->prepare("DELETE FROM `repertoire_features` WHERE `repertoire_features`.`screening_id` = ?")
            ->execute([$entry->id]);
        if (count($features) == 0)
        {
            Db::instance()->commit();
            return;
        }
        $insertRowsBind = substr(str_repeat('(?, ?),', count($features)), 0, -1);
        $insertRows = [];
        foreach ($features as $feature)
        {
            $insertRows[] = $entry->id;
            $insertRows[] = $feature->id;
        }
        Db::instance()
            ->prepare("INSERT INTO `repertoire_features` (`screening_id`, `feature_id`) VALUES $insertRowsBind")
            ->execute($insertRows);
        Db::instance()->commit();
    }
}
