<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_132 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $this->db->query("UPDATE `tbl_config` SET `value` = '1.3.2' WHERE `tbl_config`.`config_key` = 'version';");
        $result = $this->db->get('tbl_online_payment')->result();
        if (empty($result)) {
            $this->db->query("INSERT INTO `tbl_online_payment` (`online_payment_id`, `gateway_name`, `icon`) VALUES
(1, 'paypal', 'paypal.png'),
(2, 'Stripe', 'stripe.jpg'),
(3, '2checkout', '2checkout.jpg'),
(4, 'Authorize.net', 'Authorizenet.png'),
(5, 'CCAvenue', 'CCAvenue.jpg'),
(6, 'Braintree', 'Braintree.png'),
(7, 'Mollie', 'ideal_mollie.png'),
(8, 'PayUmoney', 'payumoney.jpg');");
        }
    }
}