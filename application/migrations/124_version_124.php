<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_124 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $this->db->query("UPDATE `tbl_config` SET `value` = '1.2.4' WHERE `tbl_config`.`config_key` = 'version';");
        $this->db->query("ALTER TABLE `tbl_project` CHANGE `description` `description` TEXT NULL;");
        $this->db->query("ALTER TABLE `tbl_job_appliactions` ADD `send_email` VARCHAR(20) NULL DEFAULT NULL AFTER `apply_date`, ADD `interview_date` VARCHAR(50) NULL DEFAULT NULL AFTER `send_email`;");
        $this->db->query("INSERT INTO `tbl_email_templates` (`email_templates_id`, `email_group`, `subject`, `template_body`) VALUES (NULL, 'call_for_interview', 'You have an interview offer!!!', '<p>Hello&nbsp;<strong>{NAME}</strong>,</p> <p>You have an interview offer for you.please see the details.&nbsp;<br /> <br /> <strong>Job Summary</strong>:<br /> Job Title # :<strong>&nbsp;{JOB_TITLE}</strong><br /> Designation # :<strong>&nbsp;{DESIGNATION}</strong><br /> Interview Date: <strong>{DATE}</strong></p> <p><strong>Postal Address</strong><br /> PO Box 16122 Collins Street West<br /> Victoria 8007 Australia<br /> 121 King Street, Melbourne<br /> Victoria 3000 Australia &ndash;&nbsp;<a href=\"https://www.google.com.au/maps/place/Envato/@-37.8173306,144.9534631,17z/data=!3m1!4b1!4m2!3m1!1s0x6ad65d4c2b349649:0xb6899234e561db11\" target=\"_blank\">Map</a></p> <p><br /> You can view the circular details online at:<br /> <big><strong><a href=\"{LINK}\">View Job Circular</a></strong></big><br /> <br /> Best Regards,<br /> The&nbsp;<strong>{SITE_NAME}</strong>&nbsp;Team</p> ')");

    }
}