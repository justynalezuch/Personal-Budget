<?php


namespace App\Controllers;

use App\Auth;
use App\Flash;
use Core\View;

class Settings extends Authenticated
{
    public function indexAction() {
        View::renderTemplate('Settings/index.html', [
            'user' => Auth::getUser()
        ]);
    }

    public function userUpdateAction() {

        $user = Auth::getUser();

        if($user->updateProfile($_POST)) {

            Flash::addMessage('Zmiany w profilu użytkownika zostały poprawnie zapisane.');
            $this->redirect('/settings');

        } else {

//            Flash::addMessage('error');

            View::renderTemplate('Settings/index.html', [
                'user' => $user
            ]);
        }
    }



}