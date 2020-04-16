<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_152 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $this->db->query("INSERT INTO `tbl_email_templates` (`email_templates_id`, `email_group`, `subject`, `template_body`) VALUES (NULL, 'credit_note_email', 'New Credit Note', '<p>Credit Note {credit_note_REF}</p> <p>Hi {CLIENT}</p> <p>Thanks for your business inquiry.</p> <p>The Credit Note {credit_note_REF} is attached with this email.<br /> Credit Note Overview:<br /> Credit Note # : {credit_note_REF}<br /> Amount: {CURRENCY} {AMOUNT}<br /> <br /> You can view the Credit Note online at:<br /> <big><strong><a href=\"{credit_note_LINK}\">View Credit Note</a></strong></big><br /> <br /> Best Regards,<br /> The {SITE_NAME} Team</p> ');");
        $this->db->query("UPDATE `tbl_config` SET `value` = '1.5.2' WHERE `tbl_config`.`config_key` = 'version';");
    }
}
