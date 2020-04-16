<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *
 *
 * @package
 */
class Payumoney extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('invoice_model');
    }


    public function pay($invoice_id = null)
    {
        $data['title'] = lang('make_payment');
        $data['breadcrumbs'] = lang('make_payment');
        $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');
        $client_info = $this->db->where('client_id', $invoice_info->client_id)->get('tbl_client')->row();
        $invoice_due = $this->invoice_model->calculate_to('invoice_due', $invoice_id);
        if ($invoice_due <= 0) {
            $invoice_due = 0.00;
        }
        $data['key'] = config_item('payumoney_key');
        $posted = [];
        if ($this->input->post()) {
            $data['action_url'] = $this->payu_money_gateway->get_invoice_action_url();
            foreach ($this->input->post() as $key => $value) {
                $posted[$key] = $value;
            }
            $data['txnid'] = $posted['txnid'];
            $data['invoice_id'] = $posted['invoice_id'];
            $data['amount'] = $posted['amount'];
            $data['firstname'] = $posted['firstname'];
            $data['lastname'] = '';
            $data['email'] = $posted['email'];
            $data['phonenumber'] = $posted['phone'];
            $data['address'] = $posted['address'];
        } else {
            $data['txnid'] = $this->payu_money_gateway->gen_transaction_id();
            $data['action_url'] = $this->uri->uri_string();
            $data['amount'] = $invoice_due;
            $data['invoice_id'] = $invoice_id;
            $data['firstname'] = (!empty($client_info->name) ? $client_info->name : '');
            $data['lastname'] = '';
            $data['email'] = (!empty($client_info->email) ? $client_info->email : '');
            $data['phonenumber'] = (!empty($client_info->phone) ? $client_info->phone : '');
            $data['address'] = (!empty($client_info->address) ? $client_info->address : '');

        }
        $data['hash'] = '';

        // there is post request
        if (count($posted) > 0) {
            $data['hash'] = $this->payu_money_gateway->get_invoice_hash([
                'key' => $posted['key'],
                'txnid' => $posted['txnid'],
                'amount' => $posted['amount'],
                'productinfo' => $posted['productinfo'],
                'firstname' => $posted['firstname'],
                'email' => $posted['email'],
            ]);
        }

        $data['subview'] = $this->load->view('payment/payu_money', $data, true);
        $user_id = $this->session->userdata('user_id');
        if (!empty($user_id) && empty($front_end)) {
            $this->load->view('admin/_layout_main', $data); //page load
        } elseif (!empty($front_end)) {
            $this->load->view('admin/_layout_open', $data); //page load
        } else {
            $this->load->view('frontend/_layout_main', $data); //page load
        }
    }


    public
    function invPaymentSuccess()
    {

        $hashInfo = $this->payu_money_gateway->invoice_valid_hash($_POST);
        if (!$hashInfo) {
            set_message('error', lang('invalid_transaction'));
        } else {
            if ($hashInfo['status'] == 'success') {
                $result = $this->payu_money_gateway->addPayment($_POST['productinfo'], $_POST['amount'], $_POST['txnid'], '108');
                if ($result['type'] == 'success') {
                    set_message($result['type'], $result['message']);
                } else {
                    set_message($result['type'], $result['message']);
                }
            } else {
                set_message('warning', 'Thank You. Your transaction status is ' . $hashInfo['status']);
            }
        }
        $this->session->unset_userdata('input_info');
        $client_id = $this->session->userdata('client_id');
        $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $_POST['productinfo']), 'tbl_invoices');
        if(!empty($invoice_info)){
            if ($invoice_info->type == 'debit_note') {
                $url = 'client/invoice/debit_note/details/' . $_POST['productinfo'];
            } else {
                $url = 'client/invoice/manage_invoice/invoice_details/' . $_POST['productinfo'];
            }
        }else{
            $url = 'client/dashboard';
        }
        if (!empty($client_id)) {
            redirect($url);
        } else {
            redirect('frontend/view_invoice/' . url_encode($_POST['productinfo']));
        }
        if (!empty($client_id)) {
            redirect('client/dashboard');
        } else {
            redirect('frontend/view_invoice/' . url_encode($_POST['productinfo']));
        }
    }

    public
    function invPaymentFailure()
    {
        $hashInfo = $this->payu_money_gateway->invoice_valid_hash($_POST);
        if (!$hashInfo) {
            set_message('error', lang('invalid_transaction'));
        } else {
            if ($hashInfo['unmappedstatus'] != 'userCancelled') {
                set_message('error', $hashInfo['error_Message'] . ' - ' . $hashInfo['status']);
            }
        }
        $this->session->unset_userdata('input_info');
        $client_id = $this->session->userdata('client_id');
        if (!empty($client_id)) {
            redirect('client/dashboard');
        } else {
            redirect('frontend/view_invoice/' . url_encode($_POST['productinfo']));
        }
    }


}

////end