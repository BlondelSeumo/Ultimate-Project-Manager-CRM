<?php

defined('BASEPATH') or exit('No direct script access allowed');

use Omnipay\Omnipay;

require_once(APPPATH . 'third_party/omnipay/vendor/autoload.php');

class Stripe_gateway extends App_gateway
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
        $this->setId('stripe');

        /**
         * REQUIRED
         * Gateway name
         */
        $this->setName('Stripe Checkout');


        /**
         * REQUIRED
         * Hook gateway with other online payment modes
         */
//        add_action('before_add_online_payment_modes', [ $this, 'initMode' ]);
    }

    public function finish_invoice_payment($data)
    {
        $this->ci->load->library('stripe_core');
        $invoice_info = get_row('tbl_invoices', array('invoices_id' => $data['invoice_id']));

        $metadata = array(
            'invoice_id' => $data['invoice_id'],
            'amount' => $data['amount'],
        );

        $result = $this->ci->stripe_core->charge([
            'amount' => $data['amount'] * 100,
            'currency' => $invoice_info->currency,
            "card" => $_POST['stripeToken'],
            'metadata' => $metadata,
            'description' => 'Invoice ' . $invoice_info->reference_no . ' via Stripe ',
        ]);
        return $result;
    }
}
