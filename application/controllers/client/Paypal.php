<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Paypal extends Client_Controller {

    function Paypal() {
        parent::__construct();
        $this->load->library('Paypal_Lib');
        $this->load->model('paypal_model');
        $this->load->model('invoice_model');
    }

    function index() {
        $this->session->set_flashdata('response_status', 'error');
        $this->session->set_flashdata('message', lang('paypal_canceled'));
        redirect('client');
    }

    function pay($invoice_id = NULL) {

        $invoice_info = $this->paypal_model->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');

        $invoice_due = $this->invoice_model->calculate_to('invoice_due', $invoice_id);
        if ($invoice_due <= 0) {
            $invoice_due = 0.00;
        }

        $data['invoice_info'] = array(
            'item_name' => $invoice_info->reference_no,
            'item_number' => $invoice_id,
            'currency' => $invoice_info->currency,
            'amount' => $invoice_due);

        if (config_item('paypal_live') == 'FALSE') {
            $paypalurl = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        } else {
            $paypalurl = 'https://www.paypal.com/cgi-bin/webscr';
        }
        $data['paypal_url'] = $paypalurl;

        $data['subview'] = $this->load->view('client/paypal/confimation_form', $data, FALSE);
        $this->load->view('client/_layout_modal', $data);
    }

    function cancel() {
        $this->session->set_flashdata('response_status', 'error');
        $this->session->set_flashdata('message', lang('paypal_canceled'));
        redirect('clients');
    }

    function success() {
        if ($_POST) {
            $this->session->set_flashdata('response_status', 'success');
            $this->session->set_flashdata('message', lang('payment_added_successfully'));
            redirect('clients');
        } else {
            $this->session->set_flashdata('response_status', 'error');
            $this->session->set_flashdata('message', 'Something went wrong please contact us if your Payment doesn\'t appear shortly');
            redirect('clients');
        }
    }

}

////end 