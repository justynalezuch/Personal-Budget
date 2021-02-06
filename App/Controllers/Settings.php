<?php


namespace App\Controllers;

use App\Auth;
use Core\View;

class Settings extends Authenticated
{
    public function indexAction() {
        View::renderTemplate('Settings/index.html', [
            'user' => Auth::getUser()
        ]);
    }

    public function userUpdateAction() {
        echo 'update';
    }



}