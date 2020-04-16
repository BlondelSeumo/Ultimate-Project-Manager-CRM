<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_119 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $this->db->query("UPDATE `tbl_config` SET `value` = '1.9' WHERE `tbl_config`.`config_key` = 'version';");
        $this->db->query("INSERT INTO `tbl_form` (`form_id`, `form_name`, `tbl_name`) VALUES (NULL, 'client', 'tbl_client');");
        $this->db->query("INSERT INTO `tbl_form` (`form_id`, `form_name`, `tbl_name`) VALUES (NULL, 'users', 'tbl_account_details');");
        $this->db->query("INSERT INTO `tbl_form` (`form_id`, `form_name`, `tbl_name`) VALUES (NULL, 'recruitment', 'tbl_job_circular');");
        $this->db->query("INSERT INTO `tbl_form` (`form_id`, `form_name`, `tbl_name`) VALUES (NULL, 'training', 'tbl_training');");
        $this->db->query("INSERT INTO `tbl_form` (`form_id`, `form_name`, `tbl_name`) VALUES (NULL, 'announcements', 'tbl_announcements');");

        $this->db->query("ALTER TABLE `tbl_custom_field` ADD `show_on_table` VARCHAR(5) NULL DEFAULT NULL AFTER `status`;");
        $this->db->query("ALTER TABLE `tbl_custom_field` ADD `visible_for_admin` VARCHAR(5) NULL DEFAULT NULL AFTER `show_on_table`;");
        $this->db->query("ALTER TABLE `tbl_custom_field` CHANGE `field_type` `field_type` ENUM('text','textarea','dropdown','date','checkbox','numeric','url','email') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('qty_calculation_from_items', 'Yes');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('allow_customer_edit_amount', 'Yes');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('office_hours', '8');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('copyright_name', 'Uniquecoder');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('copyright_url', 'https://codecanyon.net/user/unique_coder');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('recaptcha_site_key', ''), ('recaptcha_secret_key', '');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('mark_attendance_from_login', 'Yes');");
        $this->db->query("ALTER TABLE `tbl_leave_application` CHANGE `leave_end_date` `leave_end_date` DATE NULL DEFAULT NULL;");
        $this->db->query("ALTER TABLE `tbl_leave_application` ADD `leave_type` ENUM('single_day','multiple_days','hours') NOT NULL DEFAULT 'single_day' AFTER `reason`, ADD `hours` VARCHAR(20) NULL DEFAULT NULL AFTER `leave_type`;");
        $this->db->query("ALTER TABLE `tbl_items` ADD `saved_items_id` INT(11) NULL DEFAULT '0' AFTER `estimates_id`;");
        $this->db->query("ALTER TABLE `tbl_estimate_items` ADD `saved_items_id` INT(11) NULL DEFAULT '0' AFTER `estimates_id`;");
        $this->db->query("ALTER TABLE `tbl_proposals_items` ADD `saved_items_id` INT(11) NULL DEFAULT '0' AFTER `proposals_id`;");

        // update all leave application as a multiple_days if the end date is greater than to star date
        $all_leave = $this->db->get('tbl_leave_application')->result();
        if (!empty($all_leave)) {
            foreach ($all_leave as $v_leave) {
                if ($v_leave->leave_start_date < $v_leave->leave_end_date) {
                    $this->db->query("UPDATE `tbl_leave_application` SET `leave_type` = 'multiple_days' WHERE `tbl_leave_application`.`leave_application_id` = $v_leave->leave_application_id;");
                }
            }
        }
        $this->db->query("ALTER TABLE `tbl_task_comment` ADD `task_attachment_id` INT(11) NULL DEFAULT '0' AFTER `goal_tracking_id`, ADD `uploaded_files_id` INT(11) NULL DEFAULT '0' AFTER `task_attachment_id`;");
        $this->db->query("ALTER TABLE `tbl_announcements` ADD `attachment` TEXT NULL DEFAULT NULL AFTER `all_client`;");
        $this->db->query("ALTER TABLE `tbl_tickets_replies` ADD `ticket_reply_id` INT(0) NULL DEFAULT '0' AFTER `tickets_id`;");
        $this->db->query("INSERT INTO `tbl_online_payment` (`online_payment_id`, `gateway_name`, `icon`) VALUES (NULL, 'PayUmoney', 'payumoney.jpg');");
        $this->db->query("ALTER TABLE `tbl_invoices` ADD `allow_payumoney` ENUM('Yes', 'No') NULL DEFAULT 'No' AFTER `allow_mollie`;");
        $this->db->query("ALTER TABLE `tbl_items` ADD `hsn_code` TEXT NULL DEFAULT NULL AFTER `unit`;");
        $this->db->query("ALTER TABLE `tbl_proposals_items` ADD `hsn_code` TEXT NULL DEFAULT NULL AFTER `unit`;");
        $this->db->query("ALTER TABLE `tbl_estimate_items` ADD `hsn_code` TEXT NULL DEFAULT NULL AFTER `unit`;");
        $this->db->query("ALTER TABLE `tbl_saved_items` ADD `hsn_code` TEXT NULL DEFAULT NULL AFTER `total_cost`;");

        $old_name = FCPATH . '-';
        if (file_exists($old_name)) {
            $new_name = FCPATH . 'filemanager';
            rename($old_name, $new_name);
        }

    }
}