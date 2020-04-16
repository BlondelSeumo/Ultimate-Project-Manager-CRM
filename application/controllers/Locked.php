<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Locked extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('admin_model');
		$user_id = $this->session->userdata('user_id');
		if($user_id == NULL){
			redirect('login');
		}
    }
    public function index() {
        $data['title'] = lang('welcome_to') . ' ' . config_item('company_name');
        $this->load->view('login/lock_screen', $data);
    }

    public function check_login($username) {
        $password = $this->input->post('password', TRUE);
        //check user type
        $this->admin_model->_table_name = 'tbl_users';
        $this->admin_model->_order_by = 'user_id';

        $admin = $this->admin_model->get_by(array(
            'username' => $username,
            'password' => $this->hash($password),
                ), TRUE);
        if (!empty($admin) && $admin->activated == 1 && $admin->banned == 0) {
            if ($admin->role_id != '2') {
                $data = array(
                    'user_flag' => 1,
                );
                $this->session->set_userdata($data);
                redirect('admin/dashboard');
            } else {
                $data = array(
                    'user_flag' => 2,
                );
                $this->session->set_userdata($data);
                redirect('client/dashboard');
            }
        } else {
            $this->session->set_flashdata('error', lang('incorrect_password'));
            redirect('locked');
        }
    }

    public function lock_screen() {
        $this->session->unset_userdata('user_flag');
        redirect('locked');
    }

    public function hash($string) {
        return hash('sha512', $string . config_item('encryption_key'));
    }

}
