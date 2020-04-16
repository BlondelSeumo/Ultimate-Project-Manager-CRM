<?php

defined('BASEPATH') or exit('No direct script access allowed');

use Omnipay\Omnipay;

require_once(APPPATH . 'third_party/omnipay/vendor/autoload.php');

class Mollie_gateway extends App_gateway
{
    public function __construct()
    {

        /**
         * Call App_gateway __construct function
         */
        parent::__construct();
        /**
         * REQUIRED
         * Gateway unique id
         * The ID must be alpha/alphanumeric
         */
        $this->setId('mollie');

        /**
         * REQUIRED
         * Gateway name
         */
        $this->setName('Mollie');


        /**
         * REQUIRED
         * Hook gateway with other online payment modes
         */
//        add_action('before_add_online_payment_modes', [ $this, 'initMode' ]);
    }

    public function invoice_payment($data)
    {
        $gateway = Omnipay::create('Mollie');
        $gateway->setApiKey(config_item('mollie_api_key'));

        $oResponse = $gateway->purchase([
            'amount' => number_format($data['amount'], config_item('decimal_separator'), '.', ''),
            'description' => 'Invoice Payment via Mollie' . $data['amount'],
            'returnUrl' => site_url('payment/mollie/verify_invoice_payment'),
            'notifyUrl' => site_url('payment/mollie/invoice_webhook'),
            'metadata' => [
                'order_id' => $data['invoices_id'],
            ],
        ])->send();
        $this->ci->session->set_userdata([
            'input_info' => $data,
            'token' => $oResponse->getTransactionReference(),
        ]);
        if ($oResponse->isRedirect()) {
            $oResponse->redirect();
        } elseif ($oResponse->isPending()) {
            $message = 'Pending, Reference: ' . $oResponse->getTransactionReference();
            set_message('error', $message);
        } else {
            $message = $oResponse->getMessage();
            set_message('error', $message);
        }
        $client_id = $this->ci->session->userdata('client_id');
        if (!empty($client_id)) {
            redirect('client/dashboard');
        } else {
            redirect('frontend/view_invoice/' . url_encode($data['invoices_id']));
        }
    }


    public function fetch_invoice_payment($data)
    {
        $gateway = Omnipay::create('Mollie');
        $gateway->setApiKey(config_item('mollie_api_key'));
        return $gateway->fetchTransaction([
            'transactionReference' => $data['transaction_id'],
        ])->send();
    }
}
