<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_140 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $this->db->query("UPDATE `tbl_config` SET `value` = '2' WHERE `tbl_config`.`config_key` = 'decimal_separator';");
        $this->db->query("INSERT INTO `tbl_menu` (`menu_id`, `label`, `link`, `icon`, `parent`, `sort`, `time`, `status`) VALUES (NULL, 'purchase_settings', 'admin/settings/purchase', 'fa-fw icon-handbag', '25', '0', '2017-04-25 18:38:46', '2');");
        $this->db->query("UPDATE `tbl_config` SET `value` = '1.4.0' WHERE `tbl_config`.`config_key` = 'version';");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('item_total_qty_alert', 'No');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('amount_to_words_lowercase', 'No');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('invoice_number_format', '[INV]-[yyyy]-[mm]-[dd]-[number]');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('estimate_number_format', '[INV]-[yyyy]-[mm]-[dd]-[number]');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('proposal_number_format', '[INV]-[yyyy]-[mm]-[dd]-[number]');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('purchase_number_format', '[INV]-[yyyy]-[mm]-[dd]-[number]');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('return_stock_number_format', '[INV]-[yyyy]-[mm]-[dd]-[number]');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('purchase_start_no', '1');");
        $this->db->query("INSERT INTO `tbl_config` (`config_key`, `value`) VALUES ('return_stock_start_no', '1');");
    }
}