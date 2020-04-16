<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_133 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $this->db->query("UPDATE `tbl_config` SET `value` = '1.3.3' WHERE `tbl_config`.`config_key` = 'version';");
        $this->db->query("UPDATE `tbl_salary_payment_details` SET `salary_payment_details_label` = 'overtime_salary' WHERE `tbl_salary_payment_details`.`salary_payment_details_label` = 'Overtime Salary <small>( Per Hour)</small>';");
        $this->db->query("UPDATE `tbl_salary_payment_details` SET `salary_payment_details_label` = 'hourly_rates' WHERE `tbl_salary_payment_details`.`salary_payment_details_label` = 'Hourly Rate';");
        $this->db->query("INSERT INTO `tbl_languages` (`code`, `name`, `icon`, `active`) VALUES ('vi', 'vietnamese', 'vn', '0');");
        $this->db->query("UPDATE `tbl_menu` SET `link` = 'admin/mark_attendance' WHERE `tbl_menu`.`menu_id` = 148;");
        $this->db->query("ALTER TABLE `tbl_transactions` CHANGE `category_id` `category_id` INT(11) NULL DEFAULT NULL;");
    }
}