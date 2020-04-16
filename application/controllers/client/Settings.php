<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Settings extends Client_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('client_model');
        $this->load->model('settings_model');
    }

    public function index()
    {
        $data['title'] = 'User List';
        $data['breadcrumbs'] = lang('settings');
        $data['page'] = lang('settings');

        $client_id = $this->session->userdata('client_id');
        // get all Client info by client id
        $this->client_model->_table_name = "tbl_client"; //table name
        $this->client_model->_order_by = "client_id";
        $data['client_info'] = $this->client_model->get_by(array('client_id' => $client_id), TRUE);
        // get all country
        $this->client_model->_table_name = "tbl_countries"; //table name
        $this->client_model->_order_by = "id";
        $data['countries'] = $this->client_model->get();

        // get all currencies
        $this->client_model->_table_name = 'tbl_currencies';
        $this->client_model->_order_by = 'name';
        $data['currencies'] = $this->client_model->get();
        // get all language
        $data['languages'] = $this->db->where('active', 1)->order_by('name', 'ASC')->get('tbl_languages')->result();

        $data['subview'] = $this->load->view('client/settings', $data, true);
        $this->load->view('client/_layout_main', $data);
    }

    public function update_settings($id)
    {
        $data = $this->client_model->array_from_post(array('name', 'email', 'short_note', 'website', 'phone', 'mobile', 'fax', 'address', 'city', 'zipcode', 'currency',
            'skype_id', 'linkedin', 'facebook', 'twitter', 'language', 'country', 'vat', 'hosting_company', 'hostname', 'port', 'password', 'username', 'client_status', 'latitude', 'longitude'));

        if (!empty($_FILES['profile_photo']['name'])) {
            $val = $this->client_model->uploadImage('profile_photo');
            $val == TRUE || redirect('client/client/new_client');
            $data['profile_photo'] = $val['path'];
        }

        $this->client_model->_table_name = 'tbl_client';
        $this->client_model->_primary_key = "client_id";
        $return_id = $this->client_model->save($data, $id);
        if (!empty($id)) {
            $id = $id;
        } else {
            $id = $return_id;
        }

        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'Clients',
            'module_field_id' => $id,
            'activity' => 'activity_added_new_company',
            'icon' => 'fa-user',
            'value1' => $data['name']
        );
        $this->client_model->_table_name = 'tbl_activities';
        $this->client_model->_primary_key = "activities_id";
        $this->client_model->save($activities);
        // messages for user
        $type = "success";
        $message = lang('client_updated');
        set_message($type, $message);
        redirect('client/settings');
    }

    public function update_profile()
    {
        $data['breadcrumbs'] = lang('settings');
        $data['title'] = lang('update_profile');
        $data['subview'] = $this->load->view('client/update_profile', $data, TRUE);
        $this->load->view('client/_layout_main', $data);
    }

    public function profile_updated()
    {
        $user_id = $this->session->userdata('user_id');

        $profile_data = $this->settings_model->array_from_post(array('fullname', 'phone', 'language', 'locale'));

        if (!empty($_FILES['avatar']['name'])) {
            $val = $this->settings_model->uploadImage('avatar');
            $val == TRUE || redirect('client/update_profile');
            $profile_data['avatar'] = $val['path'];
        }

        $this->settings_model->_table_name = 'tbl_account_details';
        $this->settings_model->_primary_key = 'user_id';
        $this->settings_model->save($profile_data, $user_id);

        $client_id = $this->input->post('client_id', TRUE);
        if (!empty($client_id)) {
            $client_data = $this->settings_model->array_from_post(array('name', 'email', 'address'));
            $this->settings_model->_table_name = 'tbl_client';
            $this->settings_model->_primary_key = 'client_id';
            $this->settings_model->save($client_data, $client_id);
        }
        $type = "success";
        $message = lang('profile_updated');
        set_message($type, $message);
        redirect('client/settings/update_profile'); //redirect page
    }

    public function set_password()
    {
        $user_id = $this->session->userdata('user_id');
        $password = $this->hash($this->input->post('old_password', TRUE));
        $check_old_pass = $this->admin_model->check_by(array('password' => $password), 'tbl_users');
        if (!empty($check_old_pass)) {
            $data['password'] = $this->hash($this->input->post('new_password'));
            $this->settings_model->_table_name = 'tbl_users';
            $this->settings_model->_primary_key = 'user_id';
            $this->settings_model->save($data, $user_id);
            $type = "success";
            $message = lang('password_updated');
        } else {
            $type = "error";
            $message = lang('password_error');
        }
        set_message($type, $message);
        redirect('client/settings/update_profile'); //redirect page
    }

    public function change_email()
    {
        $user_id = $this->session->userdata('user_id');
        $password = $this->hash($this->input->post('password', TRUE));
        $check_old_pass = $this->settings_model->check_by(array('password' => $password), 'tbl_users');
        if (!empty($check_old_pass)) {
            $new_email = $this->input->post('email', TRUE);
            if ($check_old_pass->email == $new_email) {
                $type = 'error';
                $message = lang('current_email');
            } elseif ($this->is_email_available($new_email)) {
                $data = array(
                    'new_email' => $new_email,
                    'new_email_key' => md5(rand() . microtime()),
                );
                $this->settings_model->_table_name = 'tbl_users';
                $this->settings_model->_primary_key = 'user_id';
                $this->settings_model->save($data, $user_id);
                $data['user_id'] = $user_id;
                $this->send_email_change_email($new_email, $data);
                $type = "success";
                $message = lang('succesffuly_change_email');
            } else {
                $type = "error";
                $message = lang('duplicate_email');
            }
        } else {
            $type = "error";
            $message = lang('password_error');
        }
        set_message($type, $message);
        redirect('client/settings/update_profile'); //redirect page
    }

    function send_email_change_email($email, $data)
    {
        $email_template = email_templates(array('email_group' => 'change_email'), $data['user_id'], true);
        $message = $email_template->template_body;
        $subject = $email_template->subject;

        $email_key = str_replace("{NEW_EMAIL_KEY_URL}", base_url() . 'login/reset_email/' . $data['user_id'] . '/' . $data['new_email_key'], $message);
        $new_email = str_replace("{NEW_EMAIL}", $data['new_email'], $email_key);
        $site_url = str_replace("{SITE_URL}", base_url(), $new_email);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $site_url);

        $params['recipient'] = $email;

        $params['subject'] = '[ ' . config_item('company_name') . ' ]' . ' ' . $subject;
        $params['message'] = $message;

        $params['resourceed_file'] = '';

        $this->session->set_flashdata('param', $params);
        redirect('fomailer/send_email');
    }

    function is_email_available($email)
    {

        $this->db->select('1', FALSE);
        $this->db->where('LOWER(email)=', strtolower($email));
        $this->db->or_where('LOWER(new_email)=', strtolower($email));
        $query = $this->db->get('tbl_users');

        return $query->num_rows() == 0;
    }

    public function hash($string)
    {
        return hash('sha512', $string . config_item('encryption_key'));
    }

    public function change_username()
    {
        $user_id = $this->session->userdata('user_id');
        $password = $this->hash($this->input->post('password', TRUE));
        $check_old_pass = $this->admin_model->check_by(array('password' => $password), 'tbl_users');
        if (!empty($check_old_pass)) {
            $data['username'] = $this->input->post('username', true);
            $this->settings_model->_table_name = 'tbl_users';
            $this->settings_model->_primary_key = 'user_id';
            $this->settings_model->save($data, $user_id);
            $type = "success";
            $message = lang('username_updated');
        } else {
            $type = "error";
            $message = lang('password_error');
        }
        set_message($type, $message);
        redirect('client/settings/update_profile'); //redirect page
    }

    public function activities()
    {
        $data['breadcrumbs'] = lang('activities');
        $data['title'] = lang('activities');
        $data['activities_info'] = $this->db->where(array('user' => $this->session->userdata('user_id')))->get('tbl_activities')->result();
        $data['subview'] = $this->load->view('client/activities', $data, TRUE);
        $this->load->view('client/_layout_main', $data);
    }

    public function clear_activities()
    {
        $this->db->where(array('user' => $this->session->userdata('user_id')))->delete('tbl_activities');
        $type = "success";
        $message = lang('activities_deleted');
        set_message($type, $message);
        redirect('client/dashboard');
    }


}
