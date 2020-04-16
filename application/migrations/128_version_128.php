<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_128 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $this->db->query("UPDATE `tbl_config` SET `value` = '1.2.8' WHERE `tbl_config`.`config_key` = 'version';");
        $this->db->query("ALTER TABLE `tbl_working_days` CHANGE `start_hours` `start_hours` VARCHAR(20) NOT NULL;;");
        $this->db->query("ALTER TABLE `tbl_working_days` CHANGE `end_hours` `end_hours` VARCHAR(20) NOT NULL;;");
    }
}