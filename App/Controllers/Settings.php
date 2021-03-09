<?php


namespace App\Controllers;

use App\Auth;
use App\Flash;
use Core\View;

use App\Models\Income;
use App\Models\Expense;
use App\Models\IncomeCategoryAssignedToUser;
use App\Models\ExpenseCategoryAssignedToUser;
use App\Models\PaymentMethodAssignedToUser;

class Settings extends Authenticated
{
    /**
     * Active tabs
     */
    const USER = 'user';
    const INCOME_CATEGORIES = 'income-categories';
    const EXPENSE_CATEGORIES = 'expense-categories';
    const PAYMENT_METHODS = 'payment-methods';

    /**
     * Active tab in settings, set default
     *
     * @var string
     */
    private $active_tab = self::EXPENSE_CATEGORIES;

    public function before()
    {
        parent::before();
        $this->user = Auth::getUser();
        $this->income_categories = new IncomeCategoryAssignedToUser();
        $this->expense_categories = new ExpenseCategoryAssignedToUser();
        $this->payment_methods = new PaymentMethodAssignedToUser();
    }

    private function renderTemplate() {

        View::renderTemplate('Settings/index.html', [
            'user' => $this->user,
            'income_categories' => $this->income_categories,
            'expense_categories' => $this->expense_categories,
            'payment_methods' => $this->payment_methods,
            'active_tab' => $this->active_tab
        ]);
    }

    public function indexAction() {

        $this->renderTemplate();
    }

    public function userUpdateAction() {

        if($this->user->updateProfile($_POST)) {

            Flash::addMessage('Zmiany w profilu użytkownika zostały poprawnie zapisane.');
            $this->redirect('/settings');

        } else {

            Flash::addMessage('Coś poszło nie tak... Spróbuj ponownie.', Flash::WARNING);
            $this->active_tab = self::USER;
            $this->renderTemplate();
        }

    }

    public function userDeleteAction() {

        $user_id = $_POST['user_id'];

        if ( $this->user->delete($user_id) &&
            Expense::delete('user_id', $user_id) &&
            Income::delete('user_id', $user_id) &&
            IncomeCategoryAssignedToUser::deleteBasedOnUserId($user_id) &&
            ExpenseCategoryAssignedToUser::deleteBasedOnUserId($user_id) &&
            PaymentMethodAssignedToUser::deleteBasedOnUserId($user_id) )
        {
            Auth::logout();
            $this->redirect('/settings/show-deleted-account-message');

        } else {

            Flash::addMessage('Coś poszło nie tak... Spróbuj ponownie.', Flash::WARNING);
            $this->active_tab = self::USER;
            $this->renderTemplate();
        }
    }

    /**
     * Show a "deleted account" flash message.
     */
    public function showDeletedAccountMessage() {

        Flash::addMessage('Twoje konto zostało usunięte');
        $this->redirect('/');
    }

    public function userDeleteIncomesAndExpensesAction() {

        if (Expense::delete('user_id', $_POST['user_id']) &&
            Income::delete('user_id', $_POST['user_id']) )
        {

            Flash::addMessage('Przychody oraz wydatki zostały poprawnie usunięte.');
            $this->redirect('/settings');

        } else {

            Flash::addMessage('Coś poszło nie tak... Spróbuj ponownie.', Flash::WARNING);
            $this->active_tab = self::USER;
            $this->renderTemplate();
        }
    }

    public function incomeCategoryUpdateAction() {

        if($this->income_categories->update($_POST)) {

            Flash::addMessage('Kategoria została poprawnie edytowana.');
            $this->redirect('/settings');

        } else {

            Flash::addMessage('Coś poszło nie tak... Spróbuj ponownie.', Flash::WARNING);
            $this->active_tab = self::INCOME_CATEGORIES;
            $this->renderTemplate();
        }
    }

