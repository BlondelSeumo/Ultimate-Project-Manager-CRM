<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 *	@author : uniquecoder.com
 *	date	: 21 April, 2015
 *	Ultimate Project Manager CRM PRO
 *	http://uniquecoder.com
 *      version: 1.2
 */

class MY_Install
{
    public function __construct()
    {
        $CI = &get_instance();
        $CI->load->database();

        if ($CI->db->database == '') {
            redirect('install');
        } else {
            //query from installer tbl
            $installer = $CI->db->get('installer')->row();
            // if installer_flag = 0
            if ($installer->installer_flag == 0) {
                // make it 1
                $CI->db->set('installer_flag', 1);
                $CI->db->where('id', $installer->id);
                $CI->db->update('installer');
                if (is_dir('install')) {
                    redirect('install/success.php');
                }
            }
            //run this code
            //else nothing
        }
    }
}
