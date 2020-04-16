<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . '/libraries/Ccavenue.php';

class Ccavenue_gateway extends App_gateway
{

    protected $sandbox_url = 'https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction';

    protected $production_url = 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction';
    protected $working_key = '';
    protected $merchant_id = '';
    protected $access_code = '';


    public function get_invoice_action_url()
    {
        return config_item('ccavenue_enable_test_mode') == 'TRUE' ? $this->sandbox_url : $this->production_url;
    }

    public function gen_transaction_id()
    {
        return time();
    }

    public function encrypted_data($data)
    {
        $working_key = config_item('ccavenue_key');
        $encrypted_data = encrypt_cc($data, $working_key); // Method for encrypting the data.
        return $encrypted_data;
    }

    public function shippingSameAsBilling($data)
    {
        $result['delivery_name'] = $data['billing_name'];
        $result['delivery_address'] = $data['billing_address'];
        $result['delivery_city'] = $data['billing_city'];
        $result['delivery_state'] = $data['billing_state'];
        $result['delivery_zip'] = $data['billing_zip'];
        $result['delivery_tel'] = $data['billing_tel'];
        $result['delivery_country'] = $data['billing_country'];
        return $result;
    }


    public function get_invoice_order_status($data)
    {
        $workingKey = config_item('ccavenue_key');        //Working Key should be provided here.
        $encResponse = $data["encResp"];            //This is the response sent by the CCAvenue Server
        $rcvdString = decrypt_cc($encResponse, $workingKey);        //Crypto Decryption used as per the specified working key.
        $result = "";
        $decryptValues = explode('&', $rcvdString);
        $dataSize = sizeof($decryptValues);
        for ($i = 0; $i < $dataSize; $i++) {
            $information = explode('=', $decryptValues[$i]);
            if ($i == 3) $order_status = $information[1];
        }
        return $order_status;

        for ($i = 0; $i < $dataSize; $i++) {
            $information = explode('=', $decryptValues[$i]);
            if ($i == 0) {
                $result['order_id'] = $information[1];
            };
            if ($i == 1) {
                $result['tracking_id'] = $information[1];
            };
            if ($i == 3) {
                $result['order_status'] = $information[1];
            };
            if ($i == 10) {
                $result['amount'] = $information[1];
            };
        }
        return $result;
    }

}