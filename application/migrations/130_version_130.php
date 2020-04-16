<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_130 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $this->db->query("UPDATE `tbl_config` SET `value` = '1.3.0' WHERE `tbl_config`.`config_key` = 'version';");
        $this->db->query("ALTER TABLE `tbl_tasks_timer` ADD `timer_status` ENUM('on', 'off') NOT NULL DEFAULT 'off' AFTER `user_id`;");
        $this->db->query("ALTER TABLE `tbl_departments` ADD `encryption` VARCHAR(10) NOT NULL AFTER `department_head_id`, ADD `host` VARCHAR(50) NOT NULL AFTER `encryption`, ADD `username` VARCHAR(50) NOT NULL AFTER `host`, ADD `password` TEXT NOT NULL AFTER `username`, ADD `mailbox` VARCHAR(20) NOT NULL AFTER `password`, ADD `unread_email` TINYINT(1) NOT NULL DEFAULT '0' AFTER `mailbox`;");
        $this->db->query("ALTER TABLE `tbl_departments` ADD `delete_mail_after_import` TINYINT(1) NOT NULL DEFAULT '0' AFTER `unread_email`, ADD `last_postmaster_run` VARCHAR(20) NULL DEFAULT NULL AFTER `delete_mail_after_import`;");
        $this->db->query("ALTER TABLE `tbl_departments` ADD `email` VARCHAR(50) NULL DEFAULT NULL AFTER `department_head_id`;");
        $this->db->query("ALTER TABLE `tbl_leads` ADD `imported_from_email` TINYINT(1) NULL DEFAULT '0' AFTER `lead_source_id`, ADD `email_integration_uid` VARCHAR(30) NULL DEFAULT NULL AFTER `imported_from_email`;");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('last_tickets_postmaster_run', NULL);");
        $this->db->query("ALTER TABLE `tbl_tickets` ADD `email` VARCHAR(50) NULL DEFAULT NULL AFTER `ticket_code`;");
        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_outgoing_emails` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sent_to` varchar(64) DEFAULT NULL,
  `sent_from` varchar(64) DEFAULT NULL,
  `subject` text,
  `message` longtext,
  `date_sent` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `delivered` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }
}