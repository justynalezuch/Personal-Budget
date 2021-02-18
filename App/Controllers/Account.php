<?php

namespace App\Controllers;

use \App\Models\User;
use App\Models\Income;
use App\Models\Expense;
use App\Models\IncomeCategoryAssignedToUser;
use App\Models\ExpenseCategoryAssignedToUser;


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

        $is_valid = ! $income_categories->categoryExists($_GET['category_name']);

        header('Content-Type: application/json');
        echo json_encode($is_valid);
    }

    public function findIncomeByCategoryAction(){

        echo (json_encode(Income::findByCategory($_GET['category_id'])));
    }

    public function validateExpenseCategoryAction()
    {
        $income_categories = new ExpenseCategoryAssignedToUser();

        $is_valid = ! $income_categories->categoryExists($_GET['category_name']);

        header('Content-Type: application/json');
        echo json_encode($is_valid);
    }

    public function findExpenseByCategoryAction(){

        echo (json_encode(Expense::findByCategory($_GET['category_id'])));
    }





}