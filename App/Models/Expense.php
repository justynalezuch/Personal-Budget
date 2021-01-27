<?php


namespace App\Models;
use App\Auth;
use PDO;


class Expense extends \Core\Model
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
            $this->errors[] = 'Kwota wydatku: Podaj poprawną wartość - maksymalnie 10 cyfr w tym 2 po przecinku.';
        }

        if(! preg_match("/^\d{4}-\d{2}-\d{2}$/" , $this->date)) {
            $this->errors[] = "Podaj datę w prawidłowym formacie.";
        }

        if(empty($this->payment_method)) {
            $this->errors[] = "Podaj metodę płatności.";
        }

        if(empty($this->category)) {
            $this->errors[] = "Podaj kategorię wydatku.";
        }
        if(empty($this->comment)) {
            $this->comment = NULL;
        }
    }

    public function save() {

        $this->validate();

        if(empty($this->errors)) {

            $sql = 'INSERT INTO expenses VALUES (
                            NULL,
                            :user_id,
                            :expense_category_assigned_to_user_id, 
                            :payment_method_assigned_to_user_id, 
                            :amount, 
                            :date_of_expense, 
                            :expense_comment)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id',  Auth::getUser()->id, PDO::PARAM_INT);
            $stmt->bindValue(':expense_category_assigned_to_user_id', $this->category, PDO::PARAM_INT);
            $stmt->bindValue(':payment_method_assigned_to_user_id', $this->payment_method, PDO::PARAM_INT);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date_of_expense', $this->date, PDO::PARAM_STR);
            $stmt->bindValue(':expense_comment', $this->comment, PDO::PARAM_STR);

            return $stmt->execute();
        }
        return false;
    }



}