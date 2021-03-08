<?php


namespace App\Models;
use App\Auth;
use PDO;
use App\Models\IncomeCategoryAssignedToUser;


class Income extends \Core\Model
{
    /**
     * Error messages
     * @var array
     */
    public $errors = [];

    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function validate() {

        if (! preg_match("/^\d{1,8}(\.\d{0,2})?$/", $this->amount)) {
            $this->errors[] = 'Kwota przychodu: Podaj poprawną wartość - maksymalnie 10 cyfr w tym 2 po przecinku.';
        }

        if(! preg_match("/^\d{4}-\d{2}-\d{2}$/" , $this->date)) {
            $this->errors[] = "Podaj datę w prawidłowym formacie.";
        }

        if(empty($this->category)) {
            $this->errors[] = "Podaj kategorię przychodu.";
        }
        if(empty($this->comment)) {
            $this->comment = NULL;
        }
    }

    public function save() {

        $this->validate();

        if(empty($this->errors)) {

            $sql = 'INSERT INTO incomes VALUES (
                            NULL,
                            :user_id,
                            :income_category_assigned_to_user_id,
                            :amount,
                            :date_of_income,
                            :income_comment)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id',  Auth::getUser()->id, PDO::PARAM_INT);
            $stmt->bindValue(':income_category_assigned_to_user_id', $this->category, PDO::PARAM_INT);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date_of_income', $this->date, PDO::PARAM_STR);
            $stmt->bindValue(':income_comment', $this->comment, PDO::PARAM_STR);

            return $stmt->execute();
        }
        return false;
    }

    public static function findByCategory($category_id) {

        $sql = 'SELECT * FROM incomes WHERE income_category_assigned_to_user_id = :category_id  ORDER BY date_of_income DESC;';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function incomeExists($category_id) {

        $income = static::findByCategory($category_id);

        if($income) {
            return true;
        }

        return false;
    }

    public static function update($deleted_category_id, $new_category_id) {

        $sql = 'UPDATE incomes SET income_category_assigned_to_user_id = :new_category_id WHERE income_category_assigned_to_user_id = :deleted_category_id;';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':deleted_category_id', $deleted_category_id, PDO::PARAM_INT);
        $stmt->bindValue(':new_category_id', $new_category_id, PDO::PARAM_INT);

        return $stmt->execute() != false;

    }

    public static function delete($record_name, $id) {

        $sql = "DELETE FROM incomes WHERE {$record_name}  = :id;";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute() != false;
    }

}