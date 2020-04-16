<?php

defined('BASEPATH') or exit('No direct script access allowed');

use Omnipay\Omnipay;

require_once(APPPATH . 'third_party/omnipay/vendor/autoload.php');

class Authorize_gateway extends App_gateway
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
        $this->setId('authorize_aim');

        /**
         * REQUIRED
         * Gateway name
         */
        $this->setName('Authorize.net AIM');
    }


    public function finish_invoice_payment($data)
    {
        if (config_item('aim_authorize_live') == 'TRUE') {
            $mode = 'FALSE';
        } else {
            $mode = 'TRUE';
        }
        $gateway = Omnipay::create('AuthorizeNet_AIM');
        $gateway->setApiLoginId(config_item('aim_api_login_id'));
        $gateway->setTransactionKey(config_item('aim_authorize_transaction_key'));
        $gateway->setTestMode($mode);
        $billing_data = [];

        $billing_data['billingCompany'] = config_item('company_name');
        $billing_data['billingAddress1'] = $this->ci->input->post('billingAddress1');
        $billing_data['billingName'] = $this->ci->input->post('billingName');
        $billing_data['billingCity'] = $this->ci->input->post('billingCity');
        $billing_data['billingState'] = $this->ci->input->post('billingState');
        $billing_data['billingPostcode'] = $this->ci->input->post('billingPostcode');
        $billing_data['billingCountry'] = $this->ci->input->post('billingCountry');

        $billing_data['number'] = $this->ci->input->post('ccNo');
        $billing_data['expiryMonth'] = $this->ci->input->post('expMonth');
        $billing_data['expiryYear'] = $this->ci->input->post('expYear');
        $billing_data['cvv'] = $this->ci->input->post('cvv');

        $requestData = [
            'amount' => number_format($data['amount'], config_item('decimal_separator'), '.', ''),
            'currency' => $data['currency'],
            'description' => 'Invoice Payment by Authorize.net' . $data['amount'],
            'transactionId' => $data['invoice_id'],
            'invoiceNumber' => $data['ref'],
            'card' => $billing_data,
        ];
        $oResponse = $gateway->purchase($requestData)->send();
        return $oResponse;
    }
}

