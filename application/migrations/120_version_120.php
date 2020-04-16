<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_120 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $this->db->query("UPDATE `tbl_config` SET `value` = '1.2.0' WHERE `tbl_config`.`config_key` = 'version';");
        $this->db->query("INSERT INTO `tbl_menu` (`menu_id`, `label`, `link`, `icon`, `parent`, `sort`, `time`, `status`) VALUES (NULL, 'knowledgebase', '#', 'fa fa-question-circle', '0', '10', CURRENT_TIMESTAMP, '1');");
        $this->db->query("INSERT INTO `tbl_menu` (`menu_id`, `label`, `link`, `icon`, `parent`, `sort`, `time`, `status`) VALUES (NULL, 'knowledgebase', 'admin/knowledgebase', 'fa fa-circle-o', '141', '1', CURRENT_TIMESTAMP, '1');");
        $this->db->query("INSERT INTO `tbl_menu` (`menu_id`, `label`, `link`, `icon`, `parent`, `sort`, `time`, `status`) VALUES (NULL, 'categories', 'admin/knowledgebase/categories', 'fa fa-circle-o', '141', '3', CURRENT_TIMESTAMP, '1'), (NULL, 'articles', 'admin/knowledgebase/articles', 'fa fa-circle-o', '141', '2', CURRENT_TIMESTAMP, '1');");
        $this->db->query("CREATE TABLE `tbl_kb_category` (
 `kb_category_id` int(11) NOT NULL AUTO_INCREMENT,
 `category` varchar(200) NOT NULL,
 `description` longtext,
 `type` varchar(50) NOT NULL,
 `sort` int(2) NOT NULL,
 `status` tinyint(1) NOT NULL DEFAULT '1',
 PRIMARY KEY (`kb_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_knowledgebase` (
  `kb_id` int(11) NOT NULL AUTO_INCREMENT,
  `kb_category_id` int(11) NOT NULL,
  `title` text,
  `slug` text,
  `description` text,
  `attachments` text,
  `for_all` enum('Yes','No') DEFAULT 'No',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `total_view` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sort` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`kb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        
        $this->db->query("ALTER TABLE `tbl_training` CHANGE `upload_file` `upload_file` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");
        $this->db->query("INSERT INTO `tbl_client_menu` (`menu_id`, `label`, `link`, `icon`, `parent`, `time`, `sort`, `status`) VALUES (NULL, 'knowledgebase', 'knowledgebase', 'fa fa-question-circle', '0', CURRENT_TIMESTAMP, '12', '1');");
        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_dashboard` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `order_no` int(2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `report` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

INSERT INTO `tbl_dashboard` (`id`, `name`, `order_no`, `status`, `report`) VALUES
(1, 'income_expenses_report', 3, 1, 1),
(2, 'invoice_payment_report', 2, 1, 1),
(3, 'ticket_tasks_report', 1, 1, 1),
(5, 'goal_report', 4, 1, 0),
(6, 'overdue_report', 5, 1, 0),
(11, 'my_project', 6, 1, 0),
(12, 'my_tasks', 7, 1, 0),
(14, 'announcements', 8, 1, 0),
(15, 'payments_report', 1, 1, 0),
(16, 'income_expense', 9, 1, 0),
(17, 'income_report', 10, 1, 0),
(18, 'expense_report', 11, 1, 0),
(19, 'recently_paid_invoices', 12, 1, 0),
(20, 'recent_activities', 13, 1, 0),
(21, 'finance_overview', 2, 1, 0),
(22, 'todo_list', 4, 1, 0);");
        $this->db->query("INSERT INTO `tbl_menu` (`menu_id`, `label`, `link`, `icon`, `parent`, `sort`, `time`, `status`) VALUES (NULL, 'dashboard_settings', 'admin/settings/dashboard', 'fa fa-fw fa-dashboard', '25', '11', '2017-04-26 07:38:46', '2');");
    }
}