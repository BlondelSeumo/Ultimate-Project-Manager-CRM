<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_131 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $this->db->query("UPDATE `tbl_config` SET `value` = '1.3.1' WHERE `tbl_config`.`config_key` = 'version';");
        $result = $this->db->get('tbl_online_payment');
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
        $this->db->query("ALTER TABLE `tbl_draft` CHANGE `attach_file` `attach_file` TEXT NULL DEFAULT NULL;");
        $this->db->query("ALTER TABLE `tbl_inbox` CHANGE `attach_file` `attach_file` TEXT NULL DEFAULT NULL;");
        $this->db->query("ALTER TABLE `tbl_sent` CHANGE `attach_file` `attach_file` TEXT NULL DEFAULT NULL;");
        $this->db->query("INSERT INTO `tbl_email_templates` (`email_templates_id`, `email_group`, `subject`, `template_body`) VALUES (NULL, 'ticket_reopened_email', 'Ticket [SUBJECT] reopened', '<p>Ticket re-opened</p> <p>Hi {RECIPIENT},</p> <p>Ticket&nbsp;<strong>{SUBJECT}</strong>&nbsp;was re-opened by&nbsp;<strong>{USER}</strong>.<br /> Status :&nbsp;Open<br /> Click on the below link to see the ticket details and post replies:&nbsp;<br /> <a href=\"{TICKET_LINK}\"><strong>View Ticket</strong></a><br /> <br /> <br /> Best Regards,<br /> {SITE_NAME}</p> ');");
    }
}