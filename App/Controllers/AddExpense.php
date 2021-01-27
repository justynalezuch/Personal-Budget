<?php


namespace App\Controllers;
use App\Auth;
use App\Flash;
use App\Models\ExpenseCategoryAssignedToUser;
use App\Models\PaymentMethodAssignedToUser;
use App\Models\Expense;
use Core\View;

class AddExpense extends Authenticated
{
    public function newAction() {

        $expenseCategories = static::getExpenseCategories();
        $paymentMethods = static::getPaymentMethods();

        View::renderTemplate('Expense/new.html', [
            'expense_categories' => $expenseCategories,
            'payment_methods' => $paymentMethods,
        ]);
    }

    public function createAction() {

        $expense = new Expense($_POST);

        if ($expense->save()) {

            Flash::addMessage('Poprawnie dodałeś wydatek.');
            $this->redirect('/add-expense/new');

        } else {
            $expenseCategories = static::getExpenseCategories();
            $paymentMethods = static::getPaymentMethods();

            View::renderTemplate('Expense/new.html', [
                'expense_categories' => $expenseCategories,
                'payment_methods' => $paymentMethods,
                'expense' => $expense,
            ]);
        }

    }

    /**
     * Create lowercase slug
     *
     * @param $string
     * @return string
     */
    public static function createSlugFromString($string) {
        $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
        return strtolower($slug);
    }

    public static function getExpenseCategories() {

        $expenseCategories = ExpenseCategoryAssignedToUser::getAll();

        foreach ($expenseCategories as $key => $category) {
            $expenseCategories[$key]['slug'] = static::createSlugFromString($category['name']);
        }

        return $expenseCategories;
    }

    public static function getPaymentMethods() {

        $paymentMethods = PaymentMethodAssignedToUser::getAll();

//        foreach ($paymentMethods as $key => $method) {
//            $paymentMethods[$key]['slug'] = static::createSlugFromString($method['name']);
//        }

        return $paymentMethods;
    }



}