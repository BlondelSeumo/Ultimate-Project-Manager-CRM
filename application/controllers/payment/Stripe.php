<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *
 *
 * @package
 */
class Stripe extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('invoice_model');
    }

    function pay($invoices_id = NULL)
    {
        $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $invoices_id), 'tbl_invoices');
        $invoice_due = $this->invoice_model->calculate_to('invoice_due', $invoices_id);
        if ($invoice_due <= 0) {
            $invoice_due = 0.00;
        }

        $data['title'] = lang('make_payment');
        $data['breadcrumbs'] = lang('stripe');
        $data['stripe'] = TRUE;
        $data['invoices_info'] = $invoice_info;
        $data['invoice_info'] = array(
            'item_name' => $invoice_info->reference_no,
            'item_number' => $invoices_id,
            'currency' => $invoice_info->currency,
            'allow_stripe' => $invoice_info->allow_stripe,
            'amount' => $invoice_due);

        if ($this->input->post()) {
            $data['post'] = true;
            $data['item_name'] = $invoice_info->reference_no;
            $data['amount'] = $this->input->post('amount', true);
            $data['currency'] = $invoice_info->currency;

        }
        if ($this->input->post()) {
            $data['subview'] = $this->load->view('payment/stripe', $data, true);
            $client_id = $this->session->userdata('client_id');
            if (!empty($client_id)) {
                $this->load->view('client/_layout_main', $data);
            } else {
                $this->load->view('frontend/_layout_main', $data);
            }
        } else {
            $data['subview'] = $this->load->view('payment/stripe', $data, FALSE);
            $this->load->view('client/_layout_modal', $data);
        }
    }

    public function purchase()
    {

        if ($this->input->post()) {
            $data = $this->input->post();
            try {
                $charge = $this->stripe_gateway->finish_invoice_payment($data);
                if ($charge->paid == true) {
                    $result = $this->stripe_gateway->addPayment($data['invoice_id'], $data['amount'],$charge->id, '103');
                    if ($result['type'] == 'success') {
                        set_message($result['type'], $result['message']);
                    } else {
                        set_message($result['type'], $result['message']);
                    }
                }
            } catch (Exception $e) {
                set_message('error', $e->getMessage());
            }
            if (!empty($client_id)) {
                redirect('client/dashboard');
            } else {
                redirect('frontend/view_invoice/' . url_encode($data['invoice_id']));
            }
        }
    }


}

////end