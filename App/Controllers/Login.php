<?php

namespace App\Controllers;
use App\Auth;
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
            $this->redirect(Auth::getReturnToPage());
        } else {
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
        $this->redirect('/');
    }








    }