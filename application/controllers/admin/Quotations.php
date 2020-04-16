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
class Quotations extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('quotations_model');
    }

    public function index($action = NULL, $id = NULL)
    {
        $data['title'] = lang('quotations');
        $data['page'] = lang('quotations');
        $data['sub_active'] = lang('quotations');
        if ($action == 'delete_quotations') {
            $this->quotations_model->_table_name = 'tbl_quotations';
            $this->quotations_model->_primary_key = 'quotations_id';
            $this->quotations_model->delete($id);
            $type = 'success';
            $message = lang('delete_quotation');
            set_message($type, $message);
            redirect('admin/quotations/');
        } else {

            $sub_view = 'all_quotations';
            //$sub_view = 'all_quotations';
            $data['active'] = 1;
        }
        $this->quotations_model->_table_name = 'tbl_quotations';
        $this->quotations_model->_order_by = 'quotations_id';
        $data['all_quatations'] = $this->quotations_model->get();

        $data['subview'] = $this->load->view('admin/quotations/' . $sub_view, $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function quotationsList()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_quotations';
            $this->datatables->column_order = array('quotations_date', 'quotations_amount');
            $this->datatables->column_search = array('quotations_date', 'quotations_amount');
            $this->datatables->order = array('quotations_id' => 'desc');

            // get all invoice
            $fetch_data = make_datatables();

            $data = array();

            foreach ($fetch_data as $_key => $v_quatations) {

                $action = null;
                $user_info = $this->quotations_model->check_by(array('user_id' => $v_quatations->user_id), 'tbl_users');
                if (!empty($user_info)) {
                    if ($user_info->role_id == 1) {
                        $user = '(' . lang('admin') . ')';
                    } elseif ($user_info->role_id == 3) {
                        $user = '(' . lang('staff') . ')';
                    } else {
                        $user = '(' . lang('client') . ')';
                    }
                } else {
                    $user = ' ';
                }

                $sub_array = array();

                $sub_array[] = '<a class="text-info" href="' . base_url() . 'admin/quotations/quotations_details/' . $v_quatations->quotations_id . '">' . $v_quatations->quotations_form_title . '</a>';

                $sub_array[] = $v_quatations->name;
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

                $sub_array[] = (!empty($user_info->username) ? $user_info->username : '-') . ' ' . $user;

                $action .= btn_view('admin/quotations/quotations_details/' . $v_quatations->quotations_id) . ' ';
                $action .= ajax_anchor(base_url("admin/quotations/index/delete_quotations/$v_quatations->quotations_id"), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';

                $sub_array[] = $action;
                $data[] = $sub_array;
            }

            render_table($data);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function quotations_form($action = NULL, $id = NULL)
    {
        $data['title'] = lang('quotations');
        $data['page'] = lang('quotations');
        $data['quotationforms_info'] = $this->quotations_model->check_by(array('quotationforms_id' => $id), 'tbl_quotationforms');
        if ($action == 'edit_quotations_form') {
            $data['sub_active'] = lang('quotations_form');
            $form_data = json_decode($data['quotationforms_info']->quotationforms_code, true);

            $data['formbuilder_data'] = $form_data['fields'];

            $data['quotationforms_code'] = json_encode($form_data['fields']);
            $sub_view = 'quotations_form_details';
            $data['active'] = 2;
        } elseif ($action == 'delete_quotations_form') {
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'quotations',
                'module_field_id' => $id,
                'activity' => ('activity_delete_quotations_form'),
                'icon' => 'fa-coffee',
                'value1' => $data['quotationforms_info']->quotationforms_title,
            );
            // Update into tbl_project
            $this->quotations_model->_table_name = "tbl_activities"; //table name
            $this->quotations_model->_primary_key = "activities_id";
            $this->quotations_model->save($activities);

            $this->quotations_model->_table_name = 'tbl_quotationforms';
            $this->quotations_model->_primary_key = 'quotationforms_id';
            $this->quotations_model->delete($id);
            $type = 'success';
            $message = lang('delete_quotation_form');
            set_message($type, $message);
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/quotations_form');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $data['sub_active'] = lang('quotations_form');
            $sub_view = 'quotations_form';
            //$sub_view = 'all_quotations';
            $data['active'] = 1;
        }
        $this->quotations_model->_table_name = 'tbl_quotationforms';
        $this->quotations_model->_order_by = 'quotationforms_id';
        $data['all_quatations'] = $this->quotations_model->get();

        $data['subview'] = $this->load->view('admin/quotations/' . $sub_view, $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function quotationsformList()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_quotationforms';
            $this->datatables->column_order = array('quotationforms_title', 'quotationforms_date_created');
            $this->datatables->column_search = array('quotationforms_title', 'quotationforms_date_created');
            $this->datatables->order = array('quotationforms_id' => 'desc');

            // get all invoice
            $fetch_data = make_datatables();

            $data = array();

            foreach ($fetch_data as $_key => $v_quatations) {

                $action = null;
                $sub_array = array();

                $sub_array[] = '<a class="text-info" href="' . base_url() . 'admin/quotations/quotations_form_details/' . $v_quatations->quotationforms_id . '">' . $v_quatations->quotationforms_title . '</a>';

                $sub_array[] = fullname($v_quatations->quotations_created_by_id);
                $sub_array[] = strftime(config_item('date_format'), strtotime($v_quatations->quotationforms_date_created));
                if ($v_quatations->quotationforms_status == 'enabled') {
                    $quotationforms_status = '<span class="label label-success"> ' . lang('enabled') . '</span>';
                } else {
                    $quotationforms_status = '<span class="label label-danger">' . lang('disabled') . '</span>';
                };
                $sub_array[] = $quotationforms_status;

                $action .= btn_view('admin/quotations/quotations_form/edit_quotations_form/' . $v_quatations->quotationforms_id) . ' ';
                $action .= ajax_anchor(base_url("admin/quotations/quotations_form/delete_quotations_form/$v_quatations->quotationforms_id"), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';

                $sub_array[] = $action;
                $data[] = $sub_array;
            }

            render_table($data);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function quotations_details($quotations_id)
    {
        $data['title'] = 'View  Quatations Form';
        $data['page'] = lang('quotations');
        $data['sub_active'] = lang('quotations');
        $data['quotations_info'] = $this->quotations_model->check_by(array('quotations_id' => $quotations_id), 'tbl_quotations');
        $this->quotations_model->_table_name = 'tbl_quotation_details';
        $this->quotations_model->_order_by = 'quotations_id';
        $data['quotation_details'] = $this->quotations_model->get_by(array('quotations_id' => $quotations_id), FALSE);
        $data['subview'] = $this->load->view('admin/quotations/quotation_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function quotations_details_pdf($quotations_id)
    {
        $data['title'] = 'View  Quatations Form';
        $data['page'] = lang('quotations');
        $data['sub_active'] = lang('quotations');
        $data['quotations_info'] = $this->quotations_model->check_by(array('quotations_id' => $quotations_id), 'tbl_quotations');
        $this->quotations_model->_table_name = 'tbl_quotation_details';
        $this->quotations_model->_order_by = 'quotations_id';
        $data['quotation_details'] = $this->quotations_model->get_by(array('quotations_id' => $quotations_id), FALSE);
//        $data['subview'] = $this->load->view('admin/quotations/quotations_details_pdf', $data,true);
//        $this->load->view('admin/_layout_main', $data);

        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/quotations/quotations_details_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it($data['quotations_info']->quotations_form_title));
    }

    public function set_price($quotations_id)
    {
        $data['quotations_id'] = $quotations_id;
        $data['quotations_info'] = $this->quotations_model->check_by(array('quotations_id' => $quotations_id), 'tbl_quotations');
        $data['subview'] = $this->load->view('admin/quotations/set_price', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function set_price_quotations($id)
    {
        $data = $this->quotations_model->array_from_post(array('quotations_amount', 'notes'));
        $qtation_info = $this->quotations_model->check_by(array('quotations_id' => $id), 'tbl_quotations');
        $client_info = $this->quotations_model->check_by(array('client_id' => $qtation_info->client_id), 'tbl_client');

        $send_mail = $this->input->post('send_email', TRUE);
        if ($send_mail == 'on') {
            $email_template = email_templates(array('email_group' => 'quotations_form'), $qtation_info->client_id);
            
            $message = $email_template->template_body;
            $subject = $email_template->subject;
            $client_name = str_replace("{CLIENT}", $client_info->name, $message);

            $Date = str_replace("{DATE}", date('Y-m-d'), $client_name);
            $Currency = str_replace("{CURRENCY}", client_currency($qtation_info->client_id), $Date);
            $Amount = str_replace("{AMOUNT}", $this->input->post('quotations_amount', true), $Currency);
            $Notes = str_replace("{NOTES}", $this->input->post('notes', true), $Amount);
            $link = str_replace("{QUOTATION LINK}", base_url() . 'client/quotations/quotations_details/' . $id, $Notes);
            $message = str_replace("{SITE_NAME}", config_item('company_name'), $link);

            $sdata['message'] = $message;
            $message = $this->load->view('email_template', $sdata, TRUE);


            $address = $client_info->email;
            $params['recipient'] = $address;
            $params['subject'] = '[ ' . config_item('company_name') . ' ]' . ' ' . $subject;
            $params['message'] = $message;
            $params['resourceed_file'] = '';
            $this->quotations_model->send_email($params);
        }
        if (!empty($client_info->primary_contact)) {
            $notifyUser = array($client_info->primary_contact);
        } else {
            $user_info = $this->quotations_model->check_by(array('company' => $client_info->client_id), 'tbl_account_details');
            if (!empty($user_info)) {
                $notifyUser = array($user_info->user_id);
            }
        }
        if (!empty($notifyUser)) {
            foreach ($notifyUser as $v_user) {
                if ($v_user != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $v_user,
                        'icon' => 'paste',
                        'description' => 'not_set_quotations_price',
                        'link' => 'client/quotations/quotations_details/' . $id,
                        'value' => $qtation_info->quotations_form_title . ' ' . lang('amount') . ' ' . display_money($this->input->post('quotations_amount', true), client_currency($qtation_info->client_id)),
                    ));
                }
            }
            show_notification($notifyUser);
        }

        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'quotations',
            'module_field_id' => $id,
            'activity' => ('activity_set_quotations_price'),
            'icon' => 'fa-coffee',
            'value1' => $qtation_info->quotations_form_title,
            'value2' => $client_info->name . '(' . display_money($this->input->post('quotations_amount', true), client_currency($qtation_info->client_id)) . ')',
        );
        // Update into tbl_project
        $this->quotations_model->_table_name = "tbl_activities"; //table name
        $this->quotations_model->_primary_key = "activities_id";
        $this->quotations_model->save($activities);

        $data['reviewer_id'] = $this->session->userdata('user_id');
        $data['reviewed_date'] = date('Y-m-d H:i:s');
        $data['quotations_status'] = 'completed';

        $this->quotations_model->_table_name = 'tbl_quotations';
        $this->quotations_model->_primary_key = 'quotations_id';
        $this->quotations_model->save($data, $id);
        $type = 'success';
        $message = lang('save_quotation_form');
        set_message($type, $message);
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('admin/quotations_form');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
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

        $data['subview'] = $this->load->view('admin/quotations/view_quotations_form', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function quotations_form_details_pdf($id)
    {
        $data['title'] = 'View  Quatations Form';
        $data['quotationforms_info'] = $this->quotations_model->check_by(array('quotationforms_id' => $id), 'tbl_quotationforms');
        $form_data = json_decode($data['quotationforms_info']->quotationforms_code, true);
        $data['formbuilder_data'] = $form_data['fields'];
        $data['quotationforms_code'] = json_encode($form_data['fields']);
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/quotations/quotations_form_details_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it($data['quotationforms_info']->quotationforms_title));
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
        if (!empty($id)) {
            $action = ('activity_update_quotation_form');
        } else {
            $action = ('activity_save_quotation_form');
        }
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'quotations',
            'module_field_id' => $id,
            'activity' => $action,
            'icon' => 'fa-coffee',
            'value1' => $data['quotationforms_title'],
        );
        // Update into tbl_project
        $this->quotations_model->_table_name = "tbl_activities"; //table name
        $this->quotations_model->_primary_key = "activities_id";
        $this->quotations_model->save($activities);

        $type = 'success';
        $message = lang('save_quotation_form');
        set_message($type, $message);
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('admin/quotations_form');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

}
