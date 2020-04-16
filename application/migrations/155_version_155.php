<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_155 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
      
        $this->db->query("UPDATE `tbl_config` SET `value` = '1.5.5' WHERE `tbl_config`.`config_key` = 'version';");
    }
}