<?php


namespace App\Controllers;

use App\Auth;
use App\Flash;
use App\Models\IncomeCategoryAssignedToUser;
use Core\View;

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
    }

    public function indexAction() {

        View::renderTemplate('Settings/index.html', [
            'user' => $this->user,
            'income_categories' => IncomeCategoryAssignedToUser::getAll()
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



}