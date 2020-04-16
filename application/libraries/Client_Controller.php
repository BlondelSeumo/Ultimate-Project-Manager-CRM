<?php

class Client_Controller extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        //get all navigation data
        $all_menu = $this->db->get('tbl_client_menu')->result();

        $_SESSION['user_roll'] = $all_menu;
        $user_id = $this->session->userdata('user_id');
        $client_menu = $this->global_model->select_client_roll($user_id);

        $user_type = $this->session->userdata('user_type');
        if ($user_type != 1) {
            $restricted_link = array();
            foreach ($all_menu as $data1) {
                $duplicate = false;
                foreach ($client_menu as $data2) {
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
            if ($user_flag != 2) {
                $url = $this->session->userdata('url');
                redirect($url);
            }
        } else {
            redirect('locked');
        }

        $uri = null;
        $except_uri = array('client/settings/update_profile', 'client/settings/profile_updated', 'client/settings/set_password', 'client/settings/change_email', 'client/settings/change_username', 'client/settings/activities');
        if (in_array(uri_string(), $except_uri) == false) {
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
