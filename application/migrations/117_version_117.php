<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_117 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('active_custom_color', '0');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('navbar_logo_background', 'rgba(10,124,139,0.95)');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('top_bar_background', '#1f9494');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('top_bar_color', '#d9d9d9');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('sidebar_background', 'rgba(2,53,60,0.95)');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('sidebar_color', '#fffafa');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('sidebar_active_background', '#0f778e');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('sidebar_active_color', '#b3b8cb');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('submenu_open_background', '#227f85');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('active_background', '#1c7086');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('active_color', '#c1c1c1');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('body_background', 'rgba(229,252,252,0.81)');");

        $this->db->query("ALTER TABLE `tbl_account_details` ADD `direction` VARCHAR(20) NULL DEFAULT NULL AFTER `passport`;");
        $this->db->query("ALTER TABLE `tbl_salary_payment` ADD `deduct_from` INT(11) NOT NULL DEFAULT '0' COMMENT 'deduct from means tracking deduct from which account' AFTER `paid_date`;");
        $this->db->query("ALTER TABLE `tbl_payments` ADD `account_id` INT(11) NOT NULL DEFAULT '0' COMMENT 'account_id means tracking deposit from which account';");
        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_notes` (
  `notes_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `is_client` enum('Yes','No') NOT NULL DEFAULT 'No',
  `notes` text,
  `added_by` int(11) NOT NULL,
  `added_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`notes_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_priority` (
  `priority_id` int(11) NOT NULL AUTO_INCREMENT,
  `priority` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`priority_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;");

        $this->db->query("INSERT INTO `tbl_priority` (`priority_id`, `priority`) VALUES
(1, 'High'),
(2, 'Medium'),
(3, 'Low');");
        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_reminders` (
  `reminder_id` int(11) NOT NULL AUTO_INCREMENT,
  `description` text,
  `date` datetime NOT NULL,
  `notified` enum('Yes','No') NOT NULL DEFAULT 'No',
  `module` varchar(200) NOT NULL,
  `module_id` int(11) NOT NULL,
  `user_id` varchar(40) NOT NULL,
  `notify_by_email` enum('Yes','No') NOT NULL DEFAULT 'No',
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`reminder_id`),
  KEY `rel_id` (`module`),
  KEY `rel_type` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
    }
}