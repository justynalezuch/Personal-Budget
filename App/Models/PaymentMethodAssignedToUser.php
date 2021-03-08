<?php


namespace App\Models;
use App\Auth;
use PDO;


class PaymentMethodAssignedToUser extends \Core\Model
{

    public function __construct(){
        $this->user_id = Auth::getUser()->id;
    }

    /**
     * Error messages
     * @var array
     */
    public $errors = [];

    public function validate() {

        // Method name
        if($this->methodExists($this->name, $this->id ?? null)) {
            $this->errors[] = 'Istnieje już metoda o podanej nazwie.';
        }
        if (preg_match('/^[a-zA-Z\s]+$/', $this->name) == 0) {
            $this->errors[] = 'Nazwa metody może składać się z liter oraz spacji.';
        }
    }

    public function getAll() {

        $sql = 'SELECT id, name FROM payment_methods_assigned_to_users WHERE user_id=:logged_user_id;';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':logged_user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function methodExists($method, $ignore_id = null) {

        $user_payment_methods = $this->getAll();

        foreach ($user_payment_methods as $item) {
            if(strtolower($method) == strtolower($item['name'])) {
                if($item['id'] != $ignore_id) {
                    return true;
                }
            }
        }
        return false;
    }

    public function update($data) {

        $this->id = $data['payment_method_id'];
        $this->name = $data['payment_method_name'];

        $this->validate();

        if(empty($this->errors)) {

            $sql = 'UPDATE payment_methods_assigned_to_users SET name = :name WHERE id = :id;';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

            return $stmt->execute();
        }

        return false;
    }

    public function save($data) {

        $this->name = $data['payment_method_name'];

        $this->validate();

        if(empty($this->errors)) {

            $sql = 'INSERT INTO payment_methods_assigned_to_users (user_id, name) VALUES (:user_id, :name);';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    public function delete($payment_method_id) {

        $sql = 'DELETE FROM payment_methods_assigned_to_users WHERE id = :payment_method_id;';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':payment_method_id', $payment_method_id, PDO::PARAM_INT);

        return $stmt->execute() != false;
    }

    public static function deleteBasedOnUserId($user_id) {

        $sql = 'DELETE FROM payment_methods_assigned_to_users WHERE user_id = :user_id;';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        return $stmt->execute() != false;
    }
}