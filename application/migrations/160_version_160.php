<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_160 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $this->db->query("ALTER TABLE `tbl_email_templates` ADD `code` VARCHAR(20) NULL DEFAULT NULL AFTER `email_templates_id`;");
        $this->db->query("UPDATE `tbl_email_templates` SET `code`='en';");
        $this->db->query("UPDATE `tbl_config` SET `value` = '1.6.0' WHERE `tbl_config`.`config_key` = 'version';");
    }
}
