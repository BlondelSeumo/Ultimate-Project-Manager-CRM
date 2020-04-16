<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Paypal extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('invoice_model');
    }

    function index()
    {
        $this->session->set_flashdata('response_status', 'error');
        $this->session->set_flashdata('message', lang('paypal_canceled'));
        redirect('login');
    }

    function pay($invoice_id = NULL)
    {
        $data['breadcrumbs'] = lang('paypal');
        if ($this->input->post()) {
            $this->load->model('payments_model');
            $in_data = $this->input->post();
            $in_data['description'] = lang('paypal_redirection_alert') . ' ' . $in_data['amount'];
            $in_data['invoices_id'] = $invoice_id;
            $in_data['payment_method'] = 'paypal';
            $this->payments_model->invoice_payment($in_data);
            exit();
        } else {
            $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');
            $invoice_due = $this->invoice_model->calculate_to('invoice_due', $invoice_id);
            if ($invoice_due <= 0) {
                $invoice_due = 0.00;
            }
            $data['invoice_info'] = array(
                'item_name' => $invoice_info->reference_no,
                'item_number' => $invoice_id,
                'currency' => $invoice_info->currency,
                'amount' => $invoice_due);
            $data['paypal_url'] = $this->uri->uri_string();
            $data['subview'] = $this->load->view('payment/paypal', $data, FALSE);
            $this->load->view('client/_layout_modal', $data);
        }
    }

    public function complete_payment($invoice_id = null)
    {
        $input_data = $this->session->userdata('input_info');
        if (!empty($input_data)) {
            $reference_no = $this->session->userdata('reference_no');
            $cf = $input_data['payment_method'] . '_gateway';
            $paypalResponse = $this->$cf->complete_purchase([
                'token' => $reference_no,
                'amount' => $input_data['amount'],
                'currency' => $input_data['currency'],
            ]);
            // Check if error exists in the response
            if (isset($paypalResponse['L_ERRORCODE0'])) {
                set_message('error', $paypalResponse['L_SHORTMESSAGE0'] . '<br />' . $paypalResponse['L_LONGMESSAGE0']);
            } elseif (isset($paypalResponse['PAYMENTINFO_0_ACK']) && $paypalResponse['PAYMENTINFO_0_ACK'] === 'Success') {
                $this->session->unset_userdata('input_info');
                $this->session->unset_userdata('reference_no');

                $result = $this->paypal_gateway->addPayment($input_data['invoices_id'], $input_data['amount'], '101');
                if ($result['type'] == 'success') {
                    set_message($result['type'], $result['message']);
                } else {
                    set_message($result['type'], $result['message']);
                }
            } else {
                $type = 'error';
                $message = lang('please_select_payment_method');
                set_message($type, $message);
            }
        }
        $client_id = $this->session->userdata('client_id');
        if (!empty($client_id)) {
            redirect('client/dashboard');
        } else {
            redirect('frontend/view_invoice/' . url_encode($input_data['invoice_id']));
        }

    }


}

////end 