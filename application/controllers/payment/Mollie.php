<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Mollie extends My_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    function pay($invoice_id = NULL)
    {
        $data['breadcrumbs'] = lang('mollie');
        $data['title'] = lang('make_payment');
        $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');
        $allow_customer_edit_amount = config_item('allow_customer_edit_amount');

        $mdata['invoices_id'] = $invoice_id;
        $mdata['currency'] = $invoice_info->currency;

        $amount = $this->invoice_model->calculate_to('invoice_due', $invoice_info->invoices_id);
        if (!empty($allow_customer_edit_amount) && $allow_customer_edit_amount == 'No') {
            $mdata['amount'] = $amount;
            $this->mollie_gateway->invoice_payment($mdata);
        } else if (!empty($this->input->post('amount', true))) {
            $mdata['amount'] = $this->input->post('amount', true);
            $this->mollie_gateway->invoice_payment($mdata);
        }
        if (!empty($allow_customer_edit_amount) && $allow_customer_edit_amount == 'Yes') {
            $data['invoice_info'] = array(
                'item_name' => $invoice_info->reference_no,
                'item_number' => $invoice_id,
                'client_id' => $invoice_info->client_id,
                'currency' => $invoice_info->currency,
                'amount' => $amount);
            $data['subview'] = $this->load->view('payment/mollie', $data, FALSE);
            $this->load->view('client/_layout_modal', $data);
        }
    }

    public function verify_invoice_payment()
    {
        $input_data = $this->session->userdata('input_info');
        $token = $this->session->userdata('token');

        $oResponse = $this->mollie_gateway->fetch_payment([
            'transaction_id' => $token,
        ]);
        if ($oResponse->isSuccessful()) {
            $data = $oResponse->getData();
            if ($data['status'] == 'paid') {
                set_message('success', lang('online_payment_recorded_success'));
            }
        } else {
            set_message('error', $oResponse->getMessage());
        }
        $client_id = $this->session->userdata('client_id');
        if (!empty($client_id)) {
            redirect('client/dashboard');
        } else {
            redirect('frontend/view_invoice/' . url_encode($input_data['invoices_id']));
        }

    }

    public function invoice_webhook()
    {
        $data = $this->session->userdata('input_info');
        $ip = $this->input->ip_address();
        if (iPINRange($ip, '87.233.229.26-87.233.229.27')) {
            $trans_id = $this->input->post('id');
            $oResponse = $this->mollie_gateway->fetch_invoice_payment([
                'transaction_id' => $trans_id,
            ]);
            $data = $oResponse->getData();
            if ($data['status'] == 'paid') {
                $result = $this->mollie_gateway->addPayment($data['invoices_id'], $data['amount'], $trans_id, '107');
                if ($result['type'] == 'success') {
                    set_message($result['type'], $result['message']);
                } else {
                    set_message($result['type'], $result['message']);
                }
            } elseif ($data['status'] == 'refunded' || $data['status'] == 'cancelled' || $data['status'] == 'charged_back') {
//                $this->db->where('invoiceid', $data['metadata']['order_id']);
//                $this->db->where('transactionid', $trans_id);
//                $this->db->delete('tblinvoicepaymentrecords');
//                update_invoice_status($data['metadata']['order_id']);
            }
            header('HTTP/1.1 200 OK');
            $client_id = $this->session->userdata('client_id');
            if (!empty($client_id)) {
                redirect('client/dashboard');
            } else {
                redirect('frontend/view_invoice/' . url_encode($data['invoices_id']));
            }
        }
    }

}
