<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_127 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $this->db->query("UPDATE `tbl_config` SET `value` = '1.2.7' WHERE `tbl_config`.`config_key` = 'version';");
        $this->db->query("UPDATE `tbl_menu` SET `label`='transactions_menu' WHERE `tbl_menu`.`menu_id` = 29");
        $this->db->query("UPDATE `tbl_menu` SET `label` = 'transactions_reports' WHERE `tbl_menu`.`menu_id` = 146");
    }
}