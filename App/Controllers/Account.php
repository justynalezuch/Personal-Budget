<?php

namespace App\Controllers;

use \App\Models\User;
use App\Models\Income;
use App\Models\Expense;
use App\Models\IncomeCategoryAssignedToUser;
use App\Models\ExpenseCategoryAssignedToUser;
use App\Models\PaymentMethodAssignedToUser;


/**
 * Account controller
 *
 */
class Account extends \Core\Controller
{
    /**
     * Validate if email is available - AJAX
     *
     * @return void
     */
    public function validateEmailAction()
    {
        $is_valid = ! User::emailExists($_GET['email'], $_GET['ignore_id'] ?? null);

        header('Content-Type: application/json');
        echo json_encode($is_valid);
    }

    public function validateIncomeCategoryAction()
    {
        $income_categories = new IncomeCategoryAssignedToUser();

        $is_valid = ! $income_categories->categoryExists($_GET['category_name'], $_GET['ignore_id'] ?? null);

        header('Content-Type: application/json');
        echo json_encode($is_valid);
    }

    public function findIncomeByCategoryAction(){

        echo (json_encode(Income::findByCategory($_GET['category_id'])));
    }

    public function validateExpenseCategoryAction()
    {
        $expense_categories = new ExpenseCategoryAssignedToUser();

        $is_valid = ! $expense_categories->categoryExists($_GET['category_name'], $_GET['ignore_id'] ?? null);

        header('Content-Type: application/json');
        echo json_encode($is_valid);
    }

    /**
     * For warning before delete expense category
     */
    public function findExpenseByCategoryAction(){

        echo (json_encode(Expense::findByCategory($_GET['category_id'])));
    }

    /**
     * For warning before delete payment method
     */
    public function findExpenseByPaymentMethodAction(){

       echo (json_encode(Expense::findByPaymentMethod($_POST['payment_method_id'])));
    }

    public function validatePaymentMethodAction()
    {
        $expense_categories = new PaymentMethodAssignedToUser();

        $is_valid = ! $expense_categories->methodExists($_GET['payment_method_name'], $_GET['ignore_id'] ?? null);

        header('Content-Type: application/json');
        echo json_encode($is_valid);
    }

    public function checkExpenseCategoryLimitAction(){

        $amount =  $_POST['amount'];
        $category_id =  $_POST['category_id'];

        $expense_categories = new ExpenseCategoryAssignedToUser();

        $montly_limit = $expense_categories->getExpenseCategoryLimit($category_id);
        $expenses = Expense::findByCategoryAndPeriod($category_id);

        if($montly_limit) {

            $to_spend = number_format($montly_limit - $expenses, 2, '.', '');

            $data = [
                'expenses' => $expenses != '' ? $expenses : '0',
                'montly_limit' => $montly_limit,
                'to_spend' => $to_spend,
                'status' => $to_spend < $amount ? 'danger' : 'success',
                'total_amount' => number_format($expenses + $amount, 2, '.', '')
            ];

            echo json_encode($data);
        }
    }

}