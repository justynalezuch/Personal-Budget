<?php


namespace App\Controllers;

use App\Auth;
use App\Flash;
use App\Models\IncomeCategoryAssignedToUser;
use Core\View;
use App\Models\Income;

class Settings extends Authenticated
{
//    /**
//     * Active tabs
//     */
//    const USER = 'user';
//    const INCOME_CATEGORIES = 'income-categories';
//    const EXPENSE_CATEGORIES = 'expense-categories';
//    const PAYMENT_METHODS = 'payment-methods';

    public function before()
    {
        parent::before();
        $this->user = Auth::getUser();
        $this->income_categories = new IncomeCategoryAssignedToUser();

    }

    public function indexAction() {

        View::renderTemplate('Settings/index.html', [
            'user' => $this->user,
            'income_categories' => $this->income_categories
        ]);
    }

    public function userUpdateAction() {

        if($this->user->updateProfile($_POST)) {

            Flash::addMessage('Zmiany w profilu użytkownika zostały poprawnie zapisane.');
            $this->redirect('/settings');

        } else {

            Flash::addMessage('Coś poszło nie tak... Spróbuj ponownie.', Flash::WARNING);
            View::renderTemplate('Settings/index.html', [
                'user' => $this->user
            ]);
        }
    }


    public function incomeCategoryUpdateAction(){

        if($this->income_categories->updateCategory($_POST)) {

            Flash::addMessage('Kategoria została poprawnie edytowana.');
            $this->redirect('/settings');

        } else {

            Flash::addMessage('Coś poszło nie tak... Spróbuj ponownie.', Flash::WARNING);

            View::renderTemplate('Settings/index.html', [
                'user' => $this->user,
                'income_categories' => $this->income_categories
            ]);
        }
    }

    public function incomeCategoryNewAction(){

        if($this->income_categories->save($_POST)) {

            Flash::addMessage('Kategoria została poprawnie dodana.');
            $this->redirect('/settings');

        } else {

            Flash::addMessage('Coś poszło nie tak... Spróbuj ponownie.', Flash::WARNING);

            View::renderTemplate('Settings/index.html', [
                'user' => $this->user,
                'income_categories' => $this->income_categories
            ]);
        }
    }

    public function incomeCategoryDeleteAction(){

        if(Income::delete($_POST['category_id']) && $this->income_categories->delete($_POST['category_id'])) {

            Flash::addMessage('Kategoria została usunięta.');
            $this->redirect('/settings');
        } else {

            Flash::addMessage('Coś poszło nie tak... Spróbuj ponownie.', Flash::WARNING);

            View::renderTemplate('Settings/index.html', [
                'user' => $this->user,
                'income_categories' => $this->income_categories
            ]);
        }

    }





}