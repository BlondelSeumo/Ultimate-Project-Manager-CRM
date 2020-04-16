<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of admistrator
 *
 * @author pc mart ltd
 */
class Calendar extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin_model');
        $this->load->model('invoice_model');
        $this->load->model('estimates_model');
    }

    public function index($action = NULL)
    {
        $data['title'] = config_item('company_name');
        $data['dataTables'] = true;
        $data['select_2'] = true;
        $data['datepicker'] = true;
        $data['page'] = lang('calendar');
        if (!empty($action) && $action == 'search') {
            $data['searchType'] = $this->uri->segment(5);

        } else {
            $data['searchType'] = 'all';
        }
        //total expense count
        $this->admin_model->_table_name = "tbl_transactions"; //table name
        $this->admin_model->_order_by = "transactions_id"; // order by 
        $total_income_expense = $this->admin_model->get(); // get result
        $today_income_expense = $this->admin_model->get_by(array('date' => date('Y-m-d'))); // get result

        $today_income = 0;
        $today_expense = 0;
        foreach ($today_income_expense as $t_income_expense) {
            if ($t_income_expense->type == 'Income') {
                $today_income += $t_income_expense->amount;

            } elseif ($t_income_expense->type == 'Expense') {
                $today_expense += $t_income_expense->amount;
            }
        }
        $data['today_income'] = $today_income;

        $data['today_expense'] = $today_expense;

        $total_income = 0;
        $total_expense = 0;
        foreach ($total_income_expense as $v_income_expense) {
            if ($v_income_expense->type == 'Income') {
                $total_income += $v_income_expense->amount;

            } elseif ($v_income_expense->type == 'Expense') {
                $total_expense += $v_income_expense->amount;
            }
        }
        $data['total_income'] = $total_income;

        $data['total_expense'] = $total_expense;

        $user_id = $this->session->userdata('user_id');
        $user_info = $this->admin_model->check_by(array('user_id' => $user_id), 'tbl_users');
        $data['role'] = $user_info->role_id;

        $data['invoce_total'] = $this->invoice_totals_per_currency();
        if (!empty($action) && $action == 'payments') {
            $data['yearly'] = $this->input->post('yearly', TRUE);
        } else {
            $data['yearly'] = date('Y'); // get current year
        }
        if (!empty($action) && $action == 'Income') {
            $data['Income'] = $this->input->post('Income', TRUE);
        } else {
            $data['Income'] = date('Y'); // get current year
        }
        if ($this->input->post('year', TRUE)) { // if input year 
            $data['year'] = $this->input->post('year', TRUE);
        } else { // else current year
            $data['year'] = date('Y'); // get current year
        }
        // get all expense list by year and month
        $data['all_income'] = $this->get_transactions_list($data['Income'], 'Income');

        $data['all_expense'] = $this->get_transactions_list($data['year'], 'Expense');

        $data['yearly_overview'] = $this->get_yearly_overview($data['yearly']);

        if ($this->input->post('month', TRUE)) { // if input year 
            $data['month'] = $this->input->post('month', TRUE);
        } else { // else current year
            $data['month'] = date('Y-m'); // get current year
        }
        $data['income_expense'] = $this->get_income_expense($data['month']);

        $data['subview'] = $this->load->view('admin/calendar', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    function invoice_totals_per_currency()
    {
        $invoices_info = $this->db->where('inv_deleted', 'No')->get('tbl_invoices')->result();
        $paid = $due = array();
        $currency = 'USD';
        $symbol = array();
        foreach ($invoices_info as $v_invoices) {
            if (!isset($paid[$v_invoices->currency])) {
                $paid[$v_invoices->currency] = 0;
            }
            if (!isset($due[$v_invoices->currency])) {
                $due[$v_invoices->currency] = 0;
            }
            $paid[$v_invoices->currency] += $this->invoice_model->get_invoice_paid_amount($v_invoices->invoices_id);
            $due[$v_invoices->currency] += $this->invoice_model->get_invoice_due_amount($v_invoices->invoices_id);
            $currency = $this->admin_model->check_by(array('code' => $v_invoices->currency), 'tbl_currencies');
            $symbol[$v_invoices->currency] = $currency->symbol;
        }
        return array("paid" => $paid, "due" => $due, "symbol" => $symbol);
    }

    public function get_yearly_overview($year)
    {// this function is to create get monthy recap report
        for ($i = 1; $i <= 12; $i++) { // query for months
            if ($i >= 1 && $i <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                $month = '0' . $i;
            } else {
                $month = $i;
            }
            $yearly_report[$i] = $this->admin_model->calculate_amount($year, $month); // get all report by start date and in date 
        }
        return $yearly_report; // return the result
    }

    public function get_income_expense($month)
    {// this function is to create get monthy recap report
        //m = date('m', strtotime($month));
        $m = date('m', strtotime($month));
        $year = date('Y', strtotime($month));
        if ($m >= 1 && $m <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
            $start_date = $year . "-" . '0' . $m . '-' . '01';
            $end_date = $year . "-" . '0' . $m . '-' . '31';
        } else {
            $start_date = $year . "-" . $m . '-' . '01';
            $end_date = $year . "-" . $m . '-' . '31';
        }
        $get_expense_list = $this->admin_model->get_transactions_list_by_month($start_date, $end_date); // get all report by start date and in date 

        return $get_expense_list; // return the result
    }

    public function get_transactions_list($year, $type)
    {// this function is to create get monthy recap report
        for ($i = 1; $i <= 12; $i++) { // query for months
            if ($i >= 1 && $i <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                $start_date = $year . "-" . '0' . $i . '-' . '01';
                $end_date = $year . "-" . '0' . $i . '-' . '31';
            } else {
                $start_date = $year . "-" . $i . '-' . '01';
                $end_date = $year . "-" . $i . '-' . '31';
            }
            $get_expense_list[$i] = $this->admin_model->get_transactions_list_by_date($type, $start_date, $end_date); // get all report by start date and in date 
        }
        return $get_expense_list; // return the result
    }

    public function calendar_settings()
    {
        $data['title'] = lang('calendar_settings');
        $data['modal_subview'] = $this->load->view('admin/settings/calendar_settings', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function save_settings()
    {
        $input_data = $this->admin_model->array_from_post(array('gcal_api_key', 'gcal_id', 'project_on_calendar', 'milestone_on_calendar', 'tasks_on_calendar', 'bugs_on_calendar', 'invoice_on_calendar', 'payments_on_calendar', 'estimate_on_calendar', 'opportunities_on_calendar', 'leads_on_calendar', 'goal_tracking_on_calendar', 'holiday_on_calendar', 'absent_on_calendar', 'on_leave_on_calendar',
            'project_color', 'milestone_color', 'tasks_color', 'bugs_color', 'invoice_color', 'payments_color', 'estimate_color', 'opportunities_color', 'leads_color', 'goal_tracking_color', 'absent_color', 'on_leave_color'));

        foreach ($input_data as $key => $value) {
            $data = array('value' => $value);
            $this->db->where('config_key', $key)->update('tbl_config', $data);
            $exists = $this->db->where('config_key', $key)->get('tbl_config');
            if ($exists->num_rows() == 0) {
                $this->db->insert('tbl_config', array("config_key" => $key, "value" => $value));
            }
        }
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'settings',
            'module_field_id' => $this->session->userdata('user_id'),
            'activity' => ('activity_save_settings'),
            'value1' => $input_data['gcal_api_key']
        );

        $this->admin_model->_table_name = 'tbl_activities';
        $this->admin_model->_primary_key = 'activities_id';
        $this->admin_model->save($activity);
        // messages for user
        $type = "success";
        $message = lang('save_settings');
        set_message($type, $message);
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('admin/calendar');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

}
