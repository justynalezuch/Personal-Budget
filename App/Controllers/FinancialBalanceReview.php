<?php


namespace App\Controllers;

use App\Flash;
use App\Models\Balance;
use Core\View;

class FinancialBalanceReview extends Authenticated
{
    public $start_date = null;
    public $end_date = null;

    public function indexAction(){

        $period = isset($this->route_params['period']) ? $this->route_params['period'] : 'current-month';

        switch ($period) {

            case 'custom':

                if( ! empty($_POST) && $this->customDatesValidation($_POST['start_date'], $_POST['end_date']) ) {
                    $this->setPeriod( $_POST['start_date'],  $_POST['end_date']);

                }
                else {
                    $this->redirect('/financial-balance');
                }
                break;

            case 'current-month':

                $this->setPeriod(date('Y-m-01'), date('Y-m-d'));
                break;

            case 'last-month':

                $this->setPeriod(date('Y-m-d', strtotime('first day of last month')), date('Y-m-d', strtotime('last day of last month')));
                break;

            case 'current-year':

                $this->setPeriod(date('Y-01-01'), date('Y-m-d'));
                break;

            default:
                $this->redirect('/financial-balance');
        }

        $balance = new Balance($this->start_date, $this->end_date);

        View::renderTemplate('Balance/index.html', [
            'period' => $period,
            'expenses' => $balance->getExpenses(),
            'incomes' => $balance->getIncomes(),
            'summary' => $balance->getBalanceSummary(),
            'custom_start_date' => $this->start_date,
            'custom_end_date' => $this->end_date
        ]);

    }

    private function setPeriod($startDate, $endDate) {

        $this->start_date = $startDate;
        $this->end_date = $endDate;
    }

    private function customDatesValidation($startDate, $endDate) {

        if(isset($startDate) && isset($endDate)) {

            if($startDate != '' && $endDate != '') {

                if(!preg_match("/^\d{4}-\d{2}-\d{2}$/" , $startDate) || !preg_match("/^\d{4}-\d{2}-\d{2}$/" , $endDate)) {

                    Flash::addMessage('Wprowadź daty w poprawnym formacie.', Flash::DANGER);
                    return false;
                }

                if($startDate > $endDate) {
                    Flash::addMessage('Wprowadź daty w kolejności chronologicznej.', Flash::DANGER);
                    $this->custom_start_date = $startDate;
                    $this->custom_end_date = $endDate;
                    return false;
                }

                return true;
            }

            Flash::addMessage('Podaj datę początkową oraz końcową', Flash::DANGER);
            return false;
        }


        return false;
    }

}