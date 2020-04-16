<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_150 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {

        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_credit_note` (
  `credit_note_id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `project_id` int(11) DEFAULT '0',
  `credit_note_date` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `credit_note_month` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `credit_note_year` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `currency` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'USD',
  `discount_percent` int(2) DEFAULT NULL,
  `notes` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `tax` int(11) NOT NULL DEFAULT '0',
  `total_tax` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `status` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'open',
  `date_saved` timestamp NOT NULL DEFAULT '2018-12-12 11:00:00',
  `emailed` varchar(11) DEFAULT NULL,
  `permission` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `client_visible` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'No',
  `discount_type` enum('before_tax','after_tax') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT 'sales agent',
  `adjustment` decimal(18,2) NOT NULL DEFAULT '0.00',
  `discount_total` decimal(18,2) NOT NULL DEFAULT '0.00',
  `show_quantity_as` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`credit_note_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_credit_note_items` (
  `credit_note_items_id` int(11) NOT NULL AUTO_INCREMENT,
  `credit_note_id` int(11) NOT NULL,
  `saved_items_id` int(11) DEFAULT '0',
  `item_tax_rate` decimal(10,2) NOT NULL DEFAULT '0.00',
  `item_tax_name` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `item_name` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'Item Name',
  `item_desc` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `unit_cost` decimal(10,2) DEFAULT '0.00',
  `quantity` decimal(10,2) DEFAULT '0.00',
  `item_tax_total` decimal(10,2) DEFAULT '0.00',
  `total_cost` decimal(10,2) DEFAULT '0.00',
  `date_saved` timestamp NOT NULL DEFAULT '2018-12-12 11:00:00',
  `unit` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `hsn_code` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `order` int(11) DEFAULT '0',
  PRIMARY KEY (`credit_note_items_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_credit_used` (
  `credit_used_id` int(11) NOT NULL AUTO_INCREMENT,
  `invoices_id` int(11) NOT NULL,
  `credit_note_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `date_applied` datetime NOT NULL,
  `amount` decimal(18,3) NOT NULL,
  `payments_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`credit_used_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $this->db->query("ALTER TABLE `tbl_transactions` ADD `create_date` DATETIME NULL DEFAULT NULL AFTER `date`;");
        $this->db->query("ALTER TABLE `tbl_task` ADD `transactions_id` INT(11) NULL DEFAULT NULL AFTER `sub_task_id`;");
        $this->db->query("ALTER TABLE `tbl_transactions` ADD `recurring_type` VARCHAR(20) NULL DEFAULT NULL AFTER `amount`, ADD `repeat_every` INT(11) NULL DEFAULT NULL AFTER `recurring_type`, ADD `recurring` ENUM('Yes','No') NULL DEFAULT NULL AFTER `repeat_every`, ADD `total_cycles` INT(11) NULL DEFAULT NULL AFTER `recurring`, ADD `done_cycles` INT(11) NULL DEFAULT NULL AFTER `total_cycles`, ADD `custom_recurring` TINYINT(1) NULL DEFAULT '0' AFTER `done_cycles`, ADD `last_recurring_date` DATE NULL DEFAULT NULL AFTER `custom_recurring`, ADD `recurring_from` INT(11) NULL DEFAULT NULL AFTER `last_recurring_date`;");
        $this->db->query("ALTER TABLE `tbl_form` ADD `table_id` VARCHAR(110) NULL DEFAULT NULL AFTER `tbl_name`;");
        $this->db->query("INSERT INTO `tbl_form` (`form_id`, `form_name`, `tbl_name`, `table_id`) VALUES (NULL, 'items', 'tbl_saved_items','saved_items_id'), (NULL, 'supplier', 'tbl_suppliers','supplier_id'), (NULL, 'purchases', 'tbl_purchases','purchase_id'), (NULL, 'Account', 'tbl_accounts','account_id');");
        $this->db->query("ALTER TABLE `tbl_custom_field` ADD `visible_for_client` VARCHAR(11) NULL DEFAULT NULL AFTER `visible_for_admin`;");

        $form_data = $this->db->where('table_id', NULL)->get('tbl_form')->result();
        foreach ($form_data as $v_form) {
            $filed_id = str_replace('tbl_', '', $v_form->tbl_name);
            $table_id = $filed_id . '_id';
            update('tbl_form', array('form_id' => $v_form->form_id), array('table_id' => $table_id));
        }
        $this->db->query("INSERT INTO `tbl_menu` (`menu_id`, `label`, `link`, `icon`, `parent`, `sort`, `time`, `status`) VALUES (NULL, 'credit_note', 'admin/credit_note', 'fa fa-circle-o', '12', '1', '2017-06-09 21:32:05', '1');");
        $this->db->query("INSERT INTO `tbl_menu` (`menu_id`, `label`, `link`, `icon`, `parent`, `sort`, `time`, `status`) VALUES (NULL, 'credit_note_settings', 'admin/settings/credit_note', 'fa fa-fw fa-money', '25', '0', '2017-04-25 00:38:46', '2');");
        $this->db->query("INSERT INTO `tbl_menu` (`menu_id`, `label`, `link`, `icon`, `parent`, `sort`, `time`, `status`) VALUES (NULL, 'proposals_settings', 'admin/settings/proposals', 'fa fa-fw fa-leaf', '25', '0', '2017-04-25 00:38:46', '2');");
        $this->db->query("INSERT INTO `tbl_form` (`form_id`, `form_name`, `tbl_name`, `table_id`) VALUES (NULL, 'credit_note', 'tbl_credit_note', 'credit_note_id');");
        $this->db->query("UPDATE `tbl_config` SET `value` = '1.5.0' WHERE `tbl_config`.`config_key` = 'version';");
    }
}