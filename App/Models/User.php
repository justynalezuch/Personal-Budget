<?php


namespace App\Models;
use PDO;


class User extends \Core\Model
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

    public function save() {

        $this->validate();

        if(empty($this->errors)) {
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $sql = 'INSERT INTO users (name, email, password_hash)
            VALUES (:name, :email, :password_hash);';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);

            if($stmt->execute()) {
                static::assignCategories($db);
                return true;
            }
        }
        return false;
    }
    public function validate() {

        // Name
        if ($this->name == '') {
            $this->errors[] = 'Podaj nazwę użytkownika';
        }

        if (ctype_alnum($this->name) == false) {
            $this->errors[] = "Nazwa użytkownika może składać się tylko z liter i cyfr (bez polskich znaków)";
        }

        if (strlen($this->name) < 3 || strlen($this->name) > 50) {
            $this->errors[] = "Nazwa użytkownika powinna być nie krótsza niż 3 znaki i nie dłuższa niż 50 znaków.";
        }

        // Email address
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[] = 'Podaj poprawny adres email.';
        }

        if(static::emailExists($this->email, $this->id)) {
            $this->errors[] = 'Istnieje już konto zarejestrowane na podany adres email.';
        }

        if (strlen($this->email) > 50) {
            $this->errors[] = "Adres email nie może być dłuższy niż 50 znaków.";
        }

        // Password
        if(isset($this->password)) {

            if (strlen($this->password) < 8 || strlen($this->password) > 20) {
                $this->errors[] = "Hasło musi posiadać od 8 do 20 znaków.";
            }

            if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
                $this->errors[] = 'Hasło musi posiadać co najmniej jedną literę.';
            }

            if (preg_match('/.*\d+.*/i', $this->password) == 0) {
                $this->errors[] = 'Hasło musi posiadać co najmniej jedną cyfrę.';
            }
        }
    }


    /**
     * Check if email already exist.
     *
     * @param $email
     * @return bool
     */
    public static function emailExists($email, $ignore_id = null)
    {
        $user =  static::findByEmail($email);

        if($user) {
            if($user->id != $ignore_id) {
                return true;
            }
        }
        return false;
    }

    /**
     * Find user by email.
     *
     * @param $email
     * @return mixed
     */
    public static function findByEmail($email)
    {
        $sql = 'SELECT * FROM users WHERE email = :email;';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Authenticate user
     *
     * @param $email
     * @param $password
     * @return bool
     */
    public static function authenticate($email, $password)
    {
        $user = static::findByEmail($email);

        if($user) {
            if(password_verify($password, $user->password_hash)) {
                return $user;
            }
        }

        return false;
    }

    /**
     * Find user model by ID.
     *
     * @param $id
     * @return mixed
     */
    public static function findByID($id)
    {
        $sql = 'SELECT * FROM users WHERE id = :id;';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }


    /**
     * Assing default categories of payment method, incomes and expenses to new user
     *
     * @param $db
     */
    public static function assignCategories($db) {

        $lastID = $db->lastInsertId();

        $sql = "INSERT INTO payment_methods_assigned_to_users (user_id, name) SELECT :user_id, name FROM payment_methods_default;
                INSERT INTO expenses_category_assigned_to_users (user_id, name) SELECT :user_id, name FROM expenses_category_default;
                INSERT INTO incomes_category_assigned_to_users (user_id, name) SELECT :user_id, name FROM incomes_category_default;";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $lastID, PDO::PARAM_INT);
        $stmt->execute();
    }


    /**
     * Update user data
     *
     * @param $data
     * @return bool
     */
    public function updateProfile($data) {

        $this->name = $data['name'];
        $this->email = $data['email'];

        if($data['password'] != '') {
            $this->password = $data['password'];
        }

        $this->validate();

        if(empty($this->errors)) {

            $sql = 'UPDATE users SET name = :name, email = :email';

            if(isset($this->password)) {
                $sql .= ' , password_hash = :password_hash';
            }
            $sql .= ' WHERE id = :id;';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

            if(isset($this->password)) {

                $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
                $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            }

            return $stmt->execute();
        }

        return false;
    }

}