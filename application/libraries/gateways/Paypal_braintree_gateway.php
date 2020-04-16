<?php

defined('BASEPATH') or exit('No direct script access allowed');

use Omnipay\Omnipay;

require_once(APPPATH . 'third_party/omnipay/vendor/autoload.php');

class Paypal_braintree_gateway extends App_gateway
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
        $this->setId('paypal_braintree');

        /**
         * REQUIRED
         * Gateway name
         */
        $this->setName('Braintree');

    }

    public function fetch_invoice_payment($transaction_id)
    {
        if (config_item('braintree_live_or_sandbox') == 'TRUE') {
            $mode = '';
        } else {
            $mode = 'TRUE';
        }
        $gateway = Omnipay::create('Braintree');
        $gateway->setMerchantId(config_item('braintree_merchant_id'));
        $gateway->setPrivateKey(config_item('braintree_private_key'));
        $gateway->setPublicKey(config_item('braintree_public_key'));
        $gateway->setTestMode($mode);
        return $gateway->find(['transactionReference' => $transaction_id])->send();
    }


    public function generate_invoice_token()
    {
        if (config_item('braintree_live_or_sandbox') == 'TRUE') {
            $mode = '';
        } else {
            $mode = 'TRUE';
        }
        $gateway = Omnipay::create('Braintree');
        $gateway->setMerchantId(config_item('braintree_merchant_id'));
        $gateway->setPrivateKey(config_item('braintree_private_key'));
        $gateway->setPublicKey(config_item('braintree_public_key'));
        $gateway->setTestMode($mode);

        return $gateway->clientToken()->send()->getToken();
    }


    public function finish_invoice_payment($data)
    {
        // Process online for PayPal payment start
        if (config_item('braintree_live_or_sandbox') == 'TRUE') {
            $mode = '';
        } else {
            $mode = 'TRUE';
        }
        $gateway = Omnipay::create('Braintree');
        $gateway->setMerchantId(config_item('braintree_merchant_id'));
        $gateway->setPrivateKey(config_item('braintree_private_key'));
        $gateway->setPublicKey(config_item('braintree_public_key'));
        $gateway->setTestMode($mode);

        $response = $gateway->purchase([
            'amount' => number_format($data['amount'], config_item('decimal_separator'), '.', ''),
            'currency' => $data['currency'],
            'token' => $data['nonce'],
        ])->send();

        return $response;
    }
}
