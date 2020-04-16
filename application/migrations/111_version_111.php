<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_111 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_pinaction` (`pinaction_id` int(11) NOT NULL AUTO_INCREMENT,`user_id` int(11) NOT NULL,`module_id` int(11) NOT NULL,`module_name` varchar(30) DEFAULT NULL,PRIMARY KEY (`pinaction_id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
        $this->db->query("ALTER TABLE `tbl_transactions` ADD (project_id int(11) NOT NULL);");
    }
}
