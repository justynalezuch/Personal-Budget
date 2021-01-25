<?php

namespace App\Controllers;
use App\Auth;
use App\Flash;
use Core\View;
use App\Models\User;

class Login extends \Core\Controller
{

    /**
     * Show the login page
     *
     * @return void;
     */
    public function newAction() {
        View::renderTemplate('Login/new.html');
    }

    public function createAction() {

        $this->requireForm($_POST);

        $user = User::authenticate($_POST['email'], $_POST['password']);
        if($user) {

            Auth::login($user);
            Flash::addMessage('Zalogowałeś się poprawne.');
            $this->redirect(Auth::getReturnToPage());
        } else {

            Flash::addMessage('Logowanie nie powiodło się, spróbuj ponownie.');
            View::renderTemplate('Login/new.html', [
                'email' => $_POST['email'],
                'message' => "Nieprawidłowy email lub hasło."
            ]);
        }
    }

    /**
     * Log out user, redirect to new request - to start new session and show logout message.
     */
    public function destroyAction() {

        Auth::logout();
        $this->redirect('/login/show-logout-message');
    }

    /**
     * Show a "logged out" flash message.
     */
    public function showLogoutMessageAction() {
        Flash::addMessage('Wylogowałeś się poprawnie.');
        $this->redirect('/');
    }








    }