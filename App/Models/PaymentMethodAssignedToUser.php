<?php


namespace App\Models;
use App\Auth;
use PDO;


class PaymentMethodAssignedToUser extends \Core\Model
{

    public function __construct(){
        $this->user_id = Auth::getUser()->id;
    }

    public function getAll() {

        $sql = 'SELECT id, name FROM payment_methods_assigned_to_users WHERE user_id=:logged_user_id;';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':logged_user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}