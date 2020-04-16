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
class Quotations extends Client_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('quotations_model');
    }

    public function index($action = NULL, $id = NULL)
    {
        $data['title'] = lang('all') . ' ' . lang('quotations');
        $data['page'] = lang('quotations');
        $data['breadcrumbs'] = lang('quotations');
        $data['sub_active'] = lang('quotations');
        $quotationforms_id = $this->input->post('quotationforms_id', TRUE);
        if ($action == 'get_form' && !empty($quotationforms_id)) {
            $data['quotationforms_info'] = $this->quotations_model->check_by(array('quotationforms_id' => $quotationforms_id), 'tbl_quotationforms');

            $form_data = json_decode($data['quotationforms_info']->quotationforms_code, true);

            $data['formbuilder_data'] = $form_data['fields'];

            $data['quotationforms_code'] = json_encode($form_data['fields']);

            $sub_view = 'all_quotations';
            $data['active'] = 2;
        } elseif ($action == 'delete_quotations_form') {
            $this->quotations_model->_table_name = 'tbl_quotationforms';
            $this->quotations_model->_primary_key = 'quotationforms_id';
            $this->quotations_model->delete($id);
            $type = 'success';
            $message = lang('delete_quotation_form');
            set_message($type, $message);
            redirect('client/quotations/');
        } else {

            $sub_view = 'all_quotations';
            //$sub_view = 'all_quotations';
            $data['active'] = 1;
        }
        $this->quotations_model->_table_name = 'tbl_quotations';
        $this->quotations_model->_order_by = 'quotations_id';
        $data['all_quatations'] = $this->quotations_model->get_by(array('client_id' => $this->session->userdata('client_id')), FALSE);

        $data['subview'] = $this->load->view('client/quotations/' . $sub_view, $data, TRUE);
        $this->load->view('client/_layout_main', $data);
    }

    public function quotationsList()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_quotations';
