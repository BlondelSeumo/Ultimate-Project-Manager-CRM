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
class Dashboard extends Client_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin_model');
        $this->load->model('invoice_model');
        $this->load->model('client_model');
    }

    public function index($action = NULL)
    {
        $data['title'] = config_item('company_name');
        $data['page'] = lang('dashboard');
        $data['breadcrumbs'] = lang('dashboard');
        // get all project by client id
        $this->invoice_model->_table_name = "tbl_transactions"; //table name
        $this->invoice_model->_order_by = "transactions_id";
        $data['client_transactions'] = $this->invoice_model->get_by(array('paid_by' => $this->session->userdata('client_id')), FALSE);

        $data['total_income'] = $this->db->where('paid_by', $this->session->userdata('client_id'))->select_sum('credit')->get('tbl_transactions')->row();
        $data['total_expense'] = $this->db->where('paid_by', $this->session->userdata('client_id'))->select_sum('debit')->get('tbl_transactions')->row();

        $data['subview'] = $this->load->view('client/main_content', $data, TRUE);
        $this->load->view('client/_layout_main', $data);
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

    public function get_expense_list($year)
    {// this function is to create get monthy recap report
        for ($i = 1; $i <= 12; $i++) { // query for months
            if ($i >= 1 && $i <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                $start_date = $year . "-" . '0' . $i . '-' . '01';
                $end_date = $year . "-" . '0' . $i . '-' . '31';
            } else {
                $start_date = $year . "-" . $i . '-' . '01';
                $end_date = $year . "-" . $i . '-' . '31';
            }
            $get_expense_list[$i] = $this->admin_model->get_expense_list_by_date($start_date, $end_date); // get all report by start date and in date 
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
            redirect('client/dashboard');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function announcements_details($id)
    {
        $data['title'] = lang('announcements_details'); //Page title
        $this->admin_model->_table_name = "tbl_announcements"; // table name
        $this->admin_model->_order_by = "announcements_id"; // $id
        $data['announcements_details'] = $this->admin_model->get_by(array('announcements_id' => $id), TRUE);
        $this->admin_model->_primary_key = 'announcements_id';
        $updata['view_status'] = '1';
        $this->admin_model->save($updata, $id);

        $data['subview'] = $this->load->view('admin/announcements/announcements_details', $data, FALSE);
        $this->load->view('admin/_layout_modal_lg', $data); //page load
    }

}
