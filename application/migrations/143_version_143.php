<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_143 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_return_stock_payments` (
  `payments_id` int(11) NOT NULL AUTO_INCREMENT,
  `return_stock_id` int(11) DEFAULT NULL,
  `trans_id` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_method` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` longtext COLLATE utf8_unicode_ci,
  `currency` varchar(64) COLLATE utf8_unicode_ci DEFAULT 'USD',
  `notes` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `month_paid` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `year_paid` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `module` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `paid_to` int(11) DEFAULT NULL,
  `paid_by` int(11) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `account_id` int(11) NOT NULL DEFAULT '0' COMMENT 'account_id means tracking deduct from which account',
  PRIMARY KEY (`payments_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
        $this->db->query("INSERT INTO `tbl_online_payment` (`online_payment_id`, `gateway_name`, `icon`) VALUES (NULL, 'TapPayment', 'tappayment.jpg');");
        $this->db->query("ALTER TABLE `tbl_return_stock` CHANGE `supplier_id` `invoices_id` INT(11) NULL DEFAULT NULL;");
        $this->db->query("ALTER TABLE `tbl_return_stock` ADD `module` ENUM('client', 'supplier') NULL DEFAULT NULL AFTER `return_stock_id`, ADD `module_id` INT(11) NULL DEFAULT NULL AFTER `module`, ADD `main_status` VARCHAR(200) NULL DEFAULT NULL AFTER `module_id`;");
        $this->db->query("ALTER TABLE `tbl_return_stock_items` ADD `invoice_items_id` INT(11) NULL DEFAULT NULL AFTER `return_stock_id`;");
        $this->db->query("ALTER TABLE `tbl_invoices` ADD `allow_tapPayment` ENUM('Yes','No') NULL DEFAULT 'Yes' AFTER `allow_payumoney`;");
        $this->db->query("INSERT INTO `tbl_client_menu` (`menu_id`, `label`, `link`, `icon`, `parent`, `time`, `sort`, `status`) VALUES (NULL, 'refund_items', 'client/invoice/refund_itemslist', 'icon-share-alt', '0', CURRENT_TIMESTAMP, '6', '0');");
        $this->db->query("INSERT INTO `tbl_email_templates` (`email_templates_id`, `email_group`, `subject`, `template_body`) VALUES (NULL, 'invoice_item_refund_request', 'A new Refunded request recived for Invoice {REF}', '<p><strong>Hello </strong><br> <br> A new item refunded request received for Invoice {REF}.<br> <br> You can view the invoice online at:<br> <big><strong><a href=\"{LINK}\">View Refund Stock </a></strong></big><br> <br> Best Regards<br> <br> The {SITE_NAME} Team</p> ');");
        $this->db->query("UPDATE `tbl_config` SET `value` = '1.4.3' WHERE `tbl_config`.`config_key` = 'version';");
    }
}