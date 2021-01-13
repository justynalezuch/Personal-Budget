<?php


namespace App\Controllers;


use App\Models\User;
use Core\View;

class Signup extends \Core\Controller
{
    /**
     * Singup form view
     */
    public function newAction() {

        View::renderTemplate('Signup/new.html');
    }


    /**
     * Sign up new user
     *
     */
    public function createAction()
    {
        $user = new User($_POST);

        if($user->save()) {

            View::renderTemplate('Signup/success.html');

        } else {
            var_dump($user->errors);
//            Vie

        }
    }

}