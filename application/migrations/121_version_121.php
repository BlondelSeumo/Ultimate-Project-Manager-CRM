<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_121 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $this->db->query("UPDATE `tbl_config` SET `value` = '1.2.1' WHERE `tbl_config`.`config_key` = 'version';");
        $this->db->query("ALTER TABLE `tbl_attendance` CHANGE `leave_category_id` `leave_application_id` INT(11) NULL DEFAULT '0';");
        $this->db->query("UPDATE `tbl_form` SET `form_name` = 'job_circular' WHERE `tbl_form`.`form_id` = 14;");
        $this->db->query("INSERT INTO `tbl_form` (`form_id`, `form_name`, `tbl_name`) VALUES (NULL, 'leave_management', 'tbl_leave_application');");
        $this->db->query("ALTER TABLE `tbl_notifications` DROP `notifications_id`;");
        $this->db->query("ALTER TABLE `tbl_notifications` ADD `notifications_id` INT(11) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`notifications_id`);");
        $this->db->query("ALTER TABLE `tbl_users` CHANGE `online_status` `online_time` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00';");
        $this->db->query("ALTER TABLE `tbl_task` ADD `sub_task_id` INT(11) NULL DEFAULT NULL AFTER `goal_tracking_id`;");
        $this->db->query("UPDATE `tbl_menu` SET `link` = 'chat/conversations' WHERE `tbl_menu`.`menu_id` = 139;");
        $this->db->query("UPDATE `tbl_client_menu` SET `link` = 'chat/conversations' WHERE `tbl_client_menu`.`menu_id` = 19;");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('allow_sub_tasks', 'TRUE');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('allow_multiple_client_in_project', 'TRUE');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('project_details_view', '2');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('task_details_view', '2');");
        $this->db->query("ALTER TABLE `tbl_task` ADD `calculate_progress` VARCHAR(200) NULL DEFAULT NULL AFTER `task_progress`;");
        $this->db->query("ALTER TABLE `tbl_invoices` ADD `alert_overdue` TINYINT(1) NOT NULL DEFAULT '0' AFTER `due_date`;");
        $this->db->query("ALTER TABLE `tbl_estimates` ADD `alert_overdue` TINYINT(1) NOT NULL DEFAULT '0' AFTER `due_date`;");
        $this->db->query("ALTER TABLE `tbl_project` ADD `alert_overdue` TINYINT(1) NOT NULL DEFAULT '0' AFTER `end_date`;");
        $this->db->query("DROP TABLE tbl_private_message_send");
        $this->db->query("DROP TABLE `tbl_updates`");
        $this->db->query("DROP TABLE `tbl_dashboard`");
        $this->db->query("ALTER TABLE `tbl_opportunities` CHANGE `notes` `notes` TEXT NULL DEFAULT NULL;");
        $this->db->query("ALTER TABLE `tbl_client` CHANGE `password` `password` TEXT NULL;");
        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_dashboard` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET latin1 NOT NULL,
  `col` varchar(200) CHARACTER SET latin1 DEFAULT NULL,
  `order_no` int(2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `report` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;");

        $this->db->query("INSERT INTO `tbl_dashboard` (`id`, `name`, `col`, `order_no`, `status`, `report`) VALUES
(1, 'income_expenses_report', 'col-sm-4', 1, 1, 1),
(2, 'invoice_payment_report', 'col-sm-4', 1, 1, 1),
(3, 'ticket_tasks_report', 'col-sm-4', 2, 1, 1),
(5, 'goal_report', 'col-md-12 ', 7, 1, 0),
(6, 'overdue_report', 'col-md-12', 10, 1, 0),
(11, 'my_project', 'col-md-6', 24, 1, 0),
(12, 'my_tasks', 'col-md-6', 27, 1, 0),
(14, 'announcements', 'col-md-6', 30, 1, 0),
(15, 'payments_report', 'col-md-6', 39, 1, 0),
(16, 'income_expense', 'col-md-6', 15, 1, 0),
(17, 'income_report', 'col-md-6', 42, 1, 0),
(18, 'expense_report', 'col-md-6', 36, 1, 0),
(19, 'recently_paid_invoices', 'col-md-6', 21, 1, 0),
(20, 'recent_activities', 'col-md-6', 18, 1, 0),
(21, 'finance_overview', 'col-sm-12', 1, 1, 0),
(22, 'todo_list', 'col-md-6', 32, 1, 0),
(23, 'paid_amount', 'col-md-3', 2, 1, 2),
(24, 'due_amount', 'col-md-3', 4, 1, 2),
(25, 'invoice_amount', 'col-md-3', 1, 1, 2),
(26, 'paid_percentage', 'col-md-3', 3, 1, 2),
(27, 'recently_paid_invoices', 'col-sm-6', 2, 1, 3),
(28, 'payments', 'col-sm-6', 1, 1, 3),
(29, 'recent_invoice', 'col-sm-6', 3, 1, 3),
(30, 'recent_projects', 'col-sm-6', 4, 1, 3),
(31, 'recent_emails', 'col-sm-4', 5, 1, 3),
(32, 'recent_activities', 'col-sm-4', 6, 1, 3),
(33, 'announcements', 'col-sm-4', 7, 1, 3);");
        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_private_chat` (
  `private_chat_id` int(11) NOT NULL AUTO_INCREMENT,
  `chat_title` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`private_chat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_private_chat_messages` (
  `private_chat_messages_id` int(11) NOT NULL AUTO_INCREMENT,
  `private_chat_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `message_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`private_chat_messages_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_private_chat_users` (
  `private_chat_users_id` int(11) NOT NULL AUTO_INCREMENT,
  `private_chat_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `to_user_id` int(11) NOT NULL,
  `active` int(11) NOT NULL COMMENT '0 == minimize chat,1 == open chat and  2 == close chat ',
  `unread` int(11) NOT NULL,
  `title` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT '0' COMMENT 'keep last message id',
  PRIMARY KEY (`private_chat_users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }
}