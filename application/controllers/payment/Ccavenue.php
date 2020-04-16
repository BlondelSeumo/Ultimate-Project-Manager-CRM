<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Ccavenue extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('invoice_model');
    }

    function pay($invoice_id = NULL)
    {
        $data['breadcrumbs'] = lang('ccavenue');
        $data['title'] = lang('make_payment');
        $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');

        $invoice_due = $this->invoice_model->calculate_to('invoice_due', $invoice_id);
        if ($invoice_due <= 0) {
            $invoice_due = 0.00;
        }

        $data['invoice_info'] = array(
            'item_name' => $invoice_info->reference_no,
            'item_number' => $invoice_id,
            'currency' => $invoice_info->currency,
            'invoices_id' => $invoice_id,
            'client_id' => $invoice_info->client_id,
            'amount' => $invoice_due);

        $data['access_code'] = config_item('ccavenue_access_code');
        $data['merchant_id'] = config_item('ccavenue_merchant_id');
        $data['working_key'] = config_item('ccavenue_key');
        $data['txnid'] = $this->ccavenue_gateway->gen_transaction_id();
        $posted = null;
        if ($this->input->post()) {
            $data['access_code'] = config_item('ccavenue_access_code');
            $data['action_url'] = $this->ccavenue_gateway->get_invoice_action_url();
            $input = $this->input->post();
            if (!empty($input)) {
                $input['amount'] = number_format($input['amount'], config_item('decimal_separator'), '.', '');
            }
            if ($input['currency'] != 'INR') {
                $input['currency'] = 'INR';
            }
            $this->load->view('payment/pay_ccavenue', $data);
        } else {
            $data['action_url'] = $this->uri->uri_string();
            $data['encrypted_data'] = '';
            $data['subview'] = $this->load->view('payment/ccavenue', $data, TRUE);
            $client_id = $this->session->userdata('client_id');
            if (!empty($client_id)) {
                $this->load->view('client/_layout_main', $data);
            } else {
                $this->load->view('frontend/_layout_main', $data);
            }
        }
    }

    public function confirm()
    {
        $data['title'] = lang('make_payment') . 'via' . lang('ccavenue');
        $data['subview'] = $this->load->view('payment/pay_ccavenue', $data, TRUE);
        $client_id = $this->session->userdata('client_id');
        if (!empty($client_id)) {
            $this->load->view('client/_layout_main', $data);
        } else {
            $this->load->view('frontend/_layout_main', $data);
        }
    }

    public function invoice_success()
    {
        $order_info = $this->ccavenue_gateway->get_invoice_order_status($_POST);
        if ($order_info['order_status'] == 'Success') {
            $result = $this->ccavenue_gateway->addPayment($order_info['order_id'], $order_info['amount'], $order_info['tracking_id'], '105');
            if ($result['type'] == 'success') {
                set_message($result['type'], $result['message']);
            } else {
                set_message($result['type'], $result['message']);
            }
        } else {
            set_message('error', 'Thank You. Your transaction status is ' . $order_info['order_status']);
        }
        $client_id = $this->session->userdata('client_id');
        if (!empty($client_id)) {
            redirect('client/dashboard');
        } else {
            redirect('frontend/view_invoice/' . url_encode($order_info['order_id']));
        }
    }

    public function invoice_failure()
    {

        $order_info = $this->ccavenue_gateway->get_invoice_order_status($_POST);
        if (!$order_info) {
            set_message('error', lang('invalid_transaction'));
        } else {
            set_message('error', 'Thank You. Your transaction status is ' . $order_info['order_status']);
        }
        $client_id = $this->session->userdata('client_id');
        if (!empty($client_id)) {
            redirect('client/dashboard');
        } else {
            redirect('frontend/view_invoice/' . url_encode($_POST['order_id']));
        }
    }

}