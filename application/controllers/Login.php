<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Login extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin_model');
    }

    public function index()
    {
        //$this->session->sess_destroy();

        $dashboard = $this->session->userdata('url');

        $this->login_model->loggedin() == FALSE || redirect($dashboard);

        $rules = $this->login_model->rules;
        $this->form_validation->set_rules($rules);
        if (config_item('recaptcha_secret_key') != '' && config_item('recaptcha_site_key') != '') {
            $this->form_validation->set_rules('g-recaptcha-response', 'Captcha', 'callback_recaptcha');
        }
        if ($this->form_validation->run() == TRUE) {
            // We can login and redirect
            if ($this->login_model->login() == TRUE) {
                redirect($dashboard);
            } else {
                $this->session->set_flashdata('error', lang('incorrect_email_or_username'));
                redirect('login');
            }
        }
        $data['title'] = lang('welcome_to') . ' ' . config_item('company_name');
        $data['subview'] = $this->load->view('login/login_form', $data, TRUE);
        $this->load->view('login', $data);
    }

    public function register()
    {
        $data['title'] = lang('welcome_to') . ' ' . config_item('company_name');
        $data['subview'] = $this->load->view('login/register', $data, TRUE);
        $this->load->view('login', $data);
    }

    public function forgot_password()
    {
        $data['title'] = lang('welcome_to') . ' ' . config_item('company_name');
        $flag = $this->input->post('flag', TRUE);
        if (!empty($flag)) {
            $login_details = $this->login_model->get_user_details($this->input->post('email_or_username', TRUE));
            if (!empty($login_details)) {
                $data = array(
                    'user_id' => $login_details->user_id,
                    'username' => $login_details->username,
                    'email' => $login_details->email,
                    'new_pass_key' => md5(rand() . microtime()),
                );
                $this->login_model->set_password_key($login_details->user_id, $data['new_pass_key']);

                $this->send_email('forgot_password', $data['email'], $data);
                $type = 'success';
                $message = lang('message_new_password_confi');
                set_message($type, $message);
                redirect('login');
            } else {
                $type = 'error';
                $message = lang('email_or_usernme_error');
                set_message($type, $message);
                redirect('login/forgot_password');
            }
        }
        $data['subview'] = $this->load->view('login/forgot_password', $data, TRUE);
        $this->load->view('login', $data);
    }

    public function reset_password($user_id, $new_pass_key)
    {
        $data['title'] = lang('welcome_to') . ' ' . config_item('company_name');
        $check_reset_pass = $this->login_model->can_reset_password($user_id, $new_pass_key, config_item('forgot_password_expire'));

        if ($check_reset_pass == true) {
            $new_password = $this->input->post('new_password', true);
            if (!empty($new_password)) {
                $this->login_model->get_reset_password($user_id, $new_password, $new_pass_key, config_item('forgot_password_expire'));
                $login_details = $this->db->where('user_id', $user_id)->get('tbl_users')->row();
                $data = array(
                    'username' => $login_details->username,
                    'email' => $login_details->email,
                    'new_password' => $new_password,
                );
                // Send email with new password
                $this->send_email('reset_password', $data['email'], $data);
                $type = 'success';
                $message = lang('message_new_password_sent');
                set_message($type, $message);
                redirect('login');
            }
        } else {
            $type = 'error';
            $message = lang('message_expire');
            set_message($type, $message);
            redirect('login');
        }
        $data['user_id'] = $user_id;
        $data['new_pass_key'] = $new_pass_key;
        $data['subview'] = $this->load->view('login/reset_password', $data, TRUE);
        $this->load->view('login', $data);
    }

    public function registered_user()
    {
        $user_data = $this->login_model->array_from_post(array('email', 'username'));
        $check_email = $this->db->where(array('email' => $user_data['email']))->get('tbl_users')->row();
        $check_username = $this->db->where(array('username' => $user_data['username']))->get('tbl_users')->row();
        if (!empty($check_email) || !empty($check_username)) {
            $type = 'error';
            if (!empty($check_email)) {
                $message = lang('existing_email');
            } else {
                $message = lang('existing_username');
            }
            set_message($type, $message);
            redirect('login/register');
        } else {
            $user_data['password'] = $this->login_model->hash($this->input->post('password', true));
            $user_data['new_email_key'] = md5(rand() . microtime());
            $user_data['role_id'] = 2;
            $user_data['last_ip'] = $this->input->ip_address;
            $user_data['created'] = date('Y-m-d H:i:s');
            $this->login_model->_table_name = 'tbl_users';
            $this->login_model->_primary_key = 'user_id';
            $user_data['user_id'] = $this->login_model->save($user_data);

            // save into client table
            $client = $this->login_model->array_from_post(array('name', 'email', 'language'));
            $client['primary_contact'] = 0;
            $this->login_model->_table_name = 'tbl_client';
            $this->login_model->_primary_key = 'client_id';
            $profile['company'] = $this->login_model->save($client);
            $profile['user_id'] = $user_data['user_id'];
            $profile['fullname'] = $this->input->post('name', TRUE);

            $RTL = config_item('RTL');
            if (!empty($RTL)) {
                $direction = 'rtl';
            } else {
                $direction = 'ltr';
            }
            $profile['direction'] = $direction;

            $this->login_model->_table_name = 'tbl_account_details';
            $this->login_model->_primary_key = 'account_details_id';
            $this->login_model->save($profile);


            $user_data['activation_period'] = config_item('email_activation_expire') / 3600;
            $user_data['password'] = $this->input->post('password', true);
            $this->send_email('activate', $user_data['email'], $user_data);
            $client_default_menu = unserialize(config_item('client_default_menu'));
            if (!empty($client_default_menu)) {
                if (!empty($client_default_menu['client_default_menu'])) {
                    $client_menu = $client_default_menu['client_default_menu'];
                } else {
                    $client_menu = array('17', '6');
                }
            } else {
                $client_menu = array('17', '6');
            }
            foreach ($client_menu as $v_menu) {
                $client_role_data['menu_id'] = $v_menu;
                $client_role_data['user_id'] = $user_data['user_id'];
                $this->login_model->_table_name = 'tbl_client_role';
                $this->login_model->_primary_key = 'client_role_id';
                $this->login_model->save($client_role_data);
            }
            $type = 'success';
            $message = lang('registration_success');
            set_message($type, $message);
            redirect('login');
        }
    }

    function send_email($type, $email, &$data)
    {
        switch ($type) {
            case 'activate':
                return $this->send_activation_email($email, $data);
                break;
            case 'forgot_password':
                return $this->send_email_forgot_password($email, $data);
                break;
            case 'reset_password':
                return $this->send_email_reset_password($email, $data);
                break;
        }
    }

    function send_activation_email($email, $data)
    {

        $email_template = email_templates(array('email_group' => 'activate_account'), $data['user_id'], true);

        $activate_url = str_replace("{ACTIVATE_URL}", site_url('/login/activate/' . $data['user_id'] . '/' . $data['new_email_key']), $email_template->template_body);
        $activate_period = str_replace("{ACTIVATION_PERIOD}", $data['activation_period'], $activate_url);
        $username = str_replace("{USERNAME}", $data['username'], $activate_period);
        $user_email = str_replace("{EMAIL}", $data['email'], $username);
        $user_password = str_replace("{PASSWORD}", $data['password'], $user_email);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $user_password);

        $params['recipient'] = $email;
        $params['subject'] = '[ ' . config_item('company_name') . ' ]' . ' ' . $email_template->subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';

        $this->login_model->send_email($params);
    }

    function activate($user_id, $new_email_key)
    {
        // Activate user
        if ($this->login_model->activate_user($user_id, $new_email_key)) {  // success
            $this->logout();
            $type = 'success';
            $message = lang('activation_completed');
            set_message($type, $message);
            redirect('login');
        } else {
            // fail            
            $type = 'error';
            $message = lang('activation_failed');
            set_message($type, $message);
            redirect('login');
        }
    }

    function send_email_forgot_password($email, $data)
    {
        $email_template = email_templates(array('email_group' => 'forgot_password'), $data['user_id'], true);

        $message = $email_template->template_body;
        $subject = $email_template->subject;

        $site_url = str_replace("{SITE_URL}", base_url() . 'login', $message);
        $key_url = str_replace("{PASS_KEY_URL}", base_url() . 'login/reset_password/' . $data['user_id'] . '/' . $data['new_pass_key'], $site_url);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $key_url);

        $params['recipient'] = $email;

        $params['subject'] = '[ ' . config_item('company_name') . ' ] ' . $subject;
        $params['message'] = $message;

        $params['resourceed_file'] = '';

        $this->login_model->send_email($params);
    }

    function send_email_reset_password($email, $data)
    {
        $email_template = email_templates(array('email_group' => 'reset_password'));

        $message = $email_template->template_body;
        $subject = $email_template->subject;

        $username = str_replace("{USERNAME}", $data['username'], $message);
        $user_email = str_replace("{EMAIL}", $data['email'], $username);
        $user_password = str_replace("{NEW_PASSWORD}", $data['new_password'], $user_email);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $user_password);

        $params['recipient'] = $email;

        $params['subject'] = '[ ' . config_item('company_name') . ' ]' . $subject;
        $params['message'] = $message;

        $params['resourceed_file'] = '';

        $this->login_model->send_email($params);
    }

    public function reset_email($user_id, $new_email_key)
    {
        // Reset email
        if ($this->login_model->activate_new_email($user_id, $new_email_key)) { // success
            $this->logout();
            $type = 'success';
            $message = lang('new_email_activated');
        } else {                // fail
            $type = 'success';
            $message = lang('new_email_failed');
        }
        set_message($type, $message);
        redirect('login');
    }

    function activate_new_email($user_id, $new_email_key)
    {
        if ((strlen($user_id) > 0) && (strlen($new_email_key) > 0)) {
            return $this->login_model->activate_new_email($user_id, $new_email_key);
        }
        return FALSE;
    }

    public function not_found()
    {
        $this->load->view('admin/settings/not_found');
    }

    public function logout()
    {
        $this->login_model->logout();
        redirect('login');
    }

    public function recaptcha($str = '')
    {
        return do_recaptcha_validation($str);
    }

}
