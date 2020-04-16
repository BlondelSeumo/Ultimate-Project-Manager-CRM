<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *
 *
 * @package
 */
class Razorpay extends MY_Controller
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
        $data['breadcrumbs'] = lang('razorpay');
        $data['stripe'] = TRUE;
        $data['invoices_info'] = $invoice_info;
        $data['invoice_info'] = array(
            'item_name' => $invoice_info->reference_no,
            'item_number' => $invoices_id,
            'currency' => $invoice_info->currency,
            'allow_stripe' => $invoice_info->allow_stripe,
            'amount' => $invoice_due);
        if ($this->input->post()) {
            $data['subview'] = $this->load->view('payment/razorpay', $data, true);
            $client_id = $this->session->userdata('client_id');
            if (!empty($client_id)) {
                $this->load->view('client/_layout_main', $data);
            } else {
                $this->load->view('frontend/_layout_main', $data);
            }
        } else {
            $data['subview'] = $this->load->view('payment/razorpay', $data, FALSE);
            $this->load->view('client/_layout_modal', $data);
        }
    }


    public function invoice_success()
    {
        $invoice_id = $this->input->post('invoice_id', true);
        if ($this->input->post()) {
            $totalAmount = $this->input->post('totalAmount', true);
            $razorpay_payment_id = $this->input->post('razorpay_payment_id', true);
            $result = $this->ccavenue_gateway->addPayment($invoice_id, $totalAmount, $razorpay_payment_id, '106');
            $result = array(
                'status' => $result['type'],
                'message' => $result['message'],
                'client_id' => $this->session->userdata('client_id'),
            );
            echo json_encode($result);
            exit();
        }
        $client_id = $this->session->userdata('client_id');
        if (!empty($client_id)) {
            redirect('client/dashboard');
        } else {
            redirect('frontend/view_invoice/' . url_encode($invoice_id));
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


    public function purchase()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            try {
                $charge = $this->stripe_gateway->finish_invoice_payment($data);
                if ($charge->paid == true) {
                    $result = $this->stripe_gateway->addPayment($data['invoice_id'], $data['amount'], $charge->id, '103');
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