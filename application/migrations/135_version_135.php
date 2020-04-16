<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_135 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $this->db->query("UPDATE `tbl_config` SET `value` = '1.3.5' WHERE `tbl_config`.`config_key` = 'version';");
        $this->db->query("INSERT INTO `tbl_menu` (`menu_id`, `label`, `link`, `icon`, `parent`, `sort`, `time`, `status`) VALUES (NULL, 'allowed_ip', 'admin/settings/allowed_ip', 'fa fa-server', '25', '1', CURRENT_TIMESTAMP, '2');");
        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_allowed_ip` (
  `allowed_ip_id` int(11) NOT NULL AUTO_INCREMENT,
  `allowed_ip` varchar(100) NOT NULL,
  `status` enum('active','reject','pending') DEFAULT 'pending',
  PRIMARY KEY (`allowed_ip_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $this->db->query("INSERT INTO `tbl_email_templates` (`email_templates_id`, `email_group`, `subject`, `template_body`) VALUES
(67, 'clock_in_email', 'The {NAME} Just clock in', '<p>Hi there,</p>\r\n\r\n<p>The <strong>{NAME}</strong> just Clock In by using The IP. The IP is: <strong>{IP}</strong> and the time is:  <strong>{TIME}</strong><strong> </strong></p>\r\n\r\n<p>You can view this attendance by logging in to the portal using the link below.<br>\r\n<br>\r\n<big><strong><a href=\"{URL}\">View Details</a></strong></big><br>\r\n<br>\r\n<br>\r\nRegards,<br>\r\n<br>\r\nThe <strong>{SITE_NAME}</strong> Team</p>\r\n'),
(68, 'trying_clock_email', 'The {NAME} Trying to clock', '<p>Hi there,</p>\r\n\r\n<p>The <strong>{NAME} </strong> Trying to clock in by Unknown IP.The IP is: <strong>{IP}</strong> and the time is: <strong>{TIME}</strong></p>\r\n\r\n<p>You can view this IP by logging in to the portal using the link below.<br>\r\n<br>\r\n<big><strong><a href=\"{URL}\">View Details</a></strong></big><br>\r\n<br>\r\n<br>\r\nRegards,<br>\r\n<br>\r\nThe <strong>{SITE_NAME}</strong> Team</p>\r\n'),
(69, 'clock_out_email', 'The {NAME} Just clock Out', '<p>Hi there,</p>\r\n\r\n<p>The <strong>{NAME}</strong> just Clock Out by using The IP. The IP is: <strong>{IP}</strong> and the time is:  <strong>{TIME}</strong></p>\r\n\r\n<p>You can view this attendance by logging in to the portal using the link below.<br>\r\n<br>\r\n<big><strong><a href=\"{URL}\">View Details</a></strong></big><br>\r\n<br>\r\n<br>\r\nRegards,<br>\r\n<br>\r\nThe <strong>{SITE_NAME}</strong> Team</p>\r\n');");
    }
}