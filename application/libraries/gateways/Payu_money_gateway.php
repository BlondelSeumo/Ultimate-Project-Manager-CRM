<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Payu_money_gateway extends App_gateway
{
    protected $hash_sequence = 'key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10';

    protected $sandbox_url = 'https://test.payu.in/_payment';

    protected $production_url = 'https://secure.payu.in/_payment';

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
        $this->setId('payu_money');

        /**
         * REQUIRED
         * Gateway name
         */
        $this->setName('PayU Money');


    }


    public function get_invoice_action_url()
    {
        return config_item('payumoney_enable_test_mode') == 'TRUE' ? $this->sandbox_url : $this->production_url;
    }

    public function gen_transaction_id()
    {
        return substr(hash('sha256', mt_rand() . microtime()), 0, 20);
    }

    public function get_invoice_hash($posted)
    {
        $hash_sequence = $this->hash_sequence;
        $hash_vars_seq = explode('|', $hash_sequence);
        $hash_string = '';
        foreach ($hash_vars_seq as $hash_var) {
            $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
            $hash_string .= '|';
        }

        $hash_string .= config_item('payumoney_salt');

        $hash = strtolower(hash('sha512', $hash_string));

        return $hash;
    }
    public function invoice_valid_hash($posted)
    {
        $salt = config_item('payumoney_salt');

        $status = $posted['status'];
        $unmappedstatus = $posted['unmappedstatus'];

        $firstname = $posted['firstname'];
        $amount = $posted['amount'];
        $txnid = $posted['txnid'];
        $posted_hash = $posted['hash'];
        $key = $posted['key'];
        $productinfo = $posted['productinfo'];
        $email = $posted['email'];

        $transaction_mode = $posted['mode'];

        if (isset($posted['additionalCharges'])) {
            $additional_charges = $posted['additionalCharges'];
            $retHashSeq = $additional_charges . '|' . $salt . '|' . $status . '|||||||||||' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
        } else {
            $retHashSeq = $salt . '|' . $status . '|||||||||||' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
        }
        $hash = hash('sha512', $retHashSeq);

        if ($hash != $posted_hash) {
            return false;
        }
        return [
            'status' => $status,
            'unmappedstatus' => $unmappedstatus,
            'txnid' => $txnid,
            'amount' => $amount,
            'transaction_mode' => $transaction_mode,
            'error_Message' => $posted['error_Message'],
        ];
    }
}