//            $this->datatables->join_table = array('tbl_departments');
//            $this->datatables->join_where = array('tbl_departments.departments_id=tbl_tickets.departments_id');
            $this->datatables->column_order = array('quotations_form_title','quotations_date', 'quotations_amount','quotations_status');
            $this->datatables->column_search = array('quotations_form_title','quotations_date', 'quotations_amount','quotations_status');
            $this->datatables->order = array('quotations_id' => 'desc');
            // get all invoice
            $where = array('client_id' => $this->session->userdata('client_id'));
            $fetch_data = make_datatables($where);
            $data = array();
            foreach ($fetch_data as $_key => $v_quatations) {

                $action = null;
                $sub_array = array();
                $sub_array[] = '<a class="text-info" href="' . base_url() . 'client/quotations/quotations_details/' . $v_quatations->quotations_id . '">' . $v_quatations->quotations_form_title . '</a>';
                $sub_array[] = strftime(config_item('date_format'), strtotime($v_quatations->quotations_date));
                $amount = null;
                if (!empty($v_quatations->quotations_amount)) {
                    $amount = display_money($v_quatations->quotations_amount, client_currency($v_quatations->client_id));
                }
                $sub_array[] = $amount;
                if ($v_quatations->quotations_status == 'completed') {
                    $quotations_status = '<span class="label label-success">' . lang('completed') . '</span>';
                } else {
                    $quotations_status = '<span class="label label-danger">' . lang('pending') . '</span>';
                };
                $sub_array[] = $quotations_status;
                $sub_array[] = $action;
                $data[] = $sub_array;
            }

            render_table($data, $where);
        } else {
            redirect('client/dashboard');
        }
    }

    public function quotations_form($action = NULL, $id = NULL)
    {
        $data['title'] = 'All Quatations';
        $data['page'] = lang('quotations');
        $data['breadcrumbs'] = lang('quotations');
        if ($action == 'edit_quotations_form') {
            $data['sub_active'] = lang('quotations_form');
            $data['quotationforms_info'] = $this->quotations_model->check_by(array('quotationforms_id' => $id), 'tbl_quotationforms');

            $form_data = json_decode($data['quotationforms_info']->quotationforms_code, true);

            $data['formbuilder_data'] = $form_data['fields'];

            $data['quotationforms_code'] = json_encode($form_data['fields']);


            $sub_view = 'quotations_form_details';

            $data['active'] = 2;
        } elseif ($action == 'delete_quotations_form') {
            $this->quotations_model->_table_name = 'tbl_quotationforms';
            $this->quotations_model->_primary_key = 'quotationforms_id';
            $this->quotations_model->delete($id);
            $type = 'success';
            $message = lang('delete_quotation_form');
            set_message($type, $message);
            redirect('client/quotations/');
        } else {
            $data['sub_active'] = lang('quotations_form');
            $sub_view = 'quotations_form';
            //$sub_view = 'all_quotations';
            $data['active'] = 1;
        }
        $this->quotations_model->_table_name = 'tbl_quotationforms';
        $this->quotations_model->_order_by = 'quotationforms_id';
        $data['all_quatations'] = $this->quotations_model->get();

        $data['subview'] = $this->load->view('client/quotations/' . $sub_view, $data, TRUE);
        $this->load->view('client/_layout_main', $data);
    }

    public function quotations_details($quotations_id)
    {
        $data['title'] = 'View  Quatations Form';
        $data['page'] = lang('quotations');
        $data['sub_active'] = lang('quotations');
        $data['breadcrumbs'] = lang('quotations');
        $data['quotations_info'] = $this->quotations_model->check_by(array('quotations_id' => $quotations_id), 'tbl_quotations');
        $client_id = client_id();
        if ($client_id != $data['quotations_info']->client_id) {
            redirect('client/quotations');
        }
        $this->quotations_model->_table_name = 'tbl_quotation_details';
        $this->quotations_model->_order_by = 'quotations_id';
        $data['quotation_details'] = $this->quotations_model->get_by(array('quotations_id' => $quotations_id), FALSE);
        $data['subview'] = $this->load->view('client/quotations/quotation_details', $data, TRUE);
        $this->load->view('client/_layout_main', $data);
    }

    public function quotations_details_pdf($quotations_id)
    {
        $data['title'] = 'View  Quatations Form';
        $data['page'] = lang('quotations');
        $data['sub_active'] = lang('quotations');
        $data['quotations_info'] = $this->quotations_model->check_by(array('quotations_id' => $quotations_id), 'tbl_quotations');
        $client_id = client_id();
        if ($client_id != $data['quotations_info']->client_id) {
            redirect('client/quotations');
        }
        $this->quotations_model->_table_name = 'tbl_quotation_details';
        $this->quotations_model->_order_by = 'quotations_id';
        $data['quotation_details'] = $this->quotations_model->get_by(array('quotations_id' => $quotations_id), FALSE);
//        $data['subview'] = $this->load->view('admin/quotations/quotations_details_pdf', $data,true);
//        $this->load->view('admin/_layout_main', $data);

        $this->load->helper('dompdf');
        $viewfile = $this->load->view('client/quotations/quotations_details_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it($data['quotations_info']->quotations_form_title));
    }

    public function quotations_form_details($id)
    {

        $data['title'] = 'View  Quatations Form';
        $data['page'] = lang('quotations');
        $data['sub_active'] = lang('quotations_form');
        $data['quotationforms_info'] = $this->quotations_model->check_by(array('quotationforms_id' => $id), 'tbl_quotationforms');

        $form_data = json_decode($data['quotationforms_info']->quotationforms_code, true);

        $data['formbuilder_data'] = $form_data['fields'];

        $data['quotationforms_code'] = json_encode($form_data['fields']);

        $data['subview'] = $this->load->view('client/quotations/view_quotations_form', $data, TRUE);
        $this->load->view('client/_layout_main', $data);
    }

    public function add_form($id = NULL)
    {
        $data['quotationforms_title'] = $this->input->post('quotationforms_title', TRUE);
        $data['quotationforms_code'] = $this->input->post('quotationforms_code', TRUE);
        if (!empty($id)) {
            $data['quotationforms_status'] = $this->input->post('quotationforms_status', TRUE);
        }
        $data['quotations_created_by_id'] = $this->session->userdata('user_id');

        $this->quotations_model->_table_name = 'tbl_quotationforms';
        $this->quotations_model->_primary_key = 'quotationforms_id';
        $this->quotations_model->save($data, $id);
        $type = 'success';
        $message = lang('save_quotation_form');
        set_message($type, $message);
        redirect('client/quotations/');
    }

    public function set_quotations($quotationforms_id, $id = NULL)
    {
        $client_id = $this->session->userdata('client_id');

        $client_info = $this->quotations_model->check_by(array('client_id' => $client_id), 'tbl_client');

        $data = array('user_id' => $this->session->userdata('user_id'), 'client_id' => $client_id, 'name' => $client_info->name, 'email' => $client_info->email, 'mobile' => $client_info->mobile);

        $quotationforms_info = $this->quotations_model->check_by(array('quotationforms_id' => $quotationforms_id), 'tbl_quotationforms');
        $form_data = json_decode($quotationforms_info->quotationforms_code, true);
        $data['quotations_form_title'] = $quotationforms_info->quotationforms_title;
        $this->quotations_model->_table_name = 'tbl_quotations';
        $this->quotations_model->_primary_key = 'quotations_id';
        if (!empty($id)) {
            $quotations_id = $id;
            $this->quotations_model->save($data, $id);
        } else {
            $quotations_id = $this->quotations_model->save($data);
        }

        $formbuilder_data = $form_data['fields'];

        foreach ($formbuilder_data as $value) {
            if (!empty($value)) {

                $quotation_data['quotations_id'] = $quotations_id;
                $quotation_data['quotation_form_data'] = $value['label'];
                $q_data = $this->input->post($value['cid'], TRUE);
                if (is_array($q_data)) {
                    $quotation_data['quotation_data'] = serialize($q_data);
                } else {
                    $quotation_data['quotation_data'] = $q_data;
                }

                $this->quotations_model->_table_name = 'tbl_quotation_details';
                $this->quotations_model->_primary_key = 'quotation_details_id';
                $this->quotations_model->save($quotation_data);
            }
        }
        $notifyUser = array();
        $all_admin = $this->db->where(array('role_id' => 1, 'activated' => 1))->get('tbl_users')->result();
        foreach ($all_admin as $v_admin) {
            if (!empty($v_admin)) {
                array_push($notifyUser, $v_admin->user_id);
                add_notification(array(
                    'to_user_id' => $v_admin->user_id,
                    'icon' => 'paste',
                    'description' => 'not_quotation_response',
                    'link' => 'admin/quotations/quotations_details/' . $quotations_id,
                    'value' => $quotationforms_info->quotationforms_title,
                ));
            }
        }
        show_notification($notifyUser);

        $type = 'success';
        $message = lang('save_quotation_form');
        set_message($type, $message);
        redirect('client/quotations/');
    }

}
