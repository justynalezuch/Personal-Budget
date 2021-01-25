<?php


namespace App\Controllers;
use Core\View;

class Budget extends \Core\Controller
{
    public function addIncomeAction() {
        View::renderTemplate('Income/new.html');
    }

}