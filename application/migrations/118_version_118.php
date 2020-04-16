<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_118 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('auto_check_for_new_notifications', '0');");
        $this->db->query("UPDATE `tbl_config` SET `value` = '1.8' WHERE `tbl_config`.`config_key` = 'version';");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('pusher_app_id', '401479');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('pusher_app_key', '4cf88668659dc9c987c3');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('pusher_app_secret', '6fce183b214d17c20dd5');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('pusher_cluster', 'ap2');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('desktop_notifications', '1');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('realtime_notification', '0');");


        $this->db->query("ALTER TABLE `tbl_activities` ADD `link` VARCHAR(200) NULL DEFAULT NULL AFTER `icon`;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_notifications` (
  `notifications_id` int(11) NOT NULL AUTO_INCREMENT,
  `read` int(11) NOT NULL DEFAULT '0',
  `read_inline` tinyint(1) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL,
  `description` text NOT NULL,
  `from_user_id` int(11) NOT NULL DEFAULT '0',
  `to_user_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(200) DEFAULT NULL,
  `link` text,
  `icon` varchar(200) DEFAULT NULL,
  `value` text,
  PRIMARY KEY (`notifications_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

        $this->db->query("ALTER TABLE `tbl_todo` ADD `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `user_id`, ADD `due_date` DATE NOT NULL AFTER `created_date`;");
        $this->db->query("ALTER TABLE `tbl_todo` ADD `assigned` INT(11) NOT NULL DEFAULT '0' AFTER `status`;");
        $this->db->query("ALTER TABLE `tbl_todo` CHANGE `status` `status` INT(11) NOT NULL DEFAULT '0' COMMENT '1= in_progress 2= on hold 3= done';");
    }
}