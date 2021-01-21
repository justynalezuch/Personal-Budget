<?php

namespace App\Controllers;

use \App\Models\User;

/**
 * Account controller
 *
 */
class Account extends \Core\Controller
{
    /**
     * Validate if email is available - AJAX
     *
     * @return void
     */
    public function validateEmailAction()
    {
        $is_valid = ! User::emailExists($_GET['email']);

        header('Content-Type: application/json');
        echo json_encode($is_valid);
    }
}