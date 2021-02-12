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
        if($this->categoryExists($this->name)) {
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

    public function categoryExists($category) {

        $user_categories = $this->getAll();

        foreach ($user_categories as $item) {
            if(strtolower($category) == strtolower($item['name'])) {
                return true;
            }
        }
        return false;
    }

    public function updateCategory($data) {

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

            return $stmt->execute();
        }

        return false;
    }



}