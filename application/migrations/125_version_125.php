<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_125 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $this->db->query("UPDATE `tbl_config` SET `value` = '1.2.5' WHERE `tbl_config`.`config_key` = 'version';");
        $this->db->query("ALTER TABLE `tbl_config` CHANGE `value` `value` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");
        $this->db->query("INSERT INTO `tbl_menu` (`menu_id`, `label`, `link`, `icon`, `parent`, `sort`, `time`, `status`) VALUES (NULL, 'mark_attendance', 'admin/attendance_report', 'fa fa-file-text', '105', '2', '2016-05-30 22:20:21', '1');");
    }
}