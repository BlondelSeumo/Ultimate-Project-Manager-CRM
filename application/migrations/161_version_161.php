<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_161 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $this->db->query("ALTER TABLE `tbl_project` ADD `project_no` VARCHAR(100) NULL DEFAULT NULL AFTER `project_id`;");
        $this->db->query("INSERT INTO `tbl_menu` (`menu_id`, `label`, `link`, `icon`, `parent`, `sort`, `time`, `status`) VALUES (NULL, 'projects_settings', 'admin/settings/projects', 'fa fa-fw fa-folder-open-o', '25', '0', '2017-04-25 00:38:46', '2');");
        $this->db->query("UPDATE `tbl_config` SET `value` = '1.6.1' WHERE `tbl_config`.`config_key` = 'version';");
    }
}
