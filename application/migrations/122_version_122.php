<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_122 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $this->db->query("UPDATE `tbl_config` SET `value` = '1.2.2' WHERE `tbl_config`.`config_key` = 'version';");
        if (empty($this->db->field_exists('alert_overdue', 'tbl_proposals'))) {
            $this->db->query("ALTER TABLE `tbl_proposals` ADD `alert_overdue` TINYINT(1) NULL DEFAULT '0' AFTER `due_date`;");
        }
        $this->db->query("ALTER TABLE `tbl_customer_group` CHANGE `description` `description` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");
        $this->db->query("INSERT INTO `tbl_dashboard` (`id`, `name`, `col`, `order_no`, `status`, `report`) VALUES (NULL, 'my_calendar', 'col-sm-6', '1', '1', '0');");
        $this->db->query("ALTER TABLE `tbl_employee_bank` ADD `routing_number` VARCHAR(50) NULL DEFAULT NULL AFTER `account_number`, ADD `type_of_account` VARCHAR(20) NULL DEFAULT NULL AFTER `routing_number`;");
        $this->db->query("ALTER TABLE `tbl_dashboard` ADD `for_staff` TINYINT(1) NULL DEFAULT '1' AFTER `report`;");
        $this->db->query("INSERT INTO `tbl_menu` (`menu_id`, `label`, `link`, `icon`, `parent`, `sort`, `time`, `status`) VALUES (NULL, 'transactions', '#', 'fa fa-building-o', '42', '0', CURRENT_TIMESTAMP, '1');");
        $this->db->query("INSERT INTO `tbl_menu` (`menu_id`, `label`, `link`, `icon`, `parent`, `sort`, `time`, `status`) VALUES (NULL, 'sales', 'admin/report/sales_report', 'fa fa-shopping-cart', '42', '0', CURRENT_TIMESTAMP, '1');");
        $this->db->query("UPDATE `tbl_menu` SET `parent` = '146' WHERE `tbl_menu`.`menu_id` = 43;");
        $this->db->query("UPDATE `tbl_menu` SET `parent` = '146' WHERE `tbl_menu`.`menu_id` = 44;");
        $this->db->query("UPDATE `tbl_menu` SET `parent` = '146' WHERE `tbl_menu`.`menu_id` = 45;");
        $this->db->query("UPDATE `tbl_menu` SET `parent` = '146' WHERE `tbl_menu`.`menu_id` = 46;");
        $this->db->query("UPDATE `tbl_menu` SET `parent` = '146' WHERE `tbl_menu`.`menu_id` = 47;");
        $this->db->query("UPDATE `tbl_menu` SET `parent` = '146' WHERE `tbl_menu`.`menu_id` = 48;");
        $this->db->query("UPDATE `tbl_menu` SET `parent` = '146' WHERE `tbl_menu`.`menu_id` = 49;");
        $this->db->query("UPDATE `tbl_menu` SET `parent` = '146' WHERE `tbl_menu`.`menu_id` = 50;");
        $this->db->query("UPDATE `tbl_menu` SET `parent` = '146' WHERE `tbl_menu`.`menu_id` = 53;");

    }
}