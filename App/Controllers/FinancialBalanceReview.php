<?php


namespace App\Controllers;


use App\Models\Balance;
use Core\View;

class FinancialBalanceReview extends Authenticated
{
    public function index(){

        $period = isset($this->route_params['period']) ? $this->route_params['period'] : 'current-month';

        $firstDate = date('Y-m-01');
        $secondDate = date('Y-m-d');

        $balance = new Balance($firstDate, $secondDate);
        $expenses = $balance->getExpenses();
        $incomes = $balance->getIncomes();
        $summary = $balance->getBalanceSummary();
//
//        echo '<pre>';
//        var_dump($expenses);
//        var_dump($incomes);
//        exit;

        View::renderTemplate('Balance/index.html', [
            'period' => $period,
            'expenses' => $expenses,
            'incomes' => $incomes,
            'summary' => $summary
        ]);


    }

}