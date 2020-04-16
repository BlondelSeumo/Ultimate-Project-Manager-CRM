<?php

/**
 * Description of MY_Controller
 *
 * @author Nayeem
 */
class MY_Controller extends CI_Controller
{

    function __construct()
    {
        parent::__construct();

//        $this->output->cache(30);
        $this->load->model('login_model');
        $this->load->library('form_validation');
        $this->load->helper('form');
        $this->load->model('admin_model');
        $this->load->model('items_model');
        $this->load->model('invoice_model');
        $this->load->model('global_model');
        $this->load->helper('language');

        $config_data = $this->db->get('tbl_config')->result();
        foreach ($config_data as $v_config_info) {
            $this->config->set_item($v_config_info->config_key, $v_config_info->value);
        }
        $system_lang = $this->admin_model->get_lang();
        $this->config->set_item('language', $system_lang);
        $files = $this->admin_model->all_files();
        if (!empty($system_lang)) {
            foreach ($files as $file => $altpath) {
                $shortfile = str_replace("_lang.php", "", $file);
                $this->lang->load($shortfile, $system_lang);
            }
        } else {
            foreach ($files as $file => $altpath) {
                $shortfile = str_replace("_lang.php", "", $file);
                $this->lang->load($shortfile, 'english');
            }
        }
        $uri = null;
        for ($i = 1; $i <= $this->uri->total_segments(); $i++) {
            $uri .= $this->uri->segment($i) . '/';
        }
        $uriSegment = rtrim($uri, '/');
        $menu_uri['menu_active_id'] = $this->admin_model->select_menu_by_uri($uriSegment);
        $menu_uri['menu_active_id'] == false || $this->session->set_userdata($menu_uri);
        $timezone = config_item('timezone');
        if (empty($timezone)) {
            $timezone = 'Australia/Sydney';
        }

        $unread_notifications = $this->db->where(array('to_user_id' => $this->session->userdata('user_id'), 'read' => 0))->get('tbl_notifications')->result();
        if (count($unread_notifications) > 0) {
            $unread_notifications = count($unread_notifications);
        } else {
            $unread_notifications = 0;
        }
        $auto_loaded_vars = array(
            'unread_notifications' => $unread_notifications,
            'd_currency' => $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row()->symbol,
        );
        $this->load->vars($auto_loaded_vars);

        date_default_timezone_set($timezone);
        set_mysql_timezone($timezone);
        check_installation();
    }
}
