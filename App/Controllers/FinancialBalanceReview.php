<?php


namespace App\Controllers;


use App\Flash;
use App\Models\Balance;
use Core\View;

class FinancialBalanceReview extends Authenticated
{
    public $start_date = '';
    public $end_date = '';

    public function indexAction(){

        $period = isset($this->route_params['period']) ? $this->route_params['period'] : 'current-month';

        if($period == 'custom' && ( ! isset($_POST['start-date']) || ! isset($_POST['end-date']))) {

            if(! empty($_POST)) {
                Flash::addMessage('Podaj datę początkową oraz końcową', Flash::WARNING);
            }
            $this->redirect('/financial-balance');
        }

        switch ($period) {
            case 'current-month':
                $startDate = date('Y-m-01');
                $endDate = date('Y-m-d');
                break;
            case 'last-month':
                $startDate = date('Y-m-d', strtotime('first day of last month'));
                $endDate = date('Y-m-d', strtotime('last day of last month'));
                break;
            case 'current-year':
                $startDate = date('Y-01-01');
                $endDate = date('Y-m-d');
                break;
            case 'custom':
                if($this->customDatesValidation()) {
                    $startDate = $_POST['start-date'];
                    $endDate = $_POST['end-date'];
                }
                $customStartDate = isset($startDate) ? $startDate : $_POST['start-date'];
                $customEndDate = isset($endDate) ? $endDate : $_POST['end-date'];
                break;

            default:
                $this->redirect('/financial-balance');
        }

        $balance = new Balance($startDate, $endDate);
        $expenses = $balance->getExpenses();
        $incomes = $balance->getIncomes();
        $summary = $balance->getBalanceSummary();

//        echo '<pre>';
//        var_dump($expenses);
////        var_dump($incomes);
//
//        var_dump(json_encode((object)$expenses));
//        exit;


        View::renderTemplate('Balance/index.html', [
            'period' => $period,
            'expenses' => $expenses,
            'incomes' => $incomes,
            'summary' => $summary,
            'custom_start_date' => $startDate,
            'custom_end_date' => $endDate
        ]);

    }

    private function customDatesValidation() {

        if(isset($startDate) && isset($endDate)) {

            if($startDate != '' && $endDate != '') {

                if(!preg_match("/^\d{4}-\d{2}-\d{2}$/" , $startDate) || !preg_match("/^\d{4}-\d{2}-\d{2}$/" , $endDate)) {

                    Flash::addMessage('Wprowadź daty w poprawnym formacie.', Flash::WARNING);
                    return false;
                }

                if($startDate > $endDate) {
                    Flash::addMessage('Wprowadź daty w kolejności chronologicznej.', Flash::WARNING);
                    return false;
                }
                return true;
            }

            Flash::addMessage('Podaj datę początkową oraz końcową', Flash::WARNING);
        }


        return false;
    }

}