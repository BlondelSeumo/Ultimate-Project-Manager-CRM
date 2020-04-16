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
class Dashboard extends Admin_Controller
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
        $data['page'] = lang('dashboard');

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

        $data['role'] = $this->session->userdata('user_type');

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

        // goal tracking
        if ($this->input->post('goal_month', TRUE)) { // if input year
            $data['goal_month'] = $this->input->post('goal_month', TRUE);
        } else { // else current year
            $data['goal_month'] = date('Y-m'); // get current year
        }
        $data['goal_report'] = $this->get_goal_report($data['goal_month']);

        if ($this->input->post('finance_overview', TRUE)) { // if input year
            $data['finance_year'] = $this->input->post('finance_overview', TRUE);
        } else { // else current year
            $data['finance_year'] = date('Y'); // get current year
        }
        // get all income/expense list by year and month
        $data['finance_overview_by_year'] = $this->finance_overview_by_year($data['finance_year']);
        // get all income/expense and profit by year.
        // here true used for tracking it's annual
        $data['total_annual'] = $this->finance_overview_by_year($data['finance_year'], true);
        $data['subview'] = $this->load->view('admin/main_content', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function finance_overview_by_year($year, $annual = null)
    {// this function is to create get monthy recap report
        $this->load->model('report_model');
        for ($i = 1; $i <= 12; $i++) { // query for months
            if ($i >= 1 && $i <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                $start_date = $year . "-" . '0' . $i . '-' . '01';
                $end_date = $year . "-" . '0' . $i . '-' . '31';
            } else {
                $start_date = $year . "-" . $i . '-' . '01';
                $end_date = $year . "-" . $i . '-' . '31';
            }
            $finance_overview_list[$i] = $this->report_model->get_report_by_date($start_date, $end_date); // get all report by start date and in date
        }
        if (!empty($annual)) {
            $f_total_expense = 0;
            $f_total_income = 0;
            if (!empty($finance_overview_list)) {
                foreach ($finance_overview_list as $v_finance_overview) {
                    if (!empty($v_finance_overview)) {
                        foreach ($v_finance_overview as $finance_overview) {
                            if ($finance_overview->type == 'Income') {
                                $f_total_income += $finance_overview->amount;
                            }
                            if ($finance_overview->type == 'Expense') {
                                $f_total_expense += $finance_overview->amount;
                            }
                        }
                    }
                }
            }
            $tatal_annual = array(
                'total_income' => $f_total_income,
                'total_expense' => -$f_total_expense,
                'total_profit' => $f_total_income - $f_total_expense,
            );
            return $tatal_annual;
        }

        return $finance_overview_list; // return the result
    }

    public function get_goal_report($month)
    {// this function is to create get monthy recap report

        //m = date('m', strtotime($month));
        $m = date('m', strtotime($month));
        $year = date('Y', strtotime($month));
        $start_date = $year . "-" . $m . '-' . '01';
        $end_date = $year . "-" . $m . '-' . '31';

        $get_goal_report = $this->admin_model->get_goal_report_by_month($start_date, $end_date); // get all report by start date and in date

        return $get_goal_report; // return the result
    }

    function invoice_totals_per_currency()
    {
        $invoices_info = $this->db->where(array('inv_deleted' => 'No', 'status !=' => 'draft'))->get('tbl_invoices')->result();

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

    public function set_language($lang)
    {
        $check_languages = get_row('tbl_languages', array('active' => 1, 'name' => $lang));
        if (!empty($check_languages)) {
            $this->session->set_userdata('lang', $lang);
        } else {
            set_message('error', lang('nothing_to_display'));
        }
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('admin/dashboard');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function set_clocking($id = NULL, $user_id = null, $row = null, $redirect = null)
    {
        if (!empty(attendance_access())) {
            if ($id == 0) {
                $id = null;
            }
            if ($row == 0) {
                $row = null;
            }
            // sate into attendance table
            if (!empty($user_id)) {
                $adata['user_id'] = $user_id;
            } else {
                $adata['user_id'] = $this->session->userdata('user_id');
            }
            if (!empty($row)) {
                $clocktime = 1;
            } elseif (!empty($id)) {
                $clocktime = 2;
            } else {
                $clocktime = 1;
            }

            $date = date('Y-m-d');
            $time = date('H:i:s');
            //        $already_clocking = $this->admin_model->check_by(array('user_id' => $adata['user_id'], 'clocking_status' => 1), 'tbl_attendance');
            if ($clocktime == 1) {
                $adata['date_in'] = $date;
                $adata['date_out'] = $date;
            } else {
                $adata['date_out'] = $date;
            }
            if (!empty($adata['date_in'])) {
                // check existing date is here or not
                $check_date = $this->admin_model->check_by(array('user_id' => $adata['user_id'], 'date_in' => $adata['date_in']), 'tbl_attendance');
            }
            if (!empty($check_date)) { // if exis do not save date and return id
                $this->admin_model->_table_name = "tbl_attendance"; // table name
                $this->admin_model->_primary_key = "attendance_id"; // $id
                if ($check_date->attendance_status != '1') {
                    $udata['attendance_status'] = 1;
                    $this->admin_model->save($udata, $check_date->attendance_id);
                }
                if ($check_date->clocking_status == 0) {
                    $udata['date_out'] = $date;
                    $udata['clocking_status'] = 1;
                    $this->admin_model->save($udata, $check_date->attendance_id);
                }
                $data['attendance_id'] = $check_date->attendance_id;
            } else { // else save data into tbl attendance
                // get attendance id by clock id into tbl clock
                // if attendance id exist that means he/she clock in
                // return the id
                // and update the day out time
                $check_existing_data = $this->admin_model->check_by(array('clock_id' => $id), 'tbl_clock');
                $this->admin_model->_table_name = "tbl_attendance"; // table name
                $this->admin_model->_primary_key = "attendance_id"; // $id
                if (!empty($check_existing_data)) {
                    $adata['clocking_status'] = 0;
                    $this->admin_model->save($adata, $check_existing_data->attendance_id);
                } else {
                    $adata['attendance_status'] = 1;
                    $adata['clocking_status'] = 1;
                    //save data into attendance table
                    $data['attendance_id'] = $this->admin_model->save($adata);
                }
            }
            // save data into clock table
            if ($clocktime == 1) {
                $data['clockin_time'] = $time;
                send_clock_email('clock_in_email');
            } else {
                $data['clockout_time'] = $time;
                $data['comments'] = $this->input->post('comments', TRUE);
                send_clock_email('clock_out_email');
            }
            $data['ip_address'] = $this->input->ip_address();
            //save data in database
            $this->admin_model->_table_name = "tbl_clock"; // table name
            $this->admin_model->_primary_key = "clock_id"; // $id
            if (!empty($id)) {
                $data['clocking_status'] = 0;
                $this->admin_model->save($data, $id);
            } else {
                $data['clocking_status'] = 1;
                $id = $this->admin_model->save($data);
                if (!empty($check_date)) {
                    if ($check_date->clocking_status == 1) {
                        $data['clockout_time'] = $time;
                        $data['clocking_status'] = 0;
                        $this->admin_model->save($data, $id);
                    }
                }
            }
        } else {
            set_message('error', 'please contact with admin to clock in');
        }
        if (empty($redirect)) {
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/dashboard');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            return true;
        }
    }

    public function update_clock()
    {
        $clock_in = $this->input->post('clock_in', true);
        $clock_out = $this->input->post('clock_out', true);
        if (!empty($clock_in)) {
            foreach ($clock_in as $user_id) {
                $this->set_clocking(0, $user_id, true, true);
            }
        }
        if (!empty($clock_out)) {
            foreach ($clock_out as $clock_out_id) {
                $clock_id = $this->input->post($clock_out_id, true);
                $this->set_clocking($clock_id, $clock_out_id, 0, true);
            }
        }
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('admin/dashboard');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public
    function mark_attendance()
    {
        $this->load->model('global_model');
        $this->load->model('attendance_model');

        $data['title'] = lang('mark_attendance');
        $date = $this->input->post('attend_date', TRUE);

        if (empty($date)) {
            $date = date('Y-m-d');
        }
        $data['date'] = $date;

        $month = date('n', strtotime($date));
        $year = date('Y', strtotime($date));
        $day = date('d', strtotime($date));

        $data['users'] = get_staff_details();

        $holidays = $this->global_model->get_holidays(); //tbl working Days Holiday

        if ($month >= 1 && $month <= 9) {
            $yymm = $year . '-' . '0' . $month;
        } else {
            $yymm = $year . '-' . $month;
        }

        $public_holiday = $this->global_model->get_public_holidays($yymm);

        //tbl a_calendar Days Holiday
        if (!empty($public_holiday)) {
            foreach ($public_holiday as $p_holiday) {
                $p_hday = $this->global_model->GetDays($p_holiday->start_date, $p_holiday->end_date);
            }
        }
        foreach ($data['users'] as $sl => $v_employee) {
            if ($v_employee->user_id != $this->session->userdata('user_id')) {
                $x = 1;
                if ($day >= 1 && $day <= 9) {
                    $sdate = $yymm . '-' . '0' . $day;
                } else {
                    $sdate = $yymm . '-' . $day;
                }
                $day_name = date('l', strtotime("+$x days", strtotime($year . '-' . $month . '-' . $day)));

                // get leave info

                if (!empty($holidays)) {
                    foreach ($holidays as $v_holiday) {
                        if ($v_holiday->day == $day_name) {
                            $flag = 'H';
                        }
                    }
                }
                if (!empty($p_hday)) {
                    foreach ($p_hday as $v_hday) {
                        if ($v_hday == $sdate) {
                            $flag = 'H';
                        }
                    }
                }
                if (!empty($flag)) {
                    $data['attendace_info'][$sl] = $this->attendance_model->attendance_report_by_empid($v_employee->user_id, $sdate, $flag);
                } else {
                    $data['attendace_info'][$sl] = $this->attendance_model->attendance_report_by_empid($v_employee->user_id, $sdate);
                }
                $flag = '';
            }
        }
        $data['subview'] = $this->load->view('admin/settings/mark_attendance', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function change_report($status, $type = null)
    {
        if (!empty($type)) {
            if ($status == 'show') {
                $input_data[$type . '_state'] = 'block';
            } else {
                $input_data[$type . '_state'] = 'none';
            }
        } else {
            if ($status == 'show') {
                $input_data['invoice_state'] = 'block';
            } else {
                $input_data['invoice_state'] = 'none';
            }
        }
        foreach ($input_data as $key => $value) {
            $data = array('value' => $value);
            $this->db->where('config_key', $key)->update('tbl_config', $data);
            $exists = $this->db->where('config_key', $key)->get('tbl_config');
            if ($exists->num_rows() == 0) {
                $this->db->insert('tbl_config', array("config_key" => $key, "value" => $value));
            }
        }
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('admin/dashboard');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function new_todo($id = null)
    {
        $data['title'] = lang('new') . ' ' . lang('to_do');
        if (!empty($id)) {
            $data['todo_info'] = $this->db->where('todo_id', $id)->get('tbl_todo')->row();;
        }
        $data['modal_subview'] = $this->load->view('admin/settings/new_todo', $data, FALSE);
        $this->load->view('admin/_layout_modal_lg', $data); //page load
    }

    public function save_todo($id = null)
    {
        $data = $this->admin_model->array_from_post(array('user_id', 'title', 'status', 'due_date'));
        if (!empty($data['user_id']) && $data['user_id'] != $this->session->userdata('user_id')) {
            $data['assigned'] = $this->session->userdata('user_id');
        } else {
            $data['assigned'] = 0;
        }
        if (empty($data['user_id'])) {
            $data['user_id'] = $this->session->userdata('user_id');
        }
        if (empty($id)) {
            $data['order'] = 1;
        }
        $this->admin_model->_table_name = "tbl_todo"; // table name
        $this->admin_model->_primary_key = "todo_id"; // $id
        $this->admin_model->save($data, $id);
        $type = "success";
        $message = lang('todo_information_updated');
        set_message($type, $message);
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('admin/dashboard');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    function delete_todo($todo_id = '')
    {
        $this->db->where('todo_id', $todo_id);
        $this->db->delete('tbl_todo');
        $type = "success";
        $message = lang('todo_information_deleted');
        set_message($type, $message);
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('admin/dashboard');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    function completed_todo($todo_id = '')
    {
        $data['status'] = $this->input->post('status', true);
        $this->db->where('todo_id', $todo_id);
        $this->db->update('tbl_todo', $data);
        $type = "success";
        $message = lang('todo_status_change');
        echo json_encode(array("status" => $type, "message" => $message));
        exit();
    }

    public function change_todo_status($id = null, $status = null)
    {

        $_status = $this->input->post('status', true);
        if (!empty($_status)) {
            $todo_id = $this->input->post('todo_id', true);
            foreach ($todo_id as $key => $id) {
                $data['status'] = $_status;
                $data['order'] = $key + 1;
                //save data into table.
                $this->admin_model->_table_name = "tbl_todo"; // table name
                $this->admin_model->_primary_key = "todo_id"; // $id
                $this->admin_model->save($data, $id);
            }
            $post = true;

        } else {
            $data['status'] = $status;
            $todo_id = $id;

            $this->admin_model->_table_name = "tbl_todo"; // table name
            $this->admin_model->_primary_key = "todo_id"; // $id
            $this->admin_model->save($data, $todo_id);

        }
        if (!empty($post)) {
            $type = "success";
            $message = lang('todo_status_change');
            echo json_encode(array("status" => $type, "message" => $message));
            exit();
        } else {
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/dashboard');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }

    }

    public function all_todo($id = null)
    {
        $data['title'] = lang('all') . ' ' . lang('to_do') . ' ' . lang('list');
        $user_id = $this->input->post('user_id', true);
        if ($id == 'kanban') {
            $k_session['todo_kanban'] = $id;
            $this->session->set_userdata($k_session);
        } elseif ($id == 'list') {
            $data['active'] = 1;
            $this->session->unset_userdata('todo_kanban');
        }
        if (!empty($user_id)) {
            $data['user_id'] = $user_id;
            if ($user_id != $this->session->userdata('user_id')) {
                $data['where'] = array('assigned' => $this->session->userdata('user_id'));
            } else {
                $data['where'] = null;
            }

        } else {
            $data['user_id'] = $this->session->userdata('user_id');
            $data['where'] = null;
        }

        $data['subview'] = $this->load->view('admin/settings/all_todo', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

}
