<?php

namespace App\Controllers;
use Core\View;

class Home extends \Core\Controller
{
    public function indexAction()
    {
        if(isset($_SESSION['user_id'])) {
            View::renderTemplate('Home/menu.html');
        } else {
            View::renderTemplate('Home/index.html');
        }
    }

}