    public function incomeCategoryNewAction() {

        if($this->income_categories->save($_POST)) {

            Flash::addMessage('Kategoria została poprawnie dodana.');
            $this->redirect('/settings');

        } else {

            Flash::addMessage('Coś poszło nie tak... Spróbuj ponownie.', Flash::WARNING);
            $this->active_tab = self::INCOME_CATEGORIES;
            $this->renderTemplate();
        }
    }

    public function incomeCategoryDeleteAction() {

        if($this->income_categories->delete($_POST['category_id']) )
        {
            if(Income::findByCategory($_POST['category_id'])) {

                $new_category_id = $this->income_categories->categoryExists('Inne') ? $this->income_categories->categoryExists('Inne') : $this->income_categories->save(['category_name' => 'Inne']);
                Income::update($_POST['category_id'], $new_category_id);
            }

            Flash::addMessage('Kategoria została usunięta.');
            $this->redirect('/settings');

        } else {

            Flash::addMessage('Coś poszło nie tak... Spróbuj ponownie.', Flash::WARNING);
            $this->active_tab = self::INCOME_CATEGORIES;
            $this->renderTemplate();
        }
    }

    public function expenseCategoryUpdateAction() {

        if($this->expense_categories->update($_POST)) {

            Flash::addMessage('Kategoria została poprawnie edytowana.');
            $this->redirect('/settings');

        } else {

            Flash::addMessage('Coś poszło nie tak... Spróbuj ponownie.', Flash::WARNING);
            $this->active_tab = self::EXPENSE_CATEGORIES;
            $this->renderTemplate();
        }
    }

    public function expenseCategoryNewAction() {

        if($this->expense_categories->save($_POST)) {

            Flash::addMessage('Kategoria została poprawnie dodana.');
            $this->redirect('/settings');

        } else {

            Flash::addMessage('Coś poszło nie tak... Spróbuj ponownie.', Flash::WARNING);
            $this->active_tab = self::EXPENSE_CATEGORIES;
            $this->renderTemplate();
        }
    }

    public function expenseCategoryDeleteAction() {

        if ($this->expense_categories->delete($_POST['category_id']))
        {

            if(Expense::findByCategory($_POST['category_id'])) {

                $new_category_id = $this->expense_categories->categoryExists('Inne') ? $this->expense_categories->categoryExists('Inne') : $this->expense_categories->save(['category_name' => 'Inne']);
                Expense::update($_POST['category_id'], $new_category_id);
            }

            Flash::addMessage('Kategoria została usunięta.');
            $this->redirect('/settings');

        } else {

            Flash::addMessage('Coś poszło nie tak... Spróbuj ponownie.', Flash::WARNING);
            $this->active_tab = self::EXPENSE_CATEGORIES;
            $this->renderTemplate();
        }
    }


    public function paymentMethodUpdateAction() {

        if($this->payment_methods->update($_POST)) {

            Flash::addMessage('Metoda płatności została poprawnie edytowana.');
            $this->redirect('/settings');

        } else {

            Flash::addMessage('Coś poszło nie tak... Spróbuj ponownie.', Flash::WARNING);
            $this->active_tab = self::PAYMENT_METHODS;
            $this->renderTemplate();
        }
    }

    public function paymentMethodNewAction() {

        if($this->payment_methods->save($_POST)) {

            Flash::addMessage('Metoda płatności została poprawnie dodana.');
            $this->redirect('/settings');

        } else {

            Flash::addMessage('Coś poszło nie tak... Spróbuj ponownie.', Flash::WARNING);
            $this->active_tab = self::PAYMENT_METHODS;
            $this->renderTemplate();
        }
    }

    public function paymentMethodDeleteAction() {

        if(Expense::delete('payment_method_assigned_to_user_id', $_POST['payment_method_id']) &&
            $this->payment_methods->delete($_POST['payment_method_id']))
        {

            Flash::addMessage('Metoda płatności została usunięta.');
            $this->redirect('/settings');

        } else {

            Flash::addMessage('Coś poszło nie tak... Spróbuj ponownie.', Flash::WARNING);
            $this->active_tab = self::PAYMENT_METHODS;
            $this->renderTemplate();
        }
    }
}