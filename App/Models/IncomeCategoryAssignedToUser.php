<?php


namespace App\Models;
use App\Auth;
use PDO;


class IncomeCategoryAssignedToUser extends \Core\Model
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

        // Category name
        if($this->categoryExists($this->name, $this->id ?? null)) {
            $this->errors[] = 'Istnieje już kategoria o podanej nazwie.';
        }
        if (preg_match('/^[a-zA-Z\s]+$/', $this->name) == 0) {
            $this->errors[] = 'Nazwa kategori może składać się z liter oraz spacji.';
        }
    }

    public function getAll()
    {
        $sql = 'SELECT id, name FROM incomes_category_assigned_to_users WHERE user_id=:logged_user_id;';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':logged_user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function categoryExists($category, $ignore_id = null) {

        $user_categories = $this->getAll();

        foreach ($user_categories as $item) {
            if(strtolower($category) == strtolower($item['name'])) {
                if($item['id'] != $ignore_id) {
                    return $item['id'];
                }
            }
        }
        return false;
    }

    public function update($data) {

        $this->id = $data['category_id'];
        $this->name = $data['category_name'];

        $this->validate();

        if(empty($this->errors)) {

            $sql = 'UPDATE incomes_category_assigned_to_users SET name = :name WHERE id = :id;';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

            return $stmt->execute();
        }

        return false;
    }

    public function save($data) {

        $this->name = $data['category_name'];

        $this->validate();

        if(empty($this->errors)) {

            $sql = 'INSERT INTO incomes_category_assigned_to_users (user_id, name) VALUES (:user_id, :name);';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);

            $stmt->execute();
            return $db->lastInsertId();
        }

        return false;
    }

    public function delete($category_id) {

        $sql = 'DELETE FROM incomes_category_assigned_to_users WHERE id = :category_id;';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);

        return $stmt->execute() != false;
    }

    public static function deleteBasedOnUserId($user_id) {

        $sql = 'DELETE FROM incomes_category_assigned_to_users WHERE user_id = :user_id;';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        return $stmt->execute() != false;
    }

    /**public function getCategoryId($name) {

        $sql = 'SELECT id FROM incomes_category_assigned_to_users WHERE user_id = :logged_user_id AND LOWER(name) = :name;';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':logged_user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->bindValue(':name', $name);
        $stmt->execute();

        return $stmt->fetchColumn();
    }
     * **/
}