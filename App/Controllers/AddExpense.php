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

        $incomeCategories = static::getIncomeCategories();

        View::renderTemplate('Income/new.html', [
            'income_categories' => $incomeCategories
        ]);
    }

    public function createAction() {

        $income = new Income($_POST);

        if ($income->save()) {

            Flash::addMessage('Poprawnie dodałeś przychód.');
            $this->redirect('/add-income/new');

        } else {
            $incomeCategories = static::getIncomeCategories();

            View::renderTemplate('Income/new.html', [
                'income_categories' => $incomeCategories,
                'income' => $income,
            ]);
        }

    }

    public function successAction() {
        echo 'success';
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

    public static function getIncomeCategories() {

        $incomeCategories = IncomeCategoryAssignedToUser::getAll();

        foreach ($incomeCategories as $key => $category) {
            $incomeCategories[$key]['slug'] = static::createSlugFromString($category['name']);
        }

        return $incomeCategories;
    }

}