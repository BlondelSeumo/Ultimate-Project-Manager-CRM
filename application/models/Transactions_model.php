<?php

/**
 * Description of Project_Model
 *
 * @author NaYeM
 */
class Transactions_Model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    public function get_deposit($filterBy = null, $type = null)
    {
        $deposits = array();
        $all_deposits = array_reverse($this->get_permission('tbl_transactions', array('type' => 'Income')));
        if (empty($filterBy)) {
            return $all_deposits;
        } else {
            foreach ($all_deposits as $v_deposits) {
                if ($type == 'by_account' && $v_deposits->account_id == $filterBy) {
                    array_push($deposits, $v_deposits);
                }
                if ($type == 'by_category' && $v_deposits->category_id == $filterBy) {
                    array_push($deposits, $v_deposits);
                }
            }
        }
        return $deposits;
    }

    public function get_expense($filterBy = null, $type = null)
    {
        $expense = array();
        $all_expense = array_reverse($this->get_permission('tbl_transactions', array('type' => 'Expense')));
        if (empty($filterBy)) {
            return $all_expense;
        } else {
            foreach ($all_expense as $v_expense) {
                if ($type == 'by_account' && $v_expense->account_id == $filterBy) {
                    array_push($expense, $v_expense);
                }
                if ($type == 'by_category' && $v_expense->category_id == $filterBy) {
                    array_push($expense, $v_expense);
                }
            }
        }
        return $expense;
    }


    public function get_transfer($filterBy = null, $type = null)
    {
        $transfer = array();
        $all_transfer = array_reverse($this->get_permission('tbl_transfer'));
        if (empty($filterBy)) {
            return $all_transfer;
        } else {
            foreach ($all_transfer as $v_transfer) {
                if ($type == 'to_account' && $v_transfer->to_account_id == $filterBy) {
                    array_push($transfer, $v_transfer);
                } elseif ($type == 'from_account' && $v_transfer->from_account_id == $filterBy) {
                    array_push($transfer, $v_transfer);
                }
            }
        }
        return $transfer;
    }

    public function get_transactions_report($filterBy = null)
    {
        $transactions_report = array();
        $all_transactions_report = array_reverse($this->get_permission('tbl_transactions'));
        if (empty($filterBy)) {
            return $all_transactions_report;
        } else {
            foreach ($all_transactions_report as $v_transactions_report) {
                if ($v_transactions_report->account_id == $filterBy) {
                    array_push($transactions_report, $v_transactions_report);
                }
            }
        }
        return $transactions_report;
    }
}
