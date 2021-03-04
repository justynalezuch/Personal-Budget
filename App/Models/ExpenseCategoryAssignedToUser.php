<?php


namespace App\Models;
use App\Auth;
use PDO;


class ExpenseCategoryAssignedToUser extends \Core\Model
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

        // Monthly limit
        if (isset($this->monthly_limit) && $this->monthly_limit != null) {
            if (! preg_match("/^\d{1,15}(\.\d{0,2})?$/", $this->monthly_limit)) {
                $this->errors[] = 'Podaj poprawną wartość miesięcznego limitu dla kategorii.';
            }
        }
    }

    public function getAll() {

        $sql = 'SELECT id, name, monthly_limit FROM expenses_category_assigned_to_users WHERE user_id=:logged_user_id;';

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
                    return true;
                }
            }
        }
        return false;
    }

    public function update($data) {

        $this->id = $data['category_id'];
        $this->name = $data['category_name'];
        $this->monthly_limit = isset($data['monthly_limit']) && $data['monthly_limit'] != '' ?  $data['monthly_limit'] : null;

        $this->validate();

        if(empty($this->errors)) {

            $sql = 'UPDATE expenses_category_assigned_to_users SET name = :name, monthly_limit = :monthly_limit WHERE id = :id;';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            $stmt->bindValue(':monthly_limit', $this->monthly_limit, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    public function save($data) {

        $this->name = $data['category_name'];

        if(isset($data['monthly_limit'])  && $data['monthly_limit'] != '') {
            $this->monthly_limit = $data['monthly_limit'];
        }

        $this->validate();

        if(empty($this->errors)) {

            $sql = 'INSERT INTO expenses_category_assigned_to_users (user_id, name';
              if(isset($this->monthly_limit)) {
                  $sql .= ', monthly_limit';
              }

            $sql .= ') VALUES (:user_id, :name';

            if(isset($this->monthly_limit)) {
                $sql .= ', :monthly_limit';
            }

            $sql .= ');';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            if(isset($this->monthly_limit)) {
                $stmt->bindValue(':monthly_limit', $this->monthly_limit, PDO::PARAM_STR);
            }

            return $stmt->execute();
        }

        return false;
    }

    public function getExpenseCategoryLimit($category_id) {

        $sql = 'SELECT monthly_limit FROM expenses_category_assigned_to_users WHERE id = :category_id;';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public function delete($category_id) {

        $sql = 'DELETE FROM expenses_category_assigned_to_users WHERE id = :category_id;';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function deleteBasedOnUserId($user_id) {

        $sql = 'DELETE FROM expenses_category_assigned_to_users WHERE user_id = :user_id;';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        if($stmt->execute()){
            return true;
        }
        return false;
    }
}