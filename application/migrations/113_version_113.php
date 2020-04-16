<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_113 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_sessions` (`id` varchar(40) NOT NULL,`ip_address` varchar(45) NOT NULL,`timestamp` int(10) UNSIGNED NOT NULL DEFAULT '0',`data` blob NOT NULL,PRIMARY KEY (`id`),KEY `ci_sessions_timestamp` (`timestamp`));");

        if (empty($this->db->field_exists('comments_attachment', 'tbl_task_comment'))) {
            $this->db->query("ALTER TABLE `tbl_task_comment` ADD (comments_attachment text NULL)");
        }
        $this->db->query("ALTER TABLE `tbl_task` ADD (billable varchar(100) NULL DEFAULT 'No',index_no int(11) NULL DEFAULT 0)");
        $this->db->query("ALTER TABLE `tbl_transactions` ADD (billable varchar(100) NULL DEFAULT 'No',name varchar(100) NULL,invoices_id int(11) NULL DEFAULT 0)");

        $this->db->query("ALTER TABLE `tbl_leads` ADD (index_no int(11) NULL DEFAULT 0)");
        $this->db->query("ALTER TABLE `tbl_lead_status` ADD (order_no int(11) NULL DEFAULT 0)");

        $this->db->query("ALTER TABLE `tbl_client` ADD (latitude varchar(100) NULL,longitude varchar(100) NULL,customer_group_id int(11) NULL DEFAULT 0,active varchar(100) NULL)");

        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_customer_group` (`customer_group_id` int(11) NOT NULL AUTO_INCREMENT,`customer_group` varchar(200) NOT NULL,`description` varchar(200) NOT NULL,PRIMARY KEY (`customer_group_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8");

        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_client_menu` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(20) DEFAULT NULL,
  `link` varchar(200) DEFAULT NULL,
  `icon` varchar(50) NOT NULL,
  `parent` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sort` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;");

        $this->db->query("INSERT INTO `tbl_client_menu` (`menu_id`, `label`, `link`, `icon`, `parent`, `time`, `sort`, `status`) VALUES
(1, 'projects', 'client/projects', 'fa fa-folder-open-o', 0, '2017-04-20 02:18:26', 3, 0),
(2, 'bugs', 'client/bugs', 'fa fa-bug', 0, '2017-04-20 02:18:39', 4, 0),
(3, 'invoices', 'client/invoice/manage_invoice', 'fa fa-shopping-cart', 0, '2017-04-20 02:18:42', 5, 0),
(4, 'estimates', 'client/estimates', 'fa fa-tachometer', 0, '2017-04-20 02:18:45', 6, 0),
(5, 'payments', 'client/invoice/all_payments', 'fa fa-money', 0, '2017-04-20 02:18:48', 7, 0),
(6, 'tickets', 'client/tickets', 'fa fa-ticket', 0, '2017-04-20 02:18:52', 8, 0),
(7, 'quotations', 'client/quotations', 'fa fa-paste', 0, '2017-04-20 02:18:56', 9, 0),
(8, 'users', 'client/user/user_list', 'fa fa-users', 0, '2017-04-20 02:18:59', 10, 0),
(9, 'settings', 'client/settings', 'fa fa-cogs', 0, '2017-04-20 02:19:03', 11, 0),
(17, 'dashboard', 'client/dashboard', 'icon-speedometer', 0, '2017-04-20 02:17:21', 1, 0),
(18, 'mailbox', 'client/mailbox', 'fa fa-envelope', 0, '2017-04-20 02:17:21', 2, 0),
(19, 'private_chat', 'client/message', 'fa fa-envelope', 0, '2017-04-20 02:19:25', 12, 0),
(20, 'filemanager', 'client/filemanager', 'fa fa-file', 0, '2017-06-03 14:08:23', 2, 1);");

        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_client_role` (
  `client_role_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  PRIMARY KEY (`client_role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");

        $this->db->query("ALTER TABLE `tbl_user_role` ADD (view int(11) NULL DEFAULT 0,created int(11) NULL DEFAULT 0,edited int(11) NULL DEFAULT 0,deleted int(11) NULL DEFAULT 0)");

        $this->db->query("DROP TABLE tbl_menu");

        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_menu` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(100) NOT NULL,
  `link` varchar(100) NOT NULL,
  `icon` varchar(100) NOT NULL,
  `parent` int(11) NOT NULL DEFAULT '0',
  `sort` int(11) NOT NULL DEFAULT '0',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(1) DEFAULT '1' COMMENT '1= active 0=inactive',
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=140 DEFAULT CHARSET=utf8");
        $this->db->query("INSERT INTO `tbl_menu` (`menu_id`, `label`, `link`, `icon`, `parent`, `sort`, `time`, `status`) VALUES
(1, 'dashboard', 'admin/dashboard', 'fa fa-dashboard', 0, 0, '2017-04-23 00:09:36', 1),
(2, 'calendar', 'admin/calendar', 'fa fa-calendar', 0, 1, '2017-04-23 18:27:23', 1),
(4, 'client', 'admin/client/manage_client', 'fa fa-users', 0, 12, '2017-06-10 12:46:25', 1),
(5, 'mailbox', 'admin/mailbox', 'fa fa-envelope-o', 0, 2, '2017-06-10 12:46:25', 1),
(6, 'tickets', '#', 'fa fa-ticket', 0, 10, '2017-06-10 12:46:25', 1),
(7, 'all_tickets', 'admin/tickets', 'fa fa-ticket', 6, 4, '2016-05-30 07:20:22', 1),
(8, 'answered', 'admin/tickets/answered', 'fa fa-circle-o', 6, 0, '2016-05-30 07:20:22', 1),
(9, 'open', 'admin/tickets/open', 'fa fa-circle-o', 6, 1, '2016-05-30 07:20:22', 1),
(10, 'in_progress', 'admin/tickets/in_progress', 'fa fa-circle-o', 6, 2, '2016-05-30 07:20:22', 1),
(11, 'closed', 'admin/tickets/closed', 'fa fa-circle-o', 6, 3, '2016-05-30 07:20:22', 1),
(12, 'sales', '#', 'fa fa-shopping-cart', 0, 9, '2017-06-10 12:32:58', 1),
(13, 'invoice', 'admin/invoice/manage_invoice', 'fa fa-circle-o', 12, 0, '2017-04-23 18:27:23', 1),
(14, 'estimates', 'admin/estimates', 'fa fa-circle-o', 12, 1, '2017-06-10 12:32:05', 1),
(15, 'payments_received', 'admin/invoice/all_payments', 'fa fa-circle-o', 12, 3, '2017-04-23 18:27:24', 1),
(16, 'tax_rates', 'admin/invoice/tax_rates', 'fa fa-circle-o', 12, 4, '2017-04-23 18:27:24', 1),
(21, 'quotations', '#', 'fa fa-paste', 12, 6, '2017-06-10 12:35:47', 1),
(22, 'quotations_list', 'admin/quotations', 'fa fa-circle-o', 21, 0, '2017-05-18 15:19:03', 1),
(23, 'quotations_form', 'admin/quotations/quotations_form', 'fa fa-circle-o', 21, 1, '2016-05-30 07:20:23', 1),
(24, 'user', 'admin/user/user_list', 'fa fa-users', 0, 24, '2017-06-10 12:47:24', 1),
(25, 'settings', 'admin/settings', 'fa fa-cogs', 0, 25, '2017-06-10 12:47:24', 1),
(26, 'database_backup', 'admin/settings/database_backup', 'fa fa-database', 0, 26, '2017-06-10 12:35:47', 1),
(29, 'transactions', '#', 'fa fa-building-o', 0, 11, '2017-06-10 12:46:25', 1),
(30, 'deposit', 'admin/transactions/deposit', 'fa fa-circle-o', 29, 1, '2017-06-10 12:31:30', 1),
(31, 'expense', 'admin/transactions/expense', 'fa fa-circle-o', 29, 0, '2017-06-10 12:31:30', 1),
(32, 'transfer', 'admin/transactions/transfer', 'fa fa-circle-o', 29, 2, '2017-06-10 12:31:30', 1),
(33, 'transactions_report', 'admin/transactions/transactions_report', 'fa fa-circle-o', 29, 3, '2017-04-23 19:07:15', 1),
(34, 'balance_sheet', 'admin/transactions/balance_sheet', 'fa fa-circle-o', 29, 5, '2017-04-23 19:07:16', 1),
(36, 'bank_cash', 'admin/account/manage_account', 'fa fa-money', 29, 6, '2017-06-10 12:31:30', 1),
(39, 'items', 'admin/items/items_list', 'fa fa-cube', 12, 5, '2017-06-10 12:35:47', 1),
(42, 'report', '#', 'fa fa-bar-chart', 0, 23, '2017-06-10 12:47:24', 1),
(43, 'account_statement', 'admin/report/account_statement', 'fa fa-circle-o', 42, 5, '2016-05-30 07:20:23', 1),
(44, 'income_report', 'admin/report/income_report', 'fa fa-circle-o', 42, 6, '2016-05-30 07:20:23', 1),
(45, 'expense_report', 'admin/report/expense_report', 'fa fa-circle-o', 42, 7, '2016-05-30 07:20:23', 1),
(46, 'income_expense', 'admin/report/income_expense', 'fa fa-circle-o', 42, 8, '2016-05-30 07:20:23', 1),
(47, 'date_wise_report', 'admin/report/date_wise_report', 'fa fa-circle-o', 42, 9, '2016-05-30 07:20:23', 1),
(48, 'all_income', 'admin/report/all_income', 'fa fa-circle-o', 42, 10, '2016-05-30 07:20:23', 1),
(49, 'all_expense', 'admin/report/all_expense', 'fa fa-circle-o', 42, 11, '2016-05-30 07:20:23', 1),
(50, 'all_transaction', 'admin/report/all_transaction', 'fa fa-circle-o', 42, 12, '2016-05-30 07:20:23', 1),
(51, 'recurring_invoice', 'admin/invoice/recurring_invoice', 'fa fa-circle-o', 12, 2, '2017-06-10 12:32:05', 1),
(52, 'transfer_report', 'admin/transactions/transfer_report', 'fa fa-circle-o', 29, 4, '2017-06-10 12:31:30', 1),
(53, 'report_by_month', 'admin/report/report_by_month', 'fa fa-circle-o', 42, 13, '2016-05-30 07:20:23', 1),
(54, 'tasks', 'admin/tasks/all_task', 'fa fa-tasks', 0, 5, '2017-06-10 12:46:25', 1),
(55, 'leads', 'admin/leads', 'fa fa-rocket', 0, 8, '2017-06-10 12:46:25', 1),
(56, 'opportunities', 'admin/opportunities', 'fa fa-filter', 0, 7, '2017-06-10 12:46:25', 1),
(57, 'projects', 'admin/projects', 'fa fa-folder-open-o', 0, 4, '2017-06-10 12:46:25', 1),
(58, 'bugs', 'admin/bugs', 'fa fa-bug', 0, 6, '2017-06-10 12:46:25', 1),
(59, 'project', '#', 'fa fa-folder-open-o', 42, 0, '2016-05-30 07:20:22', 1),
(60, 'tasks_report', 'admin/report/tasks_report', 'fa fa-circle-o', 42, 1, '2016-05-30 07:20:22', 1),
(61, 'bugs_report', 'admin/report/bugs_report', 'fa fa-circle-o', 42, 2, '2016-05-30 07:20:22', 1),
(62, 'tickets_report', 'admin/report/tickets_report', 'fa fa-circle-o', 42, 3, '2016-05-30 07:20:22', 1),
(63, 'client_report', 'admin/report/client_report', 'fa fa-circle-o', 42, 4, '2016-05-30 07:20:23', 1),
(66, 'tasks_assignment', 'admin/report/tasks_assignment', 'fa fa-dot-circle-o', 59, 0, '2016-05-30 07:25:02', 1),
(67, 'bugs_assignment', 'admin/report/bugs_assignment', 'fa fa-dot-circle-o', 59, 1, '2016-05-30 07:25:02', 1),
(68, 'project_report', 'admin/report/project_report', 'fa fa-dot-circle-o', 59, 2, '2016-05-30 07:25:02', 1),
(69, 'goal_tracking', 'admin/goal_tracking', 'fa fa-shield', 73, 1, '2017-06-10 12:35:47', 1),
(70, 'departments', 'admin/departments', 'fa fa-user-secret', 0, 13, '2017-06-10 12:46:25', 1),
(71, 'holiday', 'admin/holiday', 'fa fa-calendar-plus-o', 73, 0, '2017-06-10 12:35:47', 1),
(72, 'leave_management', 'admin/leave_management', 'fa fa-plane', 0, 19, '2017-06-10 12:47:24', 1),
(73, 'utilities', '#', 'fa fa-gift', 0, 22, '2017-06-10 12:47:24', 1),
(74, 'overtime', 'admin/utilities/overtime', 'fa fa-clock-o', 89, 9, '2017-06-10 12:34:23', 1),
(75, 'stock', '#', 'fa fa-codepen', 0, 14, '2017-06-10 12:47:24', 1),
(76, 'stock_category', 'admin/stock/stock_category', 'fa fa-sliders', 75, 0, '2016-05-30 07:20:23', 1),
(77, 'manage_stock', '#', 'fa fa-archive', 75, 2, '2017-04-26 14:41:10', 1),
(78, 'assign_stock', '#', 'fa fa-align-left', 75, 3, '2017-04-26 14:41:10', 1),
(79, 'stock_report', 'admin/stock/report', 'fa fa-line-chart', 75, 4, '2017-04-25 17:18:25', 1),
(81, 'stock_list', 'admin/stock/stock_list', 'fa fa-stack-exchange', 75, 1, '2017-04-26 14:41:10', 1),
(82, 'assign_stock', 'admin/stock/assign_stock', 'fa fa-align-left', 78, 0, '2016-05-30 07:25:02', 1),
(83, 'assign_stock_report', 'admin/stock/assign_stock_report', 'fa fa-bar-chart', 78, 1, '2016-05-30 07:25:02', 1),
(84, 'stock_history', 'admin/stock/stock_history', 'fa fa-file-text-o', 77, 0, '2016-05-30 07:25:02', 1),
(85, 'performance', '#', 'fa fa-dribbble', 0, 18, '2017-06-10 12:47:24', 1),
(86, 'performance_indicator', 'admin/performance/performance_indicator', 'fa fa-random', 85, 0, '2016-05-30 07:20:23', 1),
(87, 'performance_report', 'admin/performance/performance_report', 'fa fa-calendar-o', 85, 2, '2016-05-30 07:20:23', 1),
(88, 'give_appraisal', 'admin/performance/give_performance_appraisal', 'fa fa-plus', 85, 1, '2016-05-30 07:20:23', 1),
(89, 'payroll', '#', 'fa fa-usd', 0, 17, '2017-06-10 12:47:24', 1),
(90, 'manage_salary_details', 'admin/payroll/manage_salary_details', 'fa fa-usd', 89, 2, '2017-04-23 06:36:37', 1),
(91, 'employee_salary_list', 'admin/payroll/employee_salary_list', 'fa fa-user-secret', 89, 3, '2017-04-23 06:36:37', 1),
(92, 'make_payment', 'admin/payroll/make_payment', 'fa fa-tasks', 89, 4, '2017-04-23 06:36:37', 1),
(93, 'generate_payslip', 'admin/payroll/generate_payslip', 'fa fa-list-ul', 89, 5, '2017-04-23 06:36:37', 1),
(94, 'salary_template', 'admin/payroll/salary_template', 'fa fa-money', 89, 0, '2017-04-23 06:36:37', 1),
(95, 'hourly_rate', 'admin/payroll/hourly_rate', 'fa fa-clock-o', 89, 1, '2017-04-23 06:36:37', 1),
(96, 'payroll_summary', 'admin/payroll/payroll_summary', 'fa fa-camera-retro', 89, 6, '2017-04-23 06:36:37', 1),
(97, 'provident_fund', 'admin/payroll/provident_fund', 'fa fa-briefcase', 89, 8, '2017-06-10 12:34:23', 1),
(98, 'advance_salary', 'admin/payroll/advance_salary', 'fa fa-cc-mastercard', 89, 7, '2017-06-10 12:34:23', 1),
(99, 'employee_award', 'admin/award', 'fa fa-trophy', 89, 10, '2017-06-10 12:35:47', 1),
(100, 'announcements', 'admin/announcements', 'fa fa-bullhorn icon', 0, 21, '2017-06-10 12:47:24', 1),
(101, 'training', 'admin/training', 'fa fa-suitcase', 0, 20, '2017-06-10 12:47:24', 1),
(102, 'job_circular', '#', 'fa fa-globe', 0, 16, '2017-06-10 12:47:24', 1),
(103, 'jobs_posted', 'admin/job_circular/jobs_posted', 'fa fa-ticket', 102, 0, '2016-05-30 07:20:21', 1),
(104, 'jobs_applications', 'admin/job_circular/jobs_applications', 'fa fa-compass', 102, 1, '2016-05-30 07:20:21', 1),
(105, 'attendance', '#', 'fa fa-file-text', 0, 15, '2017-06-10 12:47:24', 1),
(106, 'timechange_request', 'admin/attendance/timechange_request', 'fa fa-calendar-o', 105, 1, '2016-05-30 07:20:21', 1),
(107, 'attendance_report', 'admin/attendance/attendance_report', 'fa fa-file-text', 105, 2, '2016-05-30 07:20:21', 1),
(108, 'time_history', 'admin/attendance/time_history', 'fa fa-clock-o', 105, 0, '2016-05-30 07:20:21', 1),
(109, 'pull-down', '', '', 0, 0, '2016-05-31 11:13:20', 0),
(110, 'filemanager', 'admin/filemanager', 'fa fa-file', 0, 3, '2017-06-10 12:46:25', 1),
(111, 'company_details', 'admin/settings', 'fa fa-fw fa-info-circle', 25, 1, '2017-04-25 15:38:46', 2),
(112, 'system_settings', 'admin/settings/system', 'fa fa-fw fa-desktop', 25, 2, '2017-04-25 15:38:46', 2),
(113, 'email_settings', 'admin/settings/email', 'fa fa-fw fa-envelope', 25, 3, '2017-04-25 15:38:46', 2),
(114, 'email_templates', 'admin/settings/templates', 'fa fa-fw fa-pencil-square', 25, 4, '2017-04-25 15:38:46', 2),
(115, 'email_integration', 'admin/settings/email_integration', 'fa fa-fw fa-envelope-o', 25, 5, '2017-04-25 15:38:46', 2),
(116, 'payment_settings', 'admin/settings/payments', 'fa fa-fw fa-dollar', 25, 6, '2017-04-25 15:38:46', 2),
(117, 'invoice_settings', 'admin/settings/invoice', 'fa fa-fw fa-money', 25, 0, '2017-04-25 15:38:46', 2),
(118, 'estimate_settings', 'admin/settings/estimate', 'fa fa-fw fa-file-o', 25, 0, '2017-04-25 15:38:46', 2),
(119, 'tickets_leads_settings', 'admin/settings/tickets', 'fa fa-fw fa-ticket', 25, 0, '2017-04-25 15:38:46', 2),
(120, 'theme_settings', 'admin/settings/theme', 'fa fa-fw fa-code', 25, 0, '2017-04-25 15:38:46', 2),
(121, 'working_days', 'admin/settings/working_days', 'fa fa-fw fa-calendar', 25, 0, '2017-04-25 15:43:41', 2),
(122, 'leave_category', 'admin/settings/leave_category', 'fa fa-fw fa-pagelines', 25, 0, '2017-04-25 15:43:41', 2),
(123, 'income_category', 'admin/settings/income_category', 'fa fa-fw fa-certificate', 25, 0, '2017-04-25 15:43:41', 2),
(124, 'expense_category', 'admin/settings/expense_category', 'fa fa-fw fa-tasks', 25, 0, '2017-04-25 15:43:41', 2),
(125, 'customer_group', 'admin/settings/customer_group', 'fa fa-fw fa-users', 25, 0, '2017-04-25 15:43:41', 2),
(126, 'contract_type', 'admin/settings/contract_type', 'fa fa-fw fa-file-o', 25, 0, '2017-04-25 15:43:41', 2),
(127, 'lead_status', 'admin/settings/lead_status', 'fa fa-fw fa-list-ul', 25, 0, '2017-04-25 15:43:41', 2),
(128, 'lead_source', 'admin/settings/lead_source', 'fa fa-fw fa-arrow-down', 25, 0, '2017-04-25 15:43:41', 2),
(129, 'opportunities_state_reason', 'admin/settings/opportunities_state_reason', 'fa fa-fw fa-dot-circle-o', 25, 0, '2017-04-25 15:43:41', 2),
(130, 'custom_field', 'admin/settings/custom_field', 'fa fa-fw fa-star-o', 25, 0, '2017-04-25 15:43:41', 2),
(131, 'payment_method', 'admin/settings/payment_method', 'fa fa-fw fa-money', 25, 0, '2017-04-25 15:43:41', 2),
(132, 'cronjob', 'admin/settings/cronjob', 'fa fa-fw fa-contao', 25, 0, '2017-04-25 15:46:20', 2),
(133, 'menu_allocation', 'admin/settings/menu_allocation', 'fa fa-fw fa fa-compass', 25, 0, '2017-04-25 15:46:20', 2),
(134, 'notification', 'admin/settings/notification', 'fa fa-fw fa-bell-o', 25, 0, '2017-04-25 15:46:20', 2),
(135, 'email_notification', 'admin/settings/email_notification', 'fa fa-fw fa-bell-o', 25, 0, '2017-04-25 15:46:20', 2),
(136, 'database_backup', 'admin/settings/database_backup', 'fa fa-fw fa-database', 25, 0, '2017-04-25 15:46:20', 2),
(137, 'translations', 'admin/settings/translations', 'fa fa-fw fa-language', 25, 0, '2017-04-25 15:46:20', 2),
(138, 'system_update', 'admin/settings/system_update', 'fa fa-fw fa-pencil-square-o', 25, 0, '2017-04-25 15:46:20', 2),
(139, 'private_chat', 'admin/message', 'fa fa-envelope', 0, 27, '2017-06-10 12:35:47', 1)");

        $this->db->query("ALTER TABLE `tbl_clock` ADD (ip_address text NULL)");

        $this->db->query("ALTER TABLE `tbl_job_circular` CHANGE `employment_type` `employment_type` ENUM('contractual','full_time','part_time') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'full_time';");
        $this->db->query("ALTER TABLE `tbl_job_circular` ADD (experience varchar(100) NULL,age varchar(100) NULL,salary_range varchar(100) NULL)");
        $this->db->query("ALTER TABLE `tbl_account_details` ADD (passport varchar(100) NULL)");
        $this->db->query("ALTER TABLE `tbl_attendance` ADD (clocking_status tinyint(1) NULL DEFAULT 0)");

    }
}