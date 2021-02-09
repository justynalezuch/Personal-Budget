<?php


namespace App\Controllers;

use App\Auth;
use App\Flash;
use Core\View;

class Settings extends Authenticated
{
    public function before()
    {
        parent::before();
        $this->user = Auth::getUser();
    }

    public function indexAction() {

        $active =  isset($this->route_params['active']) ? $this->route_params['active'] : 'income-categories';

        View::renderTemplate('Settings/index.html', [
            'user' => $this->user,
            'active_tab' => $active
        ]);
    }

    public function userUpdateAction() {

        if($this->user->updateProfile($_POST)) {

            Flash::addMessage('Zmiany w profilu użytkownika zostały poprawnie zapisane.');
            
//            $this->redirect('/settings');

            View::renderTemplate('Settings/index.html', [
                'user' => $this->user,
                'active_tab' => 'user'
            ]);


        } else {

            Flash::addMessage('Coś poszło nie tak... Spróbuj ponownie.', Flash::WARNING);

            View::renderTemplate('Settings/index.html', [
                'user' => $this->user,
                'active_tab' => 'user'
            ]);
        }
    }

}