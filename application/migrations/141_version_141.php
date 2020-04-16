<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_141 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $this->db->query("ALTER TABLE `tbl_client` ADD `sms_notification` TINYINT(1) NULL DEFAULT NULL AFTER `active`;");
        $this->db->query("INSERT INTO `tbl_online_payment` (`online_payment_id`, `gateway_name`, `icon`) VALUES (NULL, 'Razorpay', 'razorpay.png');");
        $this->db->query("ALTER TABLE `tbl_invoices` ADD `allow_razorpay` ENUM('Yes','No') NULL DEFAULT 'No' AFTER `allow_payumoney`;");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('allow_apply_job_from_login', 'TRUE');");
        $this->db->query("UPDATE `tbl_config` SET `value` = '1.4.1' WHERE `tbl_config`.`config_key` = 'version';");
    }
}