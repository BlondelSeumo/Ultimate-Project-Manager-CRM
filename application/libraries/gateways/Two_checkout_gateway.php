<?php

defined('BASEPATH') or exit('No direct script access allowed');

use Omnipay\Omnipay;

require_once(APPPATH . 'third_party/omnipay/vendor/autoload.php');

class Two_checkout_gateway extends App_gateway
{
    private $required_address_line_2_country_codes = 'CHN, JPN, RUS';

    private $required_state_country_codes = ' ARG, AUS, BGR, CAN, CHN, CYP, EGY, FRA, IND, IDN, ITA, JPN, MYS, MEX, NLD, PAN, PHL, POL, ROU, RUS, SRB, SGP, ZAF, ESP, SWE, THA, TUR, GBR, USA';

    private $required_zip_code_country_codes = 'ARG, AUS, BGR, CAN, CHN, CYP, EGY, FRA, IND, IDN, ITA, JPN, MYS, MEX, NLD, PAN, PHL, POL, ROU, RUS, SRB, SGP, ZAF, ESP, SWE, THA, TUR, GBR, USA';

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
        $this->setId('two_checkout');

        /**
         * REQUIRED
         * Gateway name
         */
        $this->setName('2Checkout');


        /**
         * REQUIRED
         * Hook gateway with other online payment modes
         */
//        add_action('before_add_online_payment_modes', [ $this, 'initMode' ]);

        /**
         * Add ssl notice
         */
//        add_action('before_render_payment_gateway_settings', 'two_checkout_ssl_notice');

        $line_address_2_required = $this->required_address_line_2_country_codes;
        $this->required_address_line_2_country_codes = [];
        foreach (explode(', ', $line_address_2_required) as $cn_code) {
            array_push($this->required_address_line_2_country_codes, $cn_code);
        }
        $state_country_codes_required = $this->required_state_country_codes;
        $this->required_state_country_codes = [];
        foreach (explode(', ', $state_country_codes_required) as $cn_code) {
            array_push($this->required_state_country_codes, $cn_code);
        }
        $zip_code_country_codes_required = $this->required_zip_code_country_codes;
        $this->required_zip_code_country_codes = [];
        foreach (explode(', ', $zip_code_country_codes_required) as $cn_code) {
            array_push($this->required_zip_code_country_codes, $cn_code);
        }
    }


    public function finish_invoice_payment($data)
    {
        if (config_item('two_checkout_live') == 'TRUE') {
            $mode = 'FALSE';
        } else {
            $mode = 'TRUE';
        }
        $gateway = Omnipay::create('TwoCheckoutPlus_Token');
        $gateway->setAccountNumber(config_item('2checkout_seller_id'));
        $gateway->setPrivateKey(config_item('2checkout_private_key'));
        $gateway->setTestMode($mode);

        $billing_data = [];
        $billing_data['billingName'] = $this->ci->input->post('billingName');
        $billing_data['billingAddress1'] = $this->ci->input->post('billingAddress1');

        if ($this->ci->input->post('billingAddress2')) {
            $billing_data['billingAddress2'] = $this->ci->input->post('billingAddress2');
        }
        $billing_data['billingCity'] = $this->ci->input->post('billingCity');

        if ($this->ci->input->post('billingState')) {
            $billing_data['billingState'] = $this->ci->input->post('billingState');
        }
        if ($this->ci->input->post('billingPostcode')) {
            $billing_data['billingPostcode'] = $this->ci->input->post('billingPostcode');
        }
        $billing_data['billingCountry'] = $this->ci->input->post('billingCountry');
        $billing_data['email'] = $this->ci->input->post('email');

        $oResponse = $gateway->purchase([
            'amount' => number_format($data['amount'], config_item('decimal_separator'), '.', ''),
            'currency' => $data['currency'],
            'token' => $this->ci->input->post('token'),
            'transactionId' => $data['invoice_id'],
            'card' => $billing_data,
        ])->send();
        return $oResponse;
    }

    public function get_required_address_2_by_country_code()
    {
        return $this->required_address_line_2_country_codes;
    }

    public function get_required_state_by_country_code()
    {
        return $this->required_state_country_codes;
    }

    public function get_required_zip_by_country_code()
    {
        return $this->required_zip_code_country_codes;
    }
}


