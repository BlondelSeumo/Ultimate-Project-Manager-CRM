<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_115 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $this->db->query("ALTER TABLE `tbl_activities` CHANGE `value1` `value1` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL, CHANGE `value2` `value2` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;");
        $this->db->query("INSERT INTO `tbl_client_menu` (`menu_id`, `label`, `link`, `icon`, `parent`, `time`, `sort`, `status`) VALUES (NULL, 'proposals', 'client/proposals', 'fa fa-leaf', '0', '2017-07-21 18:21:08', '7', '1');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('mollie_api_key', 'test_tkjFqFF6fP92FDSwBDHpeCzBRMBQBD');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('mollie_partner_id', '3106644');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('mollie_status', 'active');");
        $this->db->query("UPDATE `tbl_config` SET `value` = 'a:1:{s:11:\"default_tax\";a:1:{i:0;s:1:\"3\";}}' WHERE `tbl_config`.`config_key` = 'default_tax';");

        $this->db->query("ALTER TABLE `tbl_customer_group` ADD `type` VARCHAR(100) NULL DEFAULT NULL COMMENT 'customer group,item group' AFTER `customer_group_id`;");


        $this->db->query("INSERT INTO `tbl_email_templates` (`email_templates_id`, `email_group`, `subject`, `template_body`) VALUES (NULL, 'proposal_email', 'New Proposal', '<p>Proposal <strong>{PROPOSAL_REF}</strong></p> <p>Hi <strong>{CLIENT}</strong></p> <p>Thanks for your business inquiry.</p> <p>The Proposal <strong>{PROPOSAL_REF} </strong>is attached with this email.<br /> Proposal&nbsp;Overview:<br /> Proposal&nbsp;# :<strong> {PROPOSAL_REF}</strong><br /> Amount: <strong>{CURRENCY} {AMOUNT}</strong><br /> <br /> You can view the estimate online at:<br /> <big><strong><a href=\"{PROPOSAL_LINK}\">View Proposal</a></strong></big><br /> <br /> Best Regards,<br /> The <strong>{SITE_NAME}</strong> Team</p> ');");

        $this->db->query("ALTER TABLE `tbl_estimates` ADD `estimate_date` VARCHAR(50) NULL DEFAULT NULL AFTER `project_id`, ADD `estimate_month` VARCHAR(20) NULL DEFAULT NULL AFTER `estimate_date`, ADD `estimate_year` VARCHAR(10) NULL DEFAULT NULL AFTER `estimate_month`;");
        $this->db->query("ALTER TABLE `tbl_estimates` CHANGE `discount` `discount_percent` INT(2) NULL DEFAULT NULL;");
        $this->db->query("ALTER TABLE `tbl_estimates` ADD `invoices_id` INT(11) NOT NULL DEFAULT '0' AFTER `invoiced`;");
        $this->db->query("ALTER TABLE `tbl_estimates` ADD `discount_type` ENUM('before_tax','after_tax') NOT NULL AFTER `client_visible`, ADD `user_id` INT(11) NOT NULL DEFAULT '0' COMMENT 'sales agent' AFTER `discount_type`, ADD `adjustment` DECIMAL(18,2) NOT NULL DEFAULT '0.00' AFTER `user_id`;");
        $this->db->query("ALTER TABLE `tbl_estimates` ADD `discount_total` DECIMAL(18,2) NOT NULL DEFAULT '0.00' AFTER `adjustment`, ADD `show_quantity_as` VARCHAR(20) NOT NULL AFTER `discount_total`;");
        $this->db->query("ALTER TABLE `tbl_estimates` ADD `total_tax` TEXT NULL DEFAULT NULL AFTER `tax`;");

        $this->db->query("ALTER TABLE `tbl_estimate_items` ADD `unit` VARCHAR(200) NULL DEFAULT NULL AFTER `date_saved`;");
        $this->db->query("ALTER TABLE `tbl_estimate_items` CHANGE `item_order` `order` INT(11) NULL DEFAULT '0';");
        $this->db->query("ALTER TABLE `tbl_estimate_items` ADD `item_tax_name` TEXT NULL DEFAULT NULL AFTER `item_tax_rate`;");

        $this->db->query("INSERT INTO `tbl_form` (`form_id`, `form_name`, `tbl_name`) VALUES (NULL, 'proposal', 'tbl_proposals');");


        $this->db->query("ALTER TABLE `tbl_invoices` ADD `invoice_date` VARCHAR(50) NULL DEFAULT NULL AFTER `project_id`, ADD `invoice_month` VARCHAR(20) NULL DEFAULT NULL AFTER `invoice_date`, ADD `invoice_year` VARCHAR(10) NULL DEFAULT NULL AFTER `invoice_month`;");
        $this->db->query("ALTER TABLE `tbl_invoices` ADD `discount_type` ENUM('before_tax','after_tax') NOT NULL AFTER `client_visible`, ADD `user_id` INT(11) NOT NULL DEFAULT '0' COMMENT 'sales agent' AFTER `discount_type`, ADD `adjustment` DECIMAL(18,2) NOT NULL DEFAULT '0.00' AFTER `user_id`;");
        $this->db->query("ALTER TABLE `tbl_invoices` CHANGE `discount` `discount_percent` INT(2) NULL DEFAULT NULL;");
        $this->db->query("ALTER TABLE `tbl_invoices` ADD `discount_total` DECIMAL(18,2) NOT NULL DEFAULT '0.00' AFTER `adjustment`, ADD `show_quantity_as` VARCHAR(20) NOT NULL AFTER `discount_total`;");
        $this->db->query("ALTER TABLE `tbl_invoices` ADD `total_tax` TEXT NULL DEFAULT NULL AFTER `tax`;");

        $this->db->query("ALTER TABLE `tbl_items` ADD `unit` VARCHAR(200) NULL DEFAULT NULL AFTER `date_saved`;");
        $this->db->query("ALTER TABLE `tbl_items` CHANGE `item_order` `order` INT(11) NULL DEFAULT '0';");
        $this->db->query("ALTER TABLE `tbl_items` ADD `item_tax_name` TEXT NULL DEFAULT NULL AFTER `item_tax_rate`;");

        $this->db->query("INSERT INTO `tbl_menu` (`menu_id`, `label`, `link`, `icon`, `parent`, `sort`, `time`, `status`) VALUES (NULL, 'proposals', 'admin/proposals', 'fa fa-circle-o', '12', '3', '2017-07-16 11:04:15', '1');");

        $this->db->query("INSERT INTO `tbl_online_payment` (`online_payment_id`, `gateway_name`, `icon`) VALUES (NULL, 'Mollie', 'ideal_mollie.png');");


        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_proposals` (
  `proposals_id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subject` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `module` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `module_id` int(11) DEFAULT '0',
  `proposal_date` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `proposal_month` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `proposal_year` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `due_date` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `currency` varchar(32) COLLATE utf8_unicode_ci DEFAULT 'USD',
  `notes` text COLLATE utf8_unicode_ci NOT NULL,
  `tax` int(11) NOT NULL DEFAULT '0',
  `total_tax` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'draft',
  `date_sent` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `proposal_deleted` enum('Yes','No') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'No',
  `emailed` enum('Yes','No') COLLATE utf8_unicode_ci DEFAULT 'No',
  `show_client` enum('Yes','No') COLLATE utf8_unicode_ci DEFAULT 'No',
  `convert` enum('Yes','No') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'No',
  `convert_module` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `convert_module_id` int(11) DEFAULT '0',
  `converted_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `permission` text COLLATE utf8_unicode_ci,
  `discount_type` enum('before_tax','after_tax') COLLATE utf8_unicode_ci DEFAULT NULL,
  `discount_percent` int(2) NOT NULL DEFAULT '0',
  `discount_total` decimal(18,2) NOT NULL DEFAULT '0.00',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `adjustment` decimal(18,2) NOT NULL DEFAULT '0.00',
  `show_quantity_as` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `allowed_cmments` enum('Yes','No') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Yes',
  PRIMARY KEY (`proposals_id`),
  UNIQUE KEY `reference_no` (`reference_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_proposals_items` (
  `proposals_items_id` int(11) NOT NULL AUTO_INCREMENT,
  `proposals_id` int(11) NOT NULL,
  `item_name` varchar(150) COLLATE utf8_unicode_ci DEFAULT 'Item Name',
  `item_desc` longtext COLLATE utf8_unicode_ci,
  `quantity` decimal(10,2) DEFAULT '0.00',
  `unit_cost` decimal(10,2) DEFAULT '0.00',
  `item_tax_rate` decimal(10,2) NOT NULL DEFAULT '0.00',
  `item_tax_name` text COLLATE utf8_unicode_ci,
  `item_tax_total` decimal(10,2) DEFAULT '0.00',
  `total_cost` decimal(10,2) DEFAULT '0.00',
  `date_saved` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `order` int(11) DEFAULT '0',
  `unit` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`proposals_items_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("ALTER TABLE `tbl_saved_items` ADD `tax_rates_id` TEXT NULL DEFAULT NULL AFTER `unit_cost`;");
        $this->db->query("ALTER TABLE `tbl_saved_items` ADD `customer_group_id` INT(11) NOT NULL DEFAULT '0' AFTER `unit_cost`, ADD `unit_type` VARCHAR(200) NULL DEFAULT NULL AFTER `customer_group_id`;");

        $this->db->query("ALTER TABLE `tbl_task_comment` ADD `comments_reply_id` INT(11) NOT NULL DEFAULT '0'");

    }
}