<?php


namespace App\Models;
use App\Auth;
use PDO;


class IncomeCategoryAssignedToUser extends \Core\Model
{
    public static function getAll() {

        $loggedUserID = Auth::getUser()->id;

        $sql = 'SELECT id, name FROM incomes_category_assigned_to_users WHERE user_id=:logged_user_id;';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':logged_user_id', $loggedUserID, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}