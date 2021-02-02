<?php


namespace App\Controllers;

use App\Flash;
use App\Models\Balance;
use Core\View;

class FinancialBalanceReview extends Authenticated
{
    public $custom_start_date = null;
    public $custom_end_date = null;

    public function indexAction(){

        $period = isset($this->route_params['period']) ? $this->route_params['period'] : 'current-month';

        switch ($period) {

            case 'custom':

                if(empty($_POST) ) {
                    $this->redirect('/financial-balance');
                }
                else if($this->customDatesValidation($_POST['start_date'], $_POST['end_date'])) {

                    $startDate = $_POST['start_date'];
                    $endDate = $_POST['end_date'];

                    $this->custom_start_date = $_POST['start_date'];
                    $this->custom_end_date = $_POST['end_date'];
                }
                else {

                    // Form values
//                    $this->custom_start_date = $_POST['start_date'];
//                    $this->custom_end_date = $_POST['end_date'];
//                    // Set default period - current month
//                    $startDate = date('Y-m-01');
//                    $endDate = date('Y-m-d');
//                    $period = 'current-month';

                    $this->redirect('/financial-balance');
                }

                break;
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

            default:
                $this->redirect('/financial-balance');
        }

        $balance = new Balance($startDate, $endDate);
        $expenses = $balance->getExpenses();
        $incomes = $balance->getIncomes();
        $summary = $balance->getBalanceSummary();


        View::renderTemplate('Balance/index.html', [
            'period' => $period,
            'expenses' => $expenses,
            'incomes' => $incomes,
            'summary' => $summary,
            'custom_start_date' => $this->custom_start_date,
            'custom_end_date' => $this->custom_end_date
        ]);

    }

    private function customDatesValidation($startDate, $endDate) {

        if(isset($startDate) && isset($endDate)) {

            if($startDate != '' && $endDate != '') {

                if(!preg_match("/^\d{4}-\d{2}-\d{2}$/" , $startDate) || !preg_match("/^\d{4}-\d{2}-\d{2}$/" , $endDate)) {

                    Flash::addMessage('Okres niestandardowy: Wprowadź daty w poprawnym formacie.', Flash::WARNING);
                    return false;
                }

                if($startDate > $endDate) {
                    Flash::addMessage('Okres niestandardowy: Wprowadź daty w kolejności chronologicznej.', Flash::WARNING);
                    $this->custom_start_date = $startDate;
                    $this->custom_end_date = $endDate;
                    return false;
                }

                return true;
            }

            Flash::addMessage('Okres niestandardowy: Podaj datę początkową oraz końcową', Flash::WARNING);
            return false;
        }


        return false;
    }

}