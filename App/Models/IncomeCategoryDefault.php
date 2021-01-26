<?php


namespace App\Models;
use PDO;


class IncomeCategoryDefault extends \Core\Model
{
    public static function getAll() {

        $sql = 'SELECT * FROM incomes_category_default';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}