<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Estimates extends Client_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('estimates_model');
        $this->load->library('gst');

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

    public function index($action = NULL, $id = NULL, $item_id = NULL)
    {

        $data['page'] = lang('estimates');
        $data['breadcrumbs'] = lang('estimates');

        if (!empty($item_id)) {
            $data['item_info'] = $this->estimates_model->check_by(array('estimate_items_id' => $item_id), 'tbl_estimate_items');
        }
        if (!empty($id)) {
            // get all estimates info by id                
            $data['estimates_info'] = $this->estimates_model->check_by(array('estimates_id' => $id), 'tbl_estimates');
            if (empty($data['estimates_info'])) {
                redirect('client/estimates');
            }
            $client_id = client_id();
            if ($client_id != $data['estimates_info']->client_id) {
                redirect('client/estimates');
            }
        }
        if ($action == 'edit_estimates') {
            $data['active'] = 2;
        } else {
            $data['active'] = 1;
        }
        // get all client
        $this->estimates_model->_table_name = 'tbl_client';
        $this->estimates_model->_order_by = 'client_id';
        $data['all_client'] = $this->estimates_model->get();

        // get all client
        $this->estimates_model->_table_name = 'tbl_estimates';
        $this->estimates_model->_order_by = 'estimates_id';
        $data['all_estimates_info'] = $this->estimates_model->get_by(array('client_id' => $this->session->userdata('client_id')), FALSE);
        if ($action == 'estimates_details') {
            $data['title'] = "Estimates Details"; //Page title      
            $subview = 'estimates_details';
            if (empty($data['estimates_info'])) {
                set_message('error', 'No data Found');
                redirect('client/estimates');
            }
        } elseif ($action == 'estimates_history') {
            $data['title'] = "Estimates History"; //Page title      
            $subview = 'estimates_history';
        } elseif ($action == 'email_estimates') {
            $data['title'] = "Email Estimates"; //Page title      
            $subview = 'email_estimates';
            $data['editor'] = $this->data;
        } else {
            $data['title'] = "Estimates"; //Page title      
            $subview = 'estimates';
        }
        $user_id = $this->session->userdata('user_id');

        $user_info = $this->estimates_model->check_by(array('user_id' => $user_id), 'tbl_users');
        $data['role'] = $user_info->role_id;


        $data['subview'] = $this->load->view('client/estimates/' . $subview, $data, TRUE);
        $this->load->view('client/_layout_main', $data); //page load
    }

    public function estimatesList($filterBy = null)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_estimates';
            $this->datatables->column_order = array('reference_no', 'status', 'estimate_date', 'due_date');
            $this->datatables->column_search = array('reference_no', 'status', 'estimate_date', 'due_date');
            $this->datatables->order = array('estimates_id' => 'desc');

            $where = null;
            $where_in = null;
            $client_id = $this->session->userdata('client_id');
            if (empty($filterBy)) {
                $where = array('client_id' => $client_id, 'status !=' => 'draft');
            }
            if ($filterBy == 'last_month' || $filterBy == 'this_months') {
                if ($filterBy == 'last_month') {
                    $month = date('Y-m', strtotime('-1 months'));
                } else {
                    $month = date('Y-m');
                }
                $where = array('client_id' => $client_id, 'status !=' => 'draft', 'estimate_month' => $month);
            } else if ($filterBy == 'expired') {
                $where = array('client_id' => $client_id, 'status' => 'pending', 'UNIX_TIMESTAMP(due_date) <' => strtotime(date('Y-m-d')));
            } else if (strstr($filterBy, '_')) {
                $year = str_replace('_', '', $filterBy);
                $where = array('client_id' => $client_id, 'status !=' => 'draft', 'estimate_year' => $year);
            } else if (!empty($filterBy)) {
                $where = array('client_id' => $client_id, 'status !=' => 'draft', 'status' => $filterBy);
            }
            // get all estimate
            $fetch_data = $this->datatables->get_client_estimates($filterBy);

            $data = array();
            foreach ($fetch_data as $_key => $v_estimates) {
                $action = null;
                if ($v_estimates->status == 'pending') {
                    $label = "info";
                } elseif ($v_estimates->status == 'accepted') {
                    $label = "success";
                } else {
                    $label = "danger";
                }

                $sub_array = array();
                $name = null;
                $name .= '<a class="text-info" href="' . base_url() . 'client/estimates/index/estimates_details/' . $v_estimates->estimates_id . '">' . $v_estimates->reference_no . '</a>';
                if ($v_estimates->invoiced == 'Yes') {
                    $invoice_info = $this->db->where('invoices_id', $v_estimates->invoices_id)->get('tbl_invoices')->row();
                    if (!empty($invoice_info)) {
                        $name .= '<p class="text-sm m0 p0"><a class="text-success" href="' . base_url() . 'client/invoice/manage_invoice/invoice_details/' . $invoice_info->invoices_id . '">' . lang('invoiced') . '</a></p>';
                    }
                }
                $sub_array[] = $name;
                $sub_array[] = strftime(config_item('date_format'), strtotime($v_estimates->estimate_date));
                $overdue = null;
                if (strtotime($v_estimates->due_date) < strtotime(date('Y-m-d')) && $v_estimates->status == 'pending' || strtotime($v_estimates->due_date) < time() && $v_estimates->status == ('draft')) {
                    $overdue .= '<span class="label label-danger ">' . lang("expired") . '</span>';
                }
                $sub_array[] = strftime(config_item('date_format'), strtotime($v_estimates->due_date)) . ' ' . $overdue;

                $sub_array[] = display_money($this->estimates_model->estimate_calculation('total', $v_estimates->estimates_id), client_currency($v_estimates->client_id));
                $sub_array[] = "<span class='label label-" . $label . "'>" . lang($v_estimates->status) . "</span>";

                $change_status = null;
                $ch_url = base_url() . 'client/estimates/';
                $change_status .= '<div class="btn-group">
        <button class="btn btn-xs btn-default dropdown-toggle"
                data-toggle="dropdown">
                    ' . lang('change') . ' ' . lang('status') . '
            <span class="caret"></span></button>
        <ul class="dropdown-menu animated zoomIn">';
                $change_status .= '<li><a href="' . $ch_url . 'change_status/declined/' . $v_estimates->estimates_id . '">' . lang('declined') . '</a></li>';
                $change_status .= '<li><a href="' . $ch_url . 'change_status/accepted/' . $v_estimates->estimates_id . '">' . lang('accepted') . '</a></li>';
                $change_status .= '</ul></div>';
                $action .= $change_status;

                $sub_array[] = $action;
                $data[] = $sub_array;
            }

            render_table($data, $where);
        } else {
            redirect('client/dashboard');
        }
    }

    public function pdf_estimates($id)
    {
        $data['estimates_info'] = $this->estimates_model->check_by(array('estimates_id' => $id), 'tbl_estimates');
        if (empty($data['estimates_info'])) {
            redirect('client/estimates');
        }
        $client_id = client_id();
        if ($client_id != $data['estimates_info']->client_id) {
            redirect('client/estimates');
        }
        $data['title'] = "Estimates PDF"; //Page title
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('client/estimates/estimates_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it('Estimates  # ' . $data['estimates_info']->reference_no));
    }

    public function save_estimates($id = NULL)
    {
        $data = $this->estimates_model->array_from_post(array('reference_no', 'client_id', 'tax', 'discount'));
        $data['due_date'] = date('Y-m-d', strtotime($this->input->post('due_date', TRUE)));
        $data['notes'] = $this->input->post('notes', TRUE);
        $currency = $this->estimates_model->client_currency_symbol($data['client_id']);
        $data['currency'] = $currency->code;
        // get all client
        $this->estimates_model->_table_name = 'tbl_estimates';
        $this->estimates_model->_primary_key = 'estimates_id';
        if (!empty($id)) {
            $estimates_id = $id;
            $this->estimates_model->save($data, $id);
        } else {
            $estimates_id = $this->estimates_model->save($data);
        }
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'estimates',
            'module_field_id' => $estimates_id,
            'activity' => 'activity_estimates_created',
            'icon' => 'fa-circle-o',
            'value1' => $data['reference_no']
        );
        $this->estimates_model->_table_name = 'tbl_activities';
        $this->estimates_model->_primary_key = 'activities_id';
        $this->estimates_model->save($activity);
        // messages for user
        $type = "success";
        $message = lang('estimate_created');
        set_message($type, $message);
        redirect('client/estimates');
    }

    public function add_item($id = NULL)
    {

        $data = $this->estimates_model->array_from_post(array('estimates_id', 'item_order'));
        $quantity = $this->input->post('quantity', TRUE);
        $array_data = $this->estimates_model->array_from_post(array('item_name', 'item_desc', 'item_tax_rate', 'unit_cost'));

        foreach ($quantity as $key => $value) {
            $data['quantity'] = $value;
            $data['item_name'] = $array_data['item_name'][$key];
            $data['item_desc'] = $array_data['item_desc'][$key];
            $data['unit_cost'] = $array_data['unit_cost'][$key];
            $data['item_tax_rate'] = $array_data['item_tax_rate'][$key];
            $sub_total = $data['unit_cost'] * $data['quantity'];

            $data['item_tax_total'] = ($data['item_tax_rate'] / 100) * $sub_total;
            $data['total_cost'] = $sub_total + $data['item_tax_total'];


            // get all client
            $this->estimates_model->_table_name = 'tbl_estimate_items';
            $this->estimates_model->_primary_key = 'estimate_items_id';
            if (!empty($id)) {
                $estimate_items_id = $id;
                $this->estimates_model->save($data, $id);
            } else {
                $estimate_items_id = $this->estimates_model->save($data);
            }
        }
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'estimates',
            'module_field_id' => $estimate_items_id,
            'activity' => 'activity_estimates_items_added',
            'icon' => 'fa-circle-o',
            'value1' => $data['estimates_id']
        );
        $this->estimates_model->_table_name = 'tbl_activities';
        $this->estimates_model->_primary_key = 'activities_id';
        $this->estimates_model->save($activity);
        // messages for user
        $type = "success";
        $message = lang('estimate_item_save');
        set_message($type, $message);
        redirect('client/estimates/index/estimates_details/' . $data['estimates_id']);
    }

    public function change_status($action, $id)
    {
        $where = array('estimates_id' => $id);
        if ($action == 'hide') {
            $data = array('show_client' => 'No');
        } elseif ($action == 'declined') {
            $data = array('status' => 'declined');
        } elseif ($action == 'accepted') {
            $data = array('status' => 'accepted');
        } else {
            $data = array('show_client' => 'Yes');
        }
        $this->estimates_model->set_action($where, $data, 'tbl_estimates');
        // messages for user
        $type = "success";
        $message = lang('estimate_' . $action);
        set_message($type, $message);
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function delete($action, $estimates_id, $item_id = NULL)
    {
        if ($action == 'delete_item') {
            $this->estimates_model->_table_name = 'tbl_estimate_items';
            $this->estimates_model->_primary_key = 'estimate_items_id';
            $this->estimates_model->delete($item_id);
        } elseif ($action == 'delete_estimates') {
            $this->estimates_model->_table_name = 'tbl_estimate_items';
            $this->estimates_model->delete_multiple(array('estimates_id' => $estimates_id));

            $this->estimates_model->_table_name = 'tbl_estimates';
            $this->estimates_model->_primary_key = 'estimates_id';
            $this->estimates_model->delete($estimates_id);
        }
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'estimates',
            'module_field_id' => $estimates_id,
            'activity' => 'activity_' . $action,
            'icon' => 'fa-circle-o',
            'value1' => $action
        );

        $this->estimates_model->_table_name = 'tbl_activities';
        $this->estimates_model->_primary_key = 'activities_id';
        $this->estimates_model->save($activity);
        $type = 'success';

        if ($action == 'delete_item') {
            $text = lang('estimate_item_deleted');
            set_message($type, $text);
            redirect('client/estimates/index/estimates_details/' . $estimates_id);
        } else {
            $text = lang('estimate_deleted');
            set_message($type, $text);
            redirect('client/estimates');
        }
    }

    public function send_estimates_email($estimates_id)
    {

        $ref = $this->input->post('ref', TRUE);
        $subject = $this->input->post('subject', TRUE);
        $message = $this->input->post('message', TRUE);

        $client_name = str_replace("{CLIENT}", $this->input->post('client_name', TRUE), $message);
        $Ref = str_replace("{ESTIMATE_REF}", $ref, $client_name);
        $Amount = str_replace("{AMOUNT}", $this->input->post('amount', true), $Ref);
        $Currency = str_replace("{CURRENCY}", $this->input->post('currency', TRUE), $Amount);
        $link = str_replace("{ESTIMATE_LINK}", base_url() . 'admin/estimates/index/estimates_details/' . $estimates_id, $Currency);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $link);


        $this->send_email_estimates($estimates_id, $message, $subject); // Email estimates

        $data = array('emailed' => 'Yes', 'date_sent' => date("Y-m-d H:i:s", time()));

        $this->estimates_model->_table_name = 'tbl_estimates';
        $this->estimates_model->_primary_key = 'estimates_id';
        $this->estimates_model->save($data, $estimates_id);

        // Log Activity
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'estimates',
            'module_field_id' => $estimates_id,
            'activity' => 'activity_estimates_sent',
            'icon' => 'fa-envelope',
            'value1' => $ref
        );
        $this->estimates_model->_table_name = 'tbl_activities';
        $this->estimates_model->_primary_key = 'activities_id';
        $this->estimates_model->save($activity);

        $type = 'success';
        $text = lang('estimate_email_sent');
        set_message($type, $text);
        redirect('client/estimates/index/estimates_details/' . $estimates_id);
    }

    function send_email_estimates($estimates_id, $message, $subject)
    {
        $estimates_info = $this->estimates_model->check_by(array('estimates_id' => $estimates_id), 'tbl_estimates');
        $client_info = $this->estimates_model->check_by(array('client_id' => $estimates_info->client_id), 'tbl_client');

        $recipient = $client_info->email;

        $data['message'] = $message;

        $message = $this->load->view('email_template', $data, TRUE);


        $params = array(
            'recipient' => $recipient,
            'subject' => $subject,
            'message' => $message
        );
        $params['resourceed_file'] = '';
        $this->session->set_flashdata('param', $params);
        redirect('fomailer/send_email');
    }

    public function convert_to_invoice($id)
    {
        $estimates_info = $this->estimates_model->check_by(array('estimates_id' => $id), 'tbl_estimates');

        $ref = config_item('invoice_prefix') . filter_var($estimates_info->reference_no, FILTER_SANITIZE_NUMBER_INT);
        if (config_item('increment_invoice_number') == 'TRUE') {
            $ref = config_item('invoice_prefix') . $this->estimates_model->generate_invoice_number();
        }
        $invoice_data = array(
            'reference_no' => $ref,
            'client_id' => $estimates_info->client_id,
            'currency' => $estimates_info->currency,
            'due_date' => $estimates_info->due_date,
            'notes' => $estimates_info->notes,
            'tax' => $estimates_info->tax,
        );

        $this->estimates_model->_table_name = 'tbl_invoices';
        $this->estimates_model->_primary_key = 'invoices_id';
        $invoice_id = $this->estimates_model->save($invoice_data);


        $this->estimates_model->_table_name = 'tbl_estimate_items';
        $this->estimates_model->_order_by = 'estimates_id';
        $estimate_items = $this->estimates_model->get_by(array('estimates_id' => $id), FALSE);

        if (!empty($estimate_items)) {
            foreach ($estimate_items as $v_est_item) {
                $items_data = array(
                    'invoices_id' => $invoice_id,
                    'item_name' => $v_est_item->item_name,
                    'item_desc' => $v_est_item->item_desc,
                    'unit_cost' => $v_est_item->unit_cost,
                    'quantity' => $v_est_item->quantity,
                    'total_cost' => $v_est_item->total_cost,
                );
                $this->estimates_model->_table_name = 'tbl_items';
                $this->estimates_model->_primary_key = 'items_id';
                $this->estimates_model->save($items_data);
            }
        }

        // Log Activity
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'estimates',
            'module_field_id' => $id,
            'activity' => 'activity_estimate_convert_to_invoice',
            'icon' => 'fa-laptop',
            'value1' => $ref
        );
        $this->estimates_model->_table_name = 'tbl_activities';
        $this->estimates_model->_primary_key = 'activities_id';
        $this->estimates_model->save($activity);

        $data = array('invoiced' => 'Yes');

        $this->estimates_model->_table_name = 'tbl_estimates';
        $this->estimates_model->_primary_key = 'estimates_id';
        $this->estimates_model->save($data, $id);

        $type = 'success';
        $message = lang('estimate_invoiced');
        set_message($type, $message);
        redirect('client/estimates/index/estimates_details/' . $id);
    }

}
