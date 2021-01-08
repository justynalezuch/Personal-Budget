<?php

namespace App\Controllers;

class Login extends \Core\Controller
{

    public function indexAction(){
        
        echo htmlspecialchars(print_r($this->route_params, true));
    }

}