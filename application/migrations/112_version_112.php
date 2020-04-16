<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_112 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        // add tbl_project_settings
        $this->db->query("UPDATE `tbl_menu` SET `link` = 'admin/projects' WHERE `tbl_menu`.`menu_id` = 57");

        $this->db->query("ALTER TABLE `tbl_invoices` ADD (client_visible ENUM('Yes','No') NULL DEFAULT 'No')");

        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_project_settings` (
  `settings_id` int(11) NOT NULL AUTO_INCREMENT,
  `settings` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`settings_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
        $this->db->query("INSERT INTO `tbl_project_settings` (`settings_id`, `settings`, `description`) VALUES
(1, 'show_team_members', 'view team members'),
(2, 'show_milestones', 'view project milestones'),
(5, 'show_project_tasks', 'view project tasks'),
(6, 'show_project_attachments', 'view project attachments'),
(7, 'show_timesheets', 'view project timesheets'),
(8, 'show_project_bugs', 'view project bugs'),
(9, 'show_project_history', 'view project history'),
(10, 'show_project_calendar', 'view project calendars'),
(11, 'show_project_comments', 'view project comments'),
(13, 'show_gantt_chart', 'view Gantt chart'),
(14, 'show_project_hours', 'view project hours'),
(15, 'comment_on_project_tasks', 'access To comment on project tasks'),
(16, 'show_project_tasks_attachments', 'view task attachments'),
(20, 'show_tasks_hours', 'show_tasks_hours'),
(21, 'show_finance_overview', 'show_finance_overview');");

        // tbl_project update
        $this->db->query("ALTER TABLE `tbl_project` ADD (calculate_progress varchar(100) NULL,estimate_hours varchar(100) NULL,billing_type varchar(100) NULL);");

        $this->db->query("INSERT INTO `tbl_languages` (`code`, `name`, `icon`, `active`) VALUES
        ('cs', 'czech', 'cz', 1),
        ('da', 'danish', 'dk', 1),
        ('el', 'greek', 'cy', 1),
        ('es', 'spanish', 'ar', 1),
        ('gu', 'gujarati', 'in', 1),
        ('hi', 'hindi', 'in', 1),
        ('id', 'indonesian', 'id', 1),
        ('ja', 'japanese', 'jp', 1),
        ('no', 'norwegian', 'no', 1),
        ('pl', 'polish', 'pl', 1),
        ('pt', 'portuguese', 'br', 1),
        ('ro', 'romanian', 'md', 1),
        ('ru', 'russian', 'ru', 1),
        ('zh', 'chinese', 'cn', 1)");

        $this->db->query("ALTER TABLE `tbl_estimates` ADD (project_id int(11) NULL DEFAULT '0')");
        $this->db->query("ALTER TABLE `tbl_tickets` ADD (project_id int(11) NULL DEFAULT '0')");
        $this->db->query("ALTER TABLE `tbl_task` ADD (hourly_rate decimal(18,2) NULL DEFAULT '0.00')");
        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_migrations` (`version` bigint(20) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
        $this->db->query("INSERT INTO `tbl_migrations` (`version`) VALUES(112);");
        $this->db->query("ALTER TABLE `tbl_transactions` CHANGE `status` `status` ENUM('Cleared','Uncleared','Reconciled','Void','non_approved','paid') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'non_approved';");
    }
}
