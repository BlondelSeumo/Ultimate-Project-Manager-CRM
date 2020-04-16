<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_129 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $this->db->query("UPDATE `tbl_config` SET `value` = '1.2.9' WHERE `tbl_config`.`config_key` = 'version';");
        $this->db->query("ALTER TABLE `tbl_bug` ADD `severity` VARCHAR(20) NULL DEFAULT NULL AFTER `priority`, ADD `reproducibility` TEXT NULL DEFAULT NULL AFTER `severity`;");
        $this->db->query("ALTER TABLE `tbl_bug` ADD `issue_no` VARCHAR(50) NULL DEFAULT NULL AFTER `bug_id`;");
        $this->db->query("UPDATE `tbl_menu` SET `link` = 'admin/tickets' WHERE `tbl_menu`.`menu_id` = 6;");
        $this->db->query("DELETE FROM `tbl_menu` WHERE `tbl_menu`.`menu_id` = 7;");
        $this->db->query("DELETE FROM `tbl_menu` WHERE `tbl_menu`.`menu_id` = 8;");
        $this->db->query("DELETE FROM `tbl_menu` WHERE `tbl_menu`.`menu_id` = 9;");
        $this->db->query("DELETE FROM `tbl_menu` WHERE `tbl_menu`.`menu_id` = 10;");
        $this->db->query("DELETE FROM `tbl_menu` WHERE `tbl_menu`.`menu_id` = 11;");
        $this->db->query("ALTER TABLE `tbl_items` CHANGE `item_tax_rate` `item_tax_rate` DECIMAL(18,2) NOT NULL DEFAULT '0.00', CHANGE `item_tax_total` `item_tax_total` DECIMAL(18,2) NOT NULL DEFAULT '0.00', CHANGE `quantity` `quantity` DECIMAL(18,2) NULL DEFAULT '0.00', CHANGE `total_cost` `total_cost` DECIMAL(18,2) NULL DEFAULT '0.00', CHANGE `unit_cost` `unit_cost` DECIMAL(18,2) NULL DEFAULT '0.00';");
        $this->db->query("ALTER TABLE `tbl_user_role` CHANGE `user_role_id` `user_role_id` INT(11) NOT NULL AUTO_INCREMENT, CHANGE `designations_id` `designations_id` INT(11) NULL DEFAULT NULL, CHANGE `menu_id` `menu_id` INT(11) NOT NULL, CHANGE `view` `view` INT(11) NULL DEFAULT '0', CHANGE `created` `created` INT(11) NULL DEFAULT '0', CHANGE `edited` `edited` INT(11) NULL DEFAULT '0', CHANGE `deleted` `deleted` INT(11) NULL DEFAULT '0';");
        $this->db->query("UPDATE `tbl_languages` SET `icon` = 'de' WHERE `tbl_languages`.`code` = 'de';");
        $this->db->query("UPDATE `tbl_languages` SET `icon` = 'tr' WHERE `tbl_languages`.`code` = 'tr';");
        $this->db->query("UPDATE `tbl_languages` SET `icon` = 'es' WHERE `tbl_languages`.`code` = 'es';");
        $this->db->query("UPDATE `tbl_languages` SET `icon` = 'gr' WHERE `tbl_languages`.`code` = 'el';");
        $this->db->query("UPDATE `tbl_languages` SET `icon` = 'es' WHERE `tbl_languages`.`code` = 'es';");
        $this->db->query("UPDATE `tbl_languages` SET `icon` = 'fr' WHERE `tbl_languages`.`code` = 'fr';");
        $this->db->query("UPDATE `tbl_languages` SET `icon` = 'fr' WHERE `tbl_languages`.`code` = 'fr';");
        $this->db->query("UPDATE `tbl_languages` SET `icon` = 'it' WHERE `tbl_languages`.`code` = 'it';");
        $this->db->query("UPDATE `tbl_languages` SET `icon` = 'nl' WHERE `tbl_languages`.`code` = 'nl';");
        $this->db->query("UPDATE `tbl_languages` SET `icon` = 'pt' WHERE `tbl_languages`.`code` = 'pt';");
        $this->db->query("UPDATE `tbl_languages` SET `icon` = 'ro' WHERE `tbl_languages`.`code` = 'ro';");
        $this->db->query("ALTER TABLE `tbl_leads` CHANGE `country` `country` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `state` `state` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");
        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_checklists` (
  `checklist_id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(32) DEFAULT NULL,
  `module_id` int(11) DEFAULT NULL,
  `description` text,
  `finished` int(11) DEFAULT '0',
  `create_datetime` datetime DEFAULT NULL,
  `added_from` int(11) DEFAULT NULL,
  `finished_from` int(11) DEFAULT NULL,
  `list_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`checklist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }
}