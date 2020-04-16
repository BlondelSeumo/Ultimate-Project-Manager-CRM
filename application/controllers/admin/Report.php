<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Report extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('report_model');
        $this->load->model('invoice_model');
        $this->load->model('estimates_model');
        $this->load->model('proposal_model');

        $this->load->helper('ckeditor');
        $this->data['ckeditor'] = array(
            'id' => 'ck_editor',
            'path' => 'asset/js/ckeditor',
            'config' => array(
                'toolbar' => "Full",
                'width' => "99.8%",
                'height' => "400px"
            )
        );
    }

    public function account_statement()
    {
        $data['title'] = lang('account_statement');
        $data['account_id'] = $this->input->post('account_id', TRUE);
        if (!empty($data['account_id'])) {
            $data['report'] = TRUE;
            $data['start_date'] = $this->input->post('start_date', TRUE);
            $data['end_date'] = $this->input->post('end_date', TRUE);
            $data['transaction_type'] = $this->input->post('transaction_type', TRUE);
            $data['all_transaction_info'] = $this->get_account_statement($data['account_id'], $data['start_date'], $data['end_date'], $data['transaction_type']);
        }
        $data['subview'] = $this->load->view('admin/report/account_statement', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function incomeList($id = null, $type = null)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->load->model('transactions_model');
            $this->datatables->table = 'tbl_transactions';
            $this->datatables->join_table = array('tbl_accounts', 'tbl_client');
            $this->datatables->join_where = array('tbl_accounts.account_id=tbl_transactions.account_id', 'tbl_transactions.paid_by=tbl_client.client_id');
            $this->datatables->column_order = array('tbl_transactions.name', 'tbl_accounts.account_name', 'date', 'notes', 'category_id', 'tbl_client.name', 'debit', 'credit', 'amount', 'reference', 'total_balance');
            $this->datatables->column_search = array('tbl_transactions.name', 'tbl_accounts.account_name', 'date', 'notes', 'category_id', 'tbl_client.name', 'debit', 'credit', 'amount', 'reference', 'total_balance');
            $this->datatables->order = array('transactions_id' => 'desc');
            // get all invoice
            $fetch_data = $this->datatables->get_deposit($id, $type);

            $data = array();

            $edited = can_action('30', 'edited');
            $deleted = can_action('30', 'deleted');
            foreach ($fetch_data as $_key => $v_deposit) {
                $action = null;
                $can_edit = $this->transactions_model->can_action('tbl_transactions', 'edit', array('transactions_id' => $v_deposit->transactions_id));
                $can_delete = $this->transactions_model->can_action('tbl_transactions', 'delete', array('transactions_id' => $v_deposit->transactions_id));
                $account_info = $this->transactions_model->check_by(array('account_id' => $v_deposit->account_id), 'tbl_accounts');

                $sub_array = array();
                $name = null;
                $name .= '<a data-toggle="modal" data-target="#myModal" class="text-info" href="' . base_url() . 'admin/transactions/view_expense/' . $v_deposit->transactions_id . '">' . display_date($v_deposit->date) . '</a>';
                $sub_array[] = $name;

                $sub_array[] = (!empty($account_info->account_name) ? $account_info->account_name : '-');

                $sub_array[] = $v_deposit->notes;

                $sub_array[] = display_money($v_deposit->amount, default_currency());
                $sub_array[] = display_money($v_deposit->credit, default_currency());
                $sub_array[] = display_money($v_deposit->debit, default_currency());
                $sub_array[] = display_money($v_deposit->total_balance, default_currency());

                $action .= '<a class="btn btn-info btn-xs" data-toggle="modal" data-target="#myModal" class="text-info" href="' . base_url() . 'admin/transactions/view_expense/' . $v_deposit->transactions_id . '"><span class="fa fa-list-alt"></span></a>' . ' ';
                if (!empty($can_edit) && !empty($edited)) {
                    $action .= btn_edit('admin/transactions/deposit/' . $v_deposit->transactions_id) . ' ';
                }
                if (!empty($can_delete) && !empty($deleted)) {
                    $action .= ajax_anchor(base_url("admin/transactions/delete_deposit/$v_deposit->transactions_id"), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                }
                $sub_array[] = $action;
                $data[] = $sub_array;

            }
            render_table($data, array('type' => 'Income'));
        } else {
            redirect('admin/dashboard');
        }
    }

    public function expenseList($id = null, $type = null)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_transactions';
            $this->datatables->join_table = array('tbl_accounts', 'tbl_client');
            $this->datatables->join_where = array('tbl_accounts.account_id=tbl_transactions.account_id', 'tbl_transactions.paid_by=tbl_client.client_id');
            $this->datatables->column_order = array('tbl_transactions.name', 'tbl_accounts.account_name', 'date', 'notes', 'category_id', 'tbl_client.name', 'debit', 'credit', 'amount', 'reference', 'total_balance');
            $this->datatables->column_search = array('tbl_transactions.name', 'tbl_accounts.account_name', 'date', 'notes', 'category_id', 'tbl_client.name', 'debit', 'credit', 'amount', 'reference', 'total_balance');
            $this->datatables->order = array('transactions_id' => 'desc');
            // get all invoice
            $this->load->model('transactions_model');
            $fetch_data = $this->datatables->get_expense($id, $type);

            $data = array();

            $edited = can_action('30', 'edited');
            $deleted = can_action('30', 'deleted');
            foreach ($fetch_data as $_key => $v_expense) {
                $action = null;
                $can_edit = $this->transactions_model->can_action('tbl_transactions', 'edit', array('transactions_id' => $v_expense->transactions_id));
                $can_delete = $this->transactions_model->can_action('tbl_transactions', 'delete', array('transactions_id' => $v_expense->transactions_id));
                $account_info = $this->transactions_model->check_by(array('account_id' => $v_expense->account_id), 'tbl_accounts');

                $sub_array = array();

                $date = null;
                $date .= '<a class="text-info" href="' . base_url() . 'admin/transactions/view_expense/' . $v_expense->transactions_id . '">' . strftime(config_item('date_format'), strtotime($v_expense->date)) . '</a>';
                $sub_array[] = $date;

                $sub_array[] = (!empty($account_info->account_name) ? $account_info->account_name : '-');
                $sub_array[] = $v_expense->notes;
                $sub_array[] = display_money($v_expense->amount, default_currency());
                $sub_array[] = display_money($v_expense->credit, default_currency());
                $sub_array[] = display_money($v_expense->debit, default_currency());
                $sub_array[] = display_money($v_expense->total_balance, default_currency());

                $action .= '<a class="btn btn-info btn-xs" class="text-info" href="' . base_url() . 'admin/transactions/view_expense/' . $v_expense->transactions_id . '"><span class="fa fa-list-alt"></span></a>' . ' ';
                if (!empty($can_edit) && !empty($edited)) {
                    $action .= btn_edit('admin/transactions/expense/' . $v_expense->transactions_id) . ' ';
                }
                if (!empty($can_delete) && !empty($deleted)) {
                    $action .= ajax_anchor(base_url("admin/transactions/delete_expense/$v_expense->transactions_id"), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                }
                $sub_array[] = $action;
                $data[] = $sub_array;

            }

            render_table($data, array('type' => 'Expense'));
        } else {
            redirect('admin/dashboard');
        }
    }

    function get_account_statement($account_id, $start_date, $end_date, $transaction_type)
    {
        if ($transaction_type == 'all_transactions') {
            $where = array('account_id' => $account_id, 'date >=' => $start_date, 'date <=' => $end_date);
        } elseif ($transaction_type == 'debit') {
            $where = array('account_id' => $account_id, 'date >=' => $start_date, 'date <=' => $end_date, 'credit' => $transaction_type);
        } else {
            $where = array('account_id' => $account_id, 'date >=' => $start_date, 'date <=' => $end_date, 'debit' => $transaction_type);
        }
        $this->report_model->_table_name = "tbl_transactions"; //table name
        $this->report_model->_order_by = "transactions_id";
        return $this->report_model->get_by($where, FALSE);
    }

    public function account_statement_pdf($account_id, $start_date, $end_date, $transaction_type)
    {

        $data['all_transaction_info'] = $this->get_account_statement($account_id, $start_date, $end_date, $transaction_type);
        $data['title'] = lang('account_statement');
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/report/account_statement_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it(lang('account_statement') . ' From:' . $start_date . ' To:', $end_date));
    }

    public function income_report()
    {
        $data['title'] = lang('income_report');
        $data['transactions_report'] = $this->get_transactions_report();
        $data['subview'] = $this->load->view('admin/report/income_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function income_report_pdf()
    {
        $data['title'] = lang('income_report');
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/report/income_report_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it(lang('income_report')));
    }

    public function get_transactions_report()
    {// this function is to create get monthy recap report
        $m = date('n');
        $year = date('Y');
        $num = cal_days_in_month(CAL_GREGORIAN, $m, $year);
        for ($i = 1; $i <= $num; $i++) {
            if ($m >= 1 && $m <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                $date = $year . "-" . '0' . $m;
            } else {
                $date = $year . "-" . $m;
            }
            $date = $date . '-' . $i;
            $transaction_report[$i] = $this->db->where('date', $date)->order_by('transactions_id', 'DESC')->get('tbl_transactions')->result();
        }
        return $transaction_report; // return the result
    }

    public function expense_report()
    {
        $data['title'] = lang('expense_report');
        $data['transactions_report'] = $this->get_transactions_report();
        $data['subview'] = $this->load->view('admin/report/expense_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function expense_report_pdf()
    {
        $data['title'] = lang('expense_report');
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/report/expense_report_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it(lang('expense_report')));
    }

    public function income_expense()
    {
        $data['title'] = lang('income_expense');
        $data['transactions_report'] = $this->get_transactions_report();
        $data['subview'] = $this->load->view('admin/report/income_expense', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function income_expense_pdf()
    {
        $data['title'] = lang('income_expense');
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/report/income_expense_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it(lang('income_expense')));
    }

    public function date_wise_report()
    {
        $data['title'] = lang('date_wise_report');
        $data['start_date'] = $this->input->post('start_date', TRUE);
        $data['end_date'] = $this->input->post('end_date', TRUE);
        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            $data['report'] = TRUE;
            $data['all_transaction_info'] = $this->db->where('date >=', $data['start_date'], 'date >=', $data['end_date'])->get('tbl_transactions')->result();
        }
        $data['subview'] = $this->load->view('admin/report/date_wise_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function date_wise_report_pdf($start_date, $end_date)
    {
        $data['title'] = lang('date_wise_report');
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $this->load->helper('dompdf');
        $data['all_transaction_info'] = get_order_by('tbl_transactions', array('date >=' => $data['start_date'], 'date <=' => $data['end_date']));
        $viewfile = $this->load->view('admin/report/date_wise_report_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it(lang('date_wise_report')));
    }

    public function report_by_month()
    {
        $data['title'] = lang('report_by_month');
        $data['current_month'] = date('m');

        if ($this->input->post('year', TRUE)) { // if input year 
            $data['year'] = $this->input->post('year', TRUE);
        } else { // else current year
            $data['year'] = date('Y'); // get current year
        }
        // get all expense list by year and month
        $data['report_by_month'] = $this->get_report_by_month($data['year']);

        $data['subview'] = $this->load->view('admin/report/report_by_month', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function get_report_by_month($year, $month = NULL)
    {// this function is to create get monthy recap report
        if (!empty($month)) {
            if ($month >= 1 && $month <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                $start_date = $year . "-" . '0' . $month . '-' . '01';
                $end_date = $year . "-" . '0' . $month . '-' . '31';
            } else {
                $start_date = $year . "-" . $month . '-' . '01';
                $end_date = $year . "-" . $month . '-' . '31';
            }
            $get_expense_list = $this->report_model->get_report_by_date($start_date, $end_date); // get all report by start date and in date 
        } else {
            for ($i = 1; $i <= 12; $i++) { // query for months
                if ($i >= 1 && $i <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                    $start_date = $year . "-" . '0' . $i . '-' . '01';
                    $end_date = $year . "-" . '0' . $i . '-' . '31';
                } else {
                    $start_date = $year . "-" . $i . '-' . '01';
                    $end_date = $year . "-" . $i . '-' . '31';
                }
                $get_expense_list[$i] = $this->report_model->get_report_by_date($start_date, $end_date); // get all report by start date and in date 
            }
        }
        return $get_expense_list; // return the result
    }

    public function report_by_month_pdf($year, $month)
    {
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'report/report_by_month',
            'module_field_id' => $year,
            'activity' => lang('activity_report_by_month_pdf'),
            'icon' => 'fa-laptop',
            'value1' => $year,
            'value2' => $month
        );
        $this->report_model->_table_name = 'tbl_activities';
        $this->report_model->_primary_key = 'activities_id';
        $this->report_model->save($activity);

        $data['report_list'] = $this->get_report_by_month($year, $month);
        $month_name = date('F', strtotime($year . '-' . $month)); // get full name of month by date query                
        $data['monthyaer'] = $month_name . '  ' . $year;
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/report/report_by_month_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it(lang('report_by_month') . '- ' . $data['monthyaer']));
    }

    public function all_income()
    {
        $data['title'] = lang('all_income');
        $data['subview'] = $this->load->view('admin/report/all_income', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function all_expense()
    {
        $data['title'] = lang('all_expense');
        $data['subview'] = $this->load->view('admin/report/all_expense', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function all_transaction()
    {
        $data['title'] = lang('all_transaction');
        $data['transactions_report'] = $this->get_transactions_report();
        $data['subview'] = $this->load->view('admin/report/all_transaction', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function tasks_assignment()
    {
        $data['title'] = lang('tasks_assignment');
        $data['all_project'] = $this->report_model->get_permission('tbl_project');
        // get permission user by menu id
        $permission_user = $this->report_model->all_permission_user('57');
        // get all admin user
        $admin_user = $this->db->where('role_id', 1)->get('tbl_users')->result();
        // if not exist data show empty array.
        if (!empty($permission_user)) {
            $permission_user = $permission_user;
        } else {
            $permission_user = array();
        }
        if (!empty($admin_user)) {
            $admin_user = $admin_user;
        } else {
            $admin_user = array();
        }
        $data['assign_user'] = array_merge($admin_user, $permission_user);

        $data['user_tasks'] = $this->get_tasks_by_user($data['assign_user']);

        $data['subview'] = $this->load->view('admin/report/project_tasks_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }


    function get_tasks_by_user($assign_user, $tasks = null)
    {
        $tasks_info = $this->report_model->get_permission('tbl_task');
        if (!empty($tasks_info)):foreach ($tasks_info as $v_tasks):
            if (!empty($tasks)) {
                if ($v_tasks->permission == 'all') {
                    $permission[$v_tasks->permission][$v_tasks->task_status][] = $v_tasks->task_status;
                } else {
                    $get_permission = json_decode($v_tasks->permission);
                    if (!empty($get_permission)) {
                        foreach ($get_permission as $id => $v_permission) {
                            if (!empty($assign_user)) {
                                foreach ($assign_user as $v_user) {
                                    if ($v_user->user_id == $id) {
                                        $permission[$v_user->user_id][$v_tasks->task_status][] = $v_tasks->task_status;
                                    }
                                }
                            }

                        }
                    }
                }
            } else {
                if (!empty($v_tasks->project_id)) {

                    if ($v_tasks->permission == 'all') {
                        $permission[$v_tasks->permission][$v_tasks->task_status][] = $v_tasks->task_status;
                    } else {
                        $get_permission = json_decode($v_tasks->permission);
                        if (!empty($get_permission)) {
                            foreach ($get_permission as $id => $v_permission) {
                                if (!empty($assign_user)) {
                                    foreach ($assign_user as $v_user) {
                                        if ($v_user->user_id == $id) {
                                            $permission[$v_user->user_id][$v_tasks->task_status][] = $v_tasks->task_status;
                                        }
                                    }
                                }

                            }
                        }
                    }
                }
            }


        endforeach;
        endif;
        if (empty($permission)) {
            $permission = array();
        }
        return $permission;
    }

    public
    function bugs_assignment()
    {
        $data['title'] = lang('bugs_assignment') . ' ' . lang('report');
        $data['all_project'] = $this->report_model->get_permission('tbl_project');
        // get permission user by menu id
        $permission_user = $this->report_model->all_permission_user('58');
        // get all admin user
        $admin_user = $this->db->where('role_id', 1)->get('tbl_users')->result();
        // if not exist data show empty array.
        if (!empty($permission_user)) {
            $permission_user = $permission_user;
        } else {
            $permission_user = array();
        }
        if (!empty($admin_user)) {
            $admin_user = $admin_user;
        } else {
            $admin_user = array();
        }
        $data['assign_user'] = array_merge($admin_user, $permission_user);

        $data['user_bugs'] = $this->get_bugs_by_user($data['assign_user']);

        $data['yearly_report'] = $this->get_project_report_by_month();
        $data['subview'] = $this->load->view('admin/report/project_bugs_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function get_project_report_by_month($tickets = null)
    {// this function is to create get monthy recap report

        for ($i = 1; $i <= 12; $i++) { // query for months
            if ($i >= 1 && $i <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                $start_date = date('Y') . "-" . '0' . $i . '-' . '01';
                $end_date = date('Y') . "-" . '0' . $i . '-' . '31';
            } else {
                $start_date = date('Y') . "-" . $i . '-' . '01';
                $end_date = date('Y') . "-" . $i . '-' . '31';
            }
            if (!empty($tickets)) {
                $where = array('created >=' => $start_date . ' 00:00:00', 'created <=' => $end_date . ' 23:59:59');
                $get_result[$i] = $this->db->where($where)->get('tbl_tickets')->result();; // get all report by start date and in date
            } else {
                $where = array('created_time >=' => $start_date, 'created_time <=' => $end_date);
                $get_result[$i] = $this->db->where($where)->get('tbl_bug')->result();; // get all report by start date and in date
            }
        }

        return $get_result; // return the result
    }

    function get_bugs_by_user($assign_user, $bugs = null)
    {
        $bugs_info = $this->report_model->get_permission('tbl_bug');

        if (!empty($bugs_info)):foreach ($bugs_info as $v_bugs):
            if (!empty($bugs)) {
                if ($v_bugs->permission == 'all') {
                    $permission[$v_bugs->permission][$v_bugs->bug_status][] = $v_bugs->bug_status;
                } else {
                    $get_permission = json_decode($v_bugs->permission);
                    if (!empty($get_permission)) {
                        foreach ($get_permission as $id => $v_permission) {
                            if (!empty($assign_user)) {
                                foreach ($assign_user as $v_user) {
                                    if ($v_user->user_id == $id) {
                                        $permission[$v_user->user_id][$v_bugs->bug_status][] = $v_bugs->bug_status;
                                    }
                                }
                            }

                        }
                    }
                }
            } else {
                if (!empty($v_bugs->project_id)) {

                    if ($v_bugs->permission == 'all') {
                        $permission[$v_bugs->permission][$v_bugs->bug_status][] = $v_bugs->bug_status;
                    } else {
                        $get_permission = json_decode($v_bugs->permission);
                        if (!empty($get_permission)) {
                            foreach ($get_permission as $id => $v_permission) {
                                if (!empty($assign_user)) {
                                    foreach ($assign_user as $v_user) {
                                        if ($v_user->user_id == $id) {
                                            $permission[$v_user->user_id][$v_bugs->bug_status][] = $v_bugs->bug_status;
                                        }
                                    }
                                }

                            }
                        }
                    }
                }
            }

        endforeach;
        endif;
        if (empty($permission)) {
            $permission = array();
        }
        return $permission;
    }

    public function project_report()
    {
        $data['title'] = lang('project_report');
        // get permission user by menu id
        $permission_user = $this->report_model->all_permission_user('57');
        // get all admin user
        $admin_user = $this->db->where('role_id', 1)->get('tbl_users')->result();
        // if not exist data show empty array.
        if (!empty($permission_user)) {
            $permission_user = $permission_user;
        } else {
            $permission_user = array();
        }
        if (!empty($admin_user)) {
            $admin_user = $admin_user;
        } else {
            $admin_user = array();
        }
        $data['assign_user'] = array_merge($admin_user, $permission_user);

        $data['all_project'] = $this->report_model->get_permission('tbl_project');
        $data['user_project'] = $this->get_project_by_user($data['assign_user']);
        $data['subview'] = $this->load->view('admin/report/project_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function tasks_report()
    {
        $data['title'] = lang('project_report');
        // get permission user by menu id
        $permission_user = $this->report_model->all_permission_user('54');
        // get all admin user
        $admin_user = $this->db->where('role_id', 1)->get('tbl_users')->result();
        // if not exist data show empty array.
        if (!empty($permission_user)) {
            $permission_user = $permission_user;
        } else {
            $permission_user = array();
        }
        if (!empty($admin_user)) {
            $admin_user = $admin_user;
        } else {
            $admin_user = array();
        }
        $data['assign_user'] = array_merge($admin_user, $permission_user);

        $data['all_tasks'] = $this->report_model->get_permission('tbl_task');
        $data['user_tasks'] = $this->get_tasks_by_user($data['assign_user'], true);

        $data['subview'] = $this->load->view('admin/report/tasks_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public
    function bugs_report()
    {
        $data['title'] = lang('bugs_assignment') . ' ' . lang('report');
        // get permission user by menu id
        $permission_user = $this->report_model->all_permission_user('58');
        // get all admin user
        $admin_user = $this->db->where('role_id', 1)->get('tbl_users')->result();
        // if not exist data show empty array.
        if (!empty($permission_user)) {
            $permission_user = $permission_user;
        } else {
            $permission_user = array();
        }
        if (!empty($admin_user)) {
            $admin_user = $admin_user;
        } else {
            $admin_user = array();
        }
        $data['assign_user'] = array_merge($admin_user, $permission_user);
        $data['user_bugs'] = $this->get_bugs_by_user($data['assign_user'], true);

        $data['yearly_report'] = $this->get_project_report_by_month();
        $data['subview'] = $this->load->view('admin/report/bugs_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    function get_project_by_user($assign_user)
    {
        $all_project = $this->report_model->get_permission('tbl_project');
        if (!empty($all_project)):foreach ($all_project as $v_project):
            if ($v_project->permission == 'all') {
                $permission[$v_project->permission][$v_project->project_status][] = $v_project->project_status;
            } else {
                $get_permission = json_decode($v_project->permission);
                if (!empty($get_permission)) {
                    foreach ($get_permission as $id => $v_permission) {
                        if (!empty($assign_user)) {
                            foreach ($assign_user as $v_user) {
                                if ($v_user->user_id == $id) {
                                    $permission[$v_user->user_id][$v_project->project_status][] = $v_project->project_status;
                                }
                            }
                        }

                    }
                }
            }
        endforeach;
        endif;
        if (empty($permission)) {
            $permission = array();
        }

        return $permission;
    }

    public function tickets_report()
    {
        $data['title'] = lang('tickets_report');
        // get permission user by menu id
        $permission_user = $this->report_model->all_permission_user('7');
        // get all admin user
        $admin_user = $this->db->where('role_id', 1)->get('tbl_users')->result();
        // if not exist data show empty array.
        if (!empty($permission_user)) {
            $permission_user = $permission_user;
        } else {
            $permission_user = array();
        }
        if (!empty($admin_user)) {
            $admin_user = $admin_user;
        } else {
            $admin_user = array();
        }
        $data['assign_user'] = array_merge($admin_user, $permission_user);
        $data['user_tickets'] = $this->get_tickets_by_user($data['assign_user']);

        $data['yearly_report'] = $this->get_project_report_by_month(true);

        $data['subview'] = $this->load->view('admin/report/tickets_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    function get_tickets_by_user($assign_user)
    {
        $all_ticktes = $this->report_model->get_permission('tbl_tickets');
        if (!empty($all_ticktes)):foreach ($all_ticktes as $v_ticktes):
            if ($v_ticktes->permission == 'all') {
                $permission[$v_ticktes->permission][$v_ticktes->status][] = $v_ticktes->status;
            } else {
                $get_permission = json_decode($v_ticktes->permission);
                if (!empty($get_permission)) {
                    foreach ($get_permission as $id => $v_permission) {
                        if (!empty($assign_user)) {
                            foreach ($assign_user as $v_user) {
                                if ($v_user->user_id == $id) {
                                    $permission[$v_user->user_id][$v_ticktes->status][] = $v_ticktes->status;
                                }
                            }
                        }

                    }
                }
            }
        endforeach;
        endif;
        if (empty($permission)) {
            $permission = array();
        }

        return $permission;
    }

    public function client_report()
    {
        $data['title'] = lang('client_report');
        $data['all_client_info'] = $this->db->get('tbl_client')->result();

        $data['subview'] = $this->load->view('admin/report/client_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function sales_report($filterBy = null)
    {
        $data['title'] = lang('sales') . ' ' . lang('report');
        if (!empty($filterBy)) {
            $data['filterBy'] = $filterBy;
            if ($filterBy == 'invoices' || $filterBy == 'invoice_by_client') {
                $start_date = null;
                $end_date = null;
                if ($filterBy == 'invoice_by_client') {
                    $client = get_result('tbl_client');
                    $status = !empty($client) ? $client[0]->client_id : '0';
                } else {
                    $status = 'all';
                }
                if ($this->input->post()) {
                    $range = explode('-', $this->input->post('range', true));
                    if (!empty($range[0])) {
                        $start_date = date('Y-m-d', strtotime($range[0]));
                        $end_date = date('Y-m-d', strtotime($range[1]));
                        $data['range'] = array($start_date, $end_date);
                    }
                    $status = $this->input->post('status', true);
                }
                $range = array($start_date, $end_date);
                $data['status'] = $status;
                $data['all_invoices'] = $this->invoice_model->get_invoice_report($data['status'], $range);

            } elseif ($filterBy == 'payments') {
                $start_date = null;
                $end_date = null;
                $client_id = null;
                if ($this->input->post()) {
                    $range = explode('-', $this->input->post('range', true));
                    if (!empty($range[0])) {
                        $start_date = date('Y-m-d', strtotime($range[0]));
                        $end_date = date('Y-m-d', strtotime($range[1]));
                        $data['range'] = array($start_date, $end_date);
                    }
                    $client_id = $this->input->post('client_id', true);
                }
                $range = array($start_date, $end_date);
                $data['status'] = $client_id;
                $data['all_payments'] = $this->invoice_model->get_payment_report($data['status'], $range);
            } else if ($filterBy == 'estimates' || $filterBy == 'estimate_by_client') {
                $start_date = null;
                $end_date = null;
                if ($filterBy == 'estimate_by_client') {
                    $client = get_result('tbl_client');
                    $status = !empty($client) ? $client[0]->client_id : '0';
                } else {
                    $status = 'all';
                }
                if ($this->input->post()) {
                    $range = explode('-', $this->input->post('range', true));
                    if (!empty($range[0])) {
                        $start_date = date('Y-m-d', strtotime($range[0]));
                        $end_date = date('Y-m-d', strtotime($range[1]));
                        $data['range'] = array($start_date, $end_date);
                    }
                    $status = $this->input->post('status', true);
                }
                $range = array($start_date, $end_date);
                $data['status'] = $status;
                $data['all_estimates'] = $this->estimates_model->get_estimate_report($data['status'], $range);
            } else if ($filterBy == 'proposals' || $filterBy == 'proposal_by_client') {
                $start_date = null;
                $end_date = null;
                if ($filterBy == 'proposal_by_client') {
                    $client = get_result('tbl_client');
                    $status = !empty($client) ? $client[0]->client_id : '0';
                } else {
                    $status = 'all';
                }
                if ($this->input->post()) {
                    $range = explode('-', $this->input->post('range', true));
                    if (!empty($range[0])) {
                        $start_date = date('Y-m-d', strtotime($range[0]));
                        $end_date = date('Y-m-d', strtotime($range[1]));
                        $data['range'] = array($start_date, $end_date);
                    }
                    $status = $this->input->post('status', true);
                }
                $range = array($start_date, $end_date);
                $data['status'] = $status;
                $data['all_proposals'] = $this->proposal_model->get_proposals_report($data['status'], $range);
            }
        }
        $data['subview'] = $this->load->view('admin/report/sales_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public
    function sales_report_pdf($filterBy = null, $status, $start_date = null, $end_date = null)
    {

        $data['title'] = lang('sales') . ' ' . lang('report');
        if (!empty($filterBy)) {
            $data['filterBy'] = $filterBy;
            if ($filterBy == 'invoices' || $filterBy == 'invoice_by_client') {
                if (!empty($start_date)) {
                    $data['range'] = array($start_date, $end_date);
                    $date = lang('FROM') . ' ' . display_date($start_date) . ' ' . lang('TO') . ' ' . display_date($end_date);
                } else {
                    $start_date = null;
                    $end_date = null;
                    $date = null;
                }
                $range = array($start_date, $end_date);
                $data['status'] = $status;
                $data['all_invoices'] = $this->invoice_model->get_invoice_report($data['status'], $range);
                $viewfile = $this->load->view('admin/report/invoice_report_pdf', $data, TRUE);
                if (is_numeric($status)) {
                    $status = client_name($status);
                }
                $title = lang($filterBy) . ' ' . lang('report') . '- ' . $status . '- ' . $date;
            } elseif ($filterBy == 'payments') {
                if (!empty($start_date)) {
                    $data['range'] = array($start_date, $end_date);
                    $date = lang('FROM') . ' ' . display_date($start_date) . ' ' . lang('TO') . ' ' . display_date($end_date);
                } else {
                    $start_date = null;
                    $end_date = null;
                    $date = null;
                }
                $range = array($start_date, $end_date);
                $data['status'] = $status;
                $data['all_payments'] = $this->invoice_model->get_payment_report($data['status'], $range);

                $viewfile = $this->load->view('admin/report/payment_report_pdf', $data, TRUE);
                if (is_numeric($status)) {
                    $status = client_name($status);
                }
                $title = lang($filterBy) . ' ' . lang('report') . '- ' . $status . '- ' . $date;
            } else if ($filterBy == 'estimates' || $filterBy == 'estimate_by_client') {
                if (!empty($start_date)) {
                    $data['range'] = array($start_date, $end_date);
                    $date = lang('FROM') . ' ' . display_date($start_date) . ' ' . lang('TO') . ' ' . display_date($end_date);
                } else {
                    $start_date = null;
                    $end_date = null;
                    $date = null;
                }
                $range = array($start_date, $end_date);
                $data['status'] = $status;
                $data['all_estimates'] = $this->estimates_model->get_estimate_report($data['status'], $range);
                $viewfile = $this->load->view('admin/report/estimate_report_pdf', $data, TRUE);
                if (is_numeric($status)) {
                    $status = client_name($status);
                }
                $title = lang($filterBy) . ' ' . lang('report') . '- ' . $status . '- ' . $date;
            } else if ($filterBy == 'proposals' || $filterBy == 'proposal_by_client') {
                if (!empty($start_date)) {
                    $data['range'] = array($start_date, $end_date);
                    $date = lang('FROM') . ' ' . display_date($start_date) . ' ' . lang('TO') . ' ' . display_date($end_date);
                } else {
                    $start_date = null;
                    $end_date = null;
                    $date = null;
                }
                if ($this->input->post()) {
                    $range = explode('-', $this->input->post('range', true));
                    if (!empty($range[0])) {
                        $start_date = date('Y-m-d', strtotime($range[0]));
                        $end_date = date('Y-m-d', strtotime($range[1]));
                        $data['range'] = array($start_date, $end_date);
                    }
                    $status = $this->input->post('status', true);
                }
                $range = array($start_date, $end_date);
                $data['status'] = $status;
                $data['all_proposals'] = $this->proposal_model->get_proposals_report($data['status'], $range);
                if (is_numeric($status)) {
                    $status = client_name($status);
                }
                $title = lang($filterBy) . ' ' . lang('report') . '- ' . $status . '- ' . $date;
                $viewfile = $this->load->view('admin/report/proposals_report_pdf', $data, TRUE);
            }
        }
        $this->load->helper('dompdf');
        if (!empty($viewfile) && !empty($title)) {
            pdf_create($viewfile, slug_it($title));
        }

//        $data['subview'] = $this->load->view('admin/report/sales_report', $data, TRUE);
//        $this->load->view('admin/_layout_main', $data); //page load
    }


}
