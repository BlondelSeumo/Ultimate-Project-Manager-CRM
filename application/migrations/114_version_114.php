<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_114 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $this->db->query("INSERT INTO `tbl_email_templates` (`email_templates_id`, `email_group`, `subject`, `template_body`) VALUES (60, 'deposit_email', 'Deposit Confirmation', '<p>Hi there,</p>\r\n\r\n<p>The&nbsp;<strong>{NAME}</strong>&nbsp;deposit&nbsp;<strong>{AMOUNT}&nbsp;</strong>has been deposit to &nbsp;<strong>{ACCOUNT}.</strong></p>\r\n\r\n<p>You can view this deposit by logging in to the portal using the link below.<br />\r\n<br />\r\n<big><strong><a href=\"{URL}\">View Deposit</a></strong></big><br />\r\n<br />\r\n<br />\r\nRegards,<br />\r\n<br />\r\nThe&nbsp;<strong>{SITE_NAME}</strong>&nbsp;Team</p>\r\n');");
        $this->db->query("ALTER TABLE `tbl_task` ADD `milestones_order` INT(11) NOT NULL DEFAULT '0' AFTER `index_no`;");
        $this->db->query("UPDATE `tbl_config` SET `value` = '1.4' WHERE `tbl_config`.`config_key` = 'version';");
    }
}