<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_138 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $this->db->query("UPDATE `tbl_menu` SET `label` = 'office_stock' WHERE `tbl_menu`.`menu_id` = 75;");
        $this->db->query("INSERT INTO `tbl_menu` (`menu_id`, `label`, `link`, `icon`, `parent`, `sort`, `time`, `status`) VALUES (NULL, 'stock', '#', 'fa fa-layers', '0', '8', '2018-11-15 11:22:20', '1');");
        $this->db->query("INSERT INTO `tbl_menu` (`menu_id`, `label`, `link`, `icon`, `parent`, `sort`, `time`, `status`) VALUES (NULL, 'supplier', 'admin/supplier', 'icon-briefcase', '150', '1', '2019-05-01 19:40:52', '1');");
        $this->db->query("INSERT INTO `tbl_menu` (`menu_id`, `label`, `link`, `icon`, `parent`, `sort`, `time`, `status`) VALUES (NULL, 'purchase', 'admin/purchase', 'icon-handbag', '150', '2', '2019-05-01 19:40:52', '1');");
        $this->db->query("INSERT INTO `tbl_menu` (`menu_id`, `label`, `link`, `icon`, `parent`, `sort`, `time`, `status`) VALUES (NULL, 'return_stock', 'admin/return_stock', 'icon-share-alt', '150', '3', '2019-05-01 19:40:52', '1');");
        $this->db->query("INSERT INTO `tbl_menu` (`menu_id`, `label`, `link`, `icon`, `parent`, `sort`, `time`, `status`) VALUES (NULL, 'purchase_payment', 'admin/purchase/all_payments', 'icon-credit-card', '150', '4', '2019-05-01 20:12:49', '1');");
        $this->db->query("UPDATE `tbl_menu` SET `parent` = '150' WHERE `tbl_menu`.`menu_id` = 39;");
        $this->db->query("UPDATE `tbl_menu` SET `sort` = '0' WHERE `tbl_menu`.`menu_id` = 39;");
        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_purchases` (
  `purchase_id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_id` int(11) DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `total` decimal(20,2) DEFAULT NULL,
  `update_stock` enum('Yes','No') DEFAULT 'Yes',
  `status` varchar(20) DEFAULT NULL,
  `emailed` enum('Yes','No') DEFAULT NULL,
  `date_sent` varchar(20) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `discount_type` enum('before_tax','after_tax') DEFAULT NULL,
  `discount_percent` decimal(10,2) DEFAULT NULL,
  `adjustment` decimal(18,2) DEFAULT NULL,
  `discount_total` decimal(18,2) DEFAULT NULL,
  `show_quantity_as` varchar(10) DEFAULT NULL,
  `permission` text,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total_tax` text,
  `tax` decimal(20,2) DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`purchase_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_purchase_items` (
  `items_id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_id` int(11) NOT NULL,
  `item_tax_rate` decimal(10,2) NOT NULL DEFAULT '0.00',
  `item_tax_name` text COLLATE utf8_unicode_ci,
  `item_tax_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `quantity` decimal(10,2) DEFAULT '0.00',
  `total_cost` decimal(10,2) DEFAULT '0.00',
  `item_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT 'Item Name',
  `item_desc` longtext COLLATE utf8_unicode_ci,
  `unit_cost` decimal(10,2) DEFAULT '0.00',
  `order` int(11) DEFAULT '0',
  `date_saved` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `unit` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hsn_code` text COLLATE utf8_unicode_ci,
  `saved_items_id` int(11) DEFAULT '0',
  PRIMARY KEY (`items_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_purchase_payments` (
  `payments_id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_id` int(11) DEFAULT NULL,
  `trans_id` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_method` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` longtext COLLATE utf8_unicode_ci,
  `currency` varchar(64) COLLATE utf8_unicode_ci DEFAULT 'USD',
  `notes` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `month_paid` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `year_paid` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `paid_to` int(11) NOT NULL,
  `paid_by` int(11) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `account_id` int(11) NOT NULL DEFAULT '0' COMMENT 'account_id means tracking deduct from which account',
  PRIMARY KEY (`payments_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_return_stock` (
  `return_stock_id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_id` int(11) DEFAULT NULL,
  `reference_no` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `total` decimal(20,2) DEFAULT NULL,
  `update_stock` enum('Yes','No') CHARACTER SET utf8 DEFAULT 'Yes',
  `status` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `emailed` enum('Yes','No') CHARACTER SET utf8 DEFAULT NULL,
  `date_sent` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `return_stock_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `discount_type` enum('before_tax','after_tax') CHARACTER SET utf8 DEFAULT NULL,
  `discount_percent` decimal(10,2) DEFAULT NULL,
  `adjustment` decimal(18,2) DEFAULT NULL,
  `discount_total` decimal(18,2) DEFAULT NULL,
  `show_quantity_as` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `permission` text CHARACTER SET utf8,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `total_tax` text CHARACTER SET utf8,
  `tax` decimal(20,2) DEFAULT NULL,
  `notes` text CHARACTER SET utf8,
  PRIMARY KEY (`return_stock_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_return_stock_items` (
  `items_id` int(11) NOT NULL AUTO_INCREMENT,
  `return_stock_id` int(11) NOT NULL,
  `item_tax_rate` decimal(10,2) NOT NULL DEFAULT '0.00',
  `item_tax_name` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `item_tax_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `quantity` decimal(10,2) DEFAULT '0.00',
  `total_cost` decimal(10,2) DEFAULT '0.00',
  `item_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'Item Name',
  `item_desc` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `unit_cost` decimal(10,2) DEFAULT '0.00',
  `order` int(11) DEFAULT '0',
  `date_saved` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `unit` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `hsn_code` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `saved_items_id` int(11) DEFAULT '0',
  PRIMARY KEY (`items_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_suppliers` (
  `supplier_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `address` text,
  `permission` text,
  PRIMARY KEY (`supplier_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
        $this->db->query("UPDATE `tbl_config` SET `value` = '1.3.8' WHERE `tbl_config`.`config_key` = 'version';");
    }
}