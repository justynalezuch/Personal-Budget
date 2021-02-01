<?php


namespace App\Models;


use App\Auth;
use PDO;

class Balance extends \Core\Model
{

    public function __construct($startDate, $endDate) {
        $this->first_date = $startDate;
        $this->second_date = $endDate;
    }

    public function getExpenses() {

        $sql = 'SELECT sum(amount) AS sum, expenses_category_assigned_to_users.name AS category_name 
            FROM expenses 
            INNER JOIN expenses_category_assigned_to_users on expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id 
            WHERE (expenses.user_id = :user_id AND date_of_expense >= :first_date AND date_of_expense <= :second_date)
            GROUP BY expenses.expense_category_assigned_to_user_id 
            ORDER BY sum DESC;
            ';

        $stmt = static::prepareDatabaseStatement($sql);
//        $stmt->setFetchMode(PDO::FETCH_CLASS, '\App\Models\Expense');
        $stmt->execute();

//        return $stmt->fetch();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getIncomes() {

        $sql = 'SELECT sum(amount) AS sum, incomes_category_assigned_to_users.name AS category_name 
            FROM incomes 
            INNER JOIN incomes_category_assigned_to_users on incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id 
            WHERE (incomes.user_id = :user_id AND date_of_income >= :first_date AND date_of_income <= :second_date)
            GROUP BY incomes.income_category_assigned_to_user_id 
            ORDER BY sum DESC;
            ';

        $stmt = static::prepareDatabaseStatement($sql);
//        $stmt->setFetchMode(PDO::FETCH_CLASS, '\App\Models\Income');
        $stmt->execute();

//        return $stmt->fetch();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function getSumOfExpenses() {

        $sql = 'SELECT sum(amount)
            FROM expenses
            WHERE (user_id = :user_id AND date_of_expense >= :first_date AND date_of_expense <= :second_date)';

        $stmt = static::prepareDatabaseStatement($sql);

        if($stmt->execute()) {
            $sumOfExpenses = (int)$stmt->fetch(PDO::FETCH_ASSOC)['sum(amount)'];
        }
        return $sumOfExpenses;
    }

    protected function getSumOfIncomes() {

        $sql = 'SELECT sum(amount)
            FROM incomes
            WHERE (user_id = :user_id AND date_of_income >= :first_date AND date_of_income <= :second_date)';

        $stmt = static::prepareDatabaseStatement($sql);

        if($stmt->execute()) {
            $sumOfIncomes = (int)$stmt->fetch(PDO::FETCH_ASSOC)['sum(amount)'];
        }
        return $sumOfIncomes;
    }

    public function prepareDatabaseStatement($sql) {

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id',  Auth::getUser()->id, PDO::PARAM_INT);
        $stmt->bindValue(':first_date', $this->first_date, PDO::PARAM_STR);
        $stmt->bindValue(':second_date', $this->second_date, PDO::PARAM_STR);

        return $stmt;
    }

    public function getBalanceSummary() {

        $sumOfIncomes = $this->getSumOfIncomes();
        $sumOfExpenses = $this->getSumOfExpenses();

        return $sumOfIncomes - $sumOfExpenses;
    }
}