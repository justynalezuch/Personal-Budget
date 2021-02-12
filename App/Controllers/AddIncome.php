<?php


namespace App\Controllers;
use App\Auth;
use App\Flash;
use App\Models\IncomeCategoryAssignedToUser;
use App\Models\Income;
use Core\View;

class AddIncome extends Authenticated
{
    public function before()
    {
        parent::before();
        $this->income_categories = new IncomeCategoryAssignedToUser();

    }
    public function newAction() {

        $incomeCategories = $this->getIncomeCategories();

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
            $incomeCategories = $this->getIncomeCategories();

            View::renderTemplate('Income/new.html', [
                'income_categories' => $incomeCategories,
                'income' => $income,
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

    public function getIncomeCategories() {

        $incomeCategories = $this->income_categories->getAll();

        foreach ($incomeCategories as $key => $category) {
            $incomeCategories[$key]['slug'] = static::createSlugFromString($category['name']);
        }

        return $incomeCategories;
    }

}