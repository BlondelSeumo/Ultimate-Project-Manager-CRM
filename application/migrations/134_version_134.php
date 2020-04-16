<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_134 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $this->db->query("UPDATE `tbl_config` SET `value` = '1.3.4' WHERE `tbl_config`.`config_key` = 'version';");
        $email_templates = $this->db->where('email_group', 'deposit_email')->get('tbl_email_templates')->row();
        if (empty($email_templates)) {
            $this->db->query("INSERT INTO `tbl_email_templates` (`email_templates_id`, `email_group`, `subject`, `template_body`) VALUES (66, 'deposit_email', 'A deposit have been Received', '<p>Hi there,</p> <p>The&nbsp;<strong>{NAME}</strong>&nbsp;of deposit&nbsp;<strong>{AMOUNT}&nbsp;</strong>has been Deposit into <strong>{ACCOUNT}</strong> the new balance is <strong>{BALANCE}</strong></p> <p>You can view this deposit by logging in to the portal using the link below.<br /> <br /> <big><strong><a href=\"{URL}\">View Deposit</a></strong></big><br /> <br /> <br /> Regards,<br /> <br /> The&nbsp;<strong>{SITE_NAME}</strong>&nbsp;Team</p>');");
        }
        $this->db->query("INSERT INTO `tbl_project_settings` (`settings_id`, `settings`, `description`) VALUES (NULL, 'show_staff_finance_overview', 'admin and staff can see the price');");
    }
}