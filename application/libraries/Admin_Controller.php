<?php

/**
 * Description of Admin_Controller
 *
 * @author pc mart ltd
 */
class Admin_Controller extends MY_Controller
{
    private $_current_version;

    function __construct()
    {
        parent::__construct();
        $this->load->model('global_model');
        $this->load->model('admin_model');

        $this->_current_version = $this->admin_model->get_current_db_version();

        if ($this->admin_model->is_db_upgrade_required($this->_current_version) && !$this->input->post('auto_update',true)) {
            if ($this->input->post('upgrade_database',true)) {
                $this->admin_model->upgrade_database();
            }
            include_once(APPPATH . 'views/admin/settings/db_update_required.php');
            die;
        }
        if (strpos($this->uri->uri_string(), 'login') === FALSE) {
            $this->session->set_userdata(array(
                'url' => $this->uri->uri_string()
            ));
        }

        //get all navigation data
        $all_menu = $this->db->get('tbl_menu')->result();

        $_SESSION['user_roll'] = $all_menu;
        //get user id from session
        $designations_id = $this->session->userdata('designations_id');
        $this->global_model->_table_name = 'tbl_user_role'; //table name
        $this->global_model->_order_by = 'user_role_id';
        // get user navigation by user id
        $user_menu = $this->global_model->select_user_roll($designations_id);

        $user_type = $this->session->userdata('user_type');

        if ($user_type != 1) {
            $restricted_link = array();
            foreach ($all_menu as $data1) {
                $duplicate = false;
                foreach ($user_menu as $data2) {
                    if ($data1->menu_id === $data2->menu_id) {
                        $duplicate = true;
                    }
                }
                if ($duplicate === false) {
                    $restricted_link[] = $data1->link;
                }
            }
            $exception_uris = $restricted_link;
        } else {
            $exception_uris = array();
        }
        $user_flag = $this->session->userdata('user_flag');
        if (!empty($user_flag)) {
            if ($user_flag != '1') {
                $url = $this->session->userdata('url');
                redirect($url);
            }
        } else {
            redirect('locked');
        }

        $uri = null;
        $a = $this->uri->segment(1) . '/' . $this->uri->segment(2);
        if ($a != 'admin/settings') {
            for ($i = 1; $i <= $this->uri->total_segments(); $i++) {
                $uri .= $this->uri->segment($i) . '/';
                $result = rtrim($uri, '/');
                if (in_array($result, $exception_uris) == true) {
                    redirect('404');
                }
            }
        }

    }

}
