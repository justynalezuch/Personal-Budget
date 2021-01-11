<?php

namespace App\Controllers;
use Core\View;

class Login extends \Core\Controller
{

    public function indexAction(){

        echo htmlspecialchars(print_r($this->route_params, true));
    }

    public function newAction(){

        View::renderTemplate('Login/new.html');
    }



}