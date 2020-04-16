<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class User extends Client_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('client_model');
    }

    public function user_list($action = NULL, $id = NULL)
    {

        $data['active'] = 1;
        // get all language
        $data['languages'] = $this->db->where('active', 1)->order_by('name', 'ASC')->get('tbl_languages')->result();
        // get all location
        $this->client_model->_table_name = 'tbl_locales';
        $this->client_model->_order_by = 'name';
        $data['locales'] = $this->client_model->get();

        $data['title'] = 'User List';
        $data['page'] = lang('users');
        $data['breadcrumbs'] = lang('users');

        $client_id = $this->session->userdata('client_id');
        if (!empty($client_id) && $client_id != '-') {
            $data['all_user_info'] = $this->client_model->get_client_contacts($client_id);
        }
        $data['subview'] = $this->load->view('client/user/user_list', $data, true);
        $this->load->view('client/_layout_main', $data);
    }

    public function usersList()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_account_details';
            $this->datatables->join_table = array('	tbl_users');
            $this->datatables->join_where = array('tbl_account_details.user_id=tbl_users.user_id');
            $this->datatables->column_order = array('tbl_users.username','fullname', 'employment_id', 'company', 'locale', 'language', 'phone', 'mobile', 'skype');
            $this->datatables->column_search = array('tbl_users.username','fullname', 'employment_id', 'company', 'locale', 'language', 'phone', 'mobile', 'skype');
            $this->datatables->order = array('tbl_account_details.user_id' => 'desc');
            // get all invoice
            $client_id = $this->session->userdata('client_id');
            $where = array('company' => $client_id);
            $fetch_data = make_datatables($where);
            $data = array();
            foreach ($fetch_data as $_key => $v_user) {
                $v_user = get_staff_details($v_user->user_id);
                $sub_array = array();
                $sub_array[] = '<img style="width: 36px;margin-right: 10px;" src="' . base_url() . staffImage($v_user->user_id) . '" class="img-circle">';;
                $sub_array[] = $v_user->fullname;
                $sub_array[] = $v_user->username;
                $sub_array[] = $v_user->phone;
                $sub_array[] = $v_user->mobile;
                $sub_array[] = $v_user->skype;
                if ($v_user->last_login == '0000-00-00 00:00:00' || empty($v_user->last_login)) {
                    $login_time = "-";
                } else {
                    $login_time = strftime(config_item('date_format'), strtotime($v_user->last_login)) . ' ' . display_time($v_user->last_login);
                }
                $sub_array[] = $login_time;
                $active = null;
                if ($v_user->activated == 1) {
                    $active = '<span class="label label-success">' . lang('active') . '</span>';
                } else {
                    $active = '<span class="label label-danger">' . lang('deactive') . '</span>';
                }
                $sub_array[] = $active;
                $data[] = $sub_array;
            }
            render_table($data, $where);
        } else {
            redirect('client/dashboard');
        }
    }

    public function user_details($id,$active = null)
    {
        $data['title'] = lang('user_details');
        $data['id'] = $id;
        if (!empty($active)) {
            $data['active'] = $active;
        } else {
            $data['active'] = 1;
        }
        $data['breadcrumbs'] = lang('my_details').'-'.fullname();
        $data['subview'] = $this->load->view('client/user/user_details', $data, TRUE);
        $this->load->view('client/_layout_main', $data);
    }

    /*     * * Save New User ** */

    public function save_user()
    {
        $data = $this->client_model->array_from_post(array('fullname', 'phone', 'mobile', 'skype', 'language', 'locale'));
        $data['company'] = $this->session->userdata('client_id');
        $user_data = $this->client_model->array_from_post(array('email', 'username', 'password',));
        $check_email = $this->client_model->check_by(array('email' => $user_data['email']), 'tbl_users');
        $check_username = $this->client_model->check_by(array('username' => $user_data['username']), 'tbl_users');

        if ($user_data['password'] == $this->input->post('confirm_password', true)) {
            $u_data['password'] = $this->hash($user_data['password']);

            if (!empty($check_username)) {
                $message['error'][] = 'This Username Already Used ! ';
            } else {
                $u_data['username'] = $user_data['username'];
            }
            if (!empty($check_email)) {
                $message['error'][] = 'This email Address Already Used ! ';
            } else {
                $u_data['email'] = $user_data['email'];
            }
        } else {
            $message['error'][] = 'Sorry Your Password and Confirm Password Does not match !';
        }

        if (!empty($u_data['password']) && !empty($u_data['username']) && !empty($u_data['email'])) {

            $u_data['role_id'] = 2;
            $u_data['activated'] = 1;

            $this->client_model->_table_name = 'tbl_users';
            $this->client_model->_primary_key = 'user_id';
            $user_id = $this->client_model->save($u_data);

            $data['user_id'] = $user_id;

            $this->client_model->_table_name = 'tbl_account_details';
            $this->client_model->_primary_key = 'account_details_id';
            $return_id = $this->client_model->save($data);
            // check primary contact
            $primary_contact = $this->client_model->check_by(array('client_id' => $data['company']), 'tbl_client');

            if ($primary_contact->primary_contact == 0) {
                $c_data['primary_contact'] = $return_id;
                $this->client_model->_table_name = 'tbl_client';
                $this->client_model->_primary_key = 'client_id';
                $this->client_model->save($c_data, $data['company']);
            }

            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'Add Contact',
                'module_field_id' => $user_id,
                'activity' => 'activity_added_new_contact',
                'icon' => 'fa-user',
                'value1' => $data['fullname']
            );
            $this->client_model->_table_name = 'tbl_activities';
            $this->client_model->_primary_key = "activities_id";
            $this->client_model->save($activities);
        }

        $message['success'] = lang('save_user_info');
        if (!empty($message['error'])) {
            $this->session->set_userdata($message);
            redirect('client/user/user_list'); //redirect page
        } else {

            $this->session->set_userdata($message);
            redirect('client/user/user_list'); //redirect page
        }
    }

    public function hash($string)
    {
        return hash('sha512', $string . config_item('encryption_key'));
    }

    // crud for sidebar todo list
    function todo($task = '', $todo_id = '', $swap_with = '')
    {
        if ($task == 'add') {
            $this->add_todo();
        }
        if ($task == 'reload_incomplete_todo') {
            $this->get_incomplete_todo();
        }
        if ($task == 'mark_as_done') {
            $this->mark_todo_as_done($todo_id);
        }
        if ($task == 'mark_as_undone') {
            $this->mark_todo_as_undone($todo_id);
        }
        if ($task == 'swap') {

            $this->swap_todo($todo_id, $swap_with);
        }
        if ($task == 'delete') {
            $this->delete_todo($todo_id);
        }
        $todo['opened'] = 1;
        $this->session->set_userdata($todo);
        redirect('client/dashboard/');
    }

    function add_todo()
    {
        $data['title'] = $this->input->post('title', true);
        $data['user_id'] = $this->session->userdata('user_id');

        $this->db->insert('tbl_todo', $data);
        $todo_id = $this->db->insert_id();

        $data['order'] = $todo_id;
        $this->db->where('todo_id', $todo_id);
        $this->db->update('tbl_todo', $data);
    }

    function mark_todo_as_done($todo_id = '')
    {
        $data['status'] = 1;
        $this->db->where('todo_id', $todo_id);
        $this->db->update('tbl_todo', $data);
    }

    function mark_todo_as_undone($todo_id = '')
    {
        $data['status'] = 0;
        $this->db->where('todo_id', $todo_id);
        $this->db->update('tbl_todo', $data);
    }

    function swap_todo($todo_id = '', $swap_with = '')
    {
        $counter = 0;
        $temp_order = $this->db->get_where('tbl_todo', array('todo_id' => $todo_id))->row()->order;
        $user = $this->session->userdata('user_id');

        // Move current todo up.
        if ($swap_with == 'up') {

            // Fetch all todo lists of current user in ascending order.
            $this->db->order_by('order', 'ASC');
            $todo_lists = $this->db->get_where('tbl_todo', array('user_id' => $user))->result_array();
            $array_length = count($todo_lists);

            // Create separate array for orders and todo_id's from above array.
            foreach ($todo_lists as $todo_list) {
                $id_list[] = $todo_list['todo_id'];
                $order_list[] = $todo_list['order'];
            }
        }

        // Move current todo down.
        if ($swap_with == 'down') {

            // Fetch all todo lists of current user in descending order.
            $this->db->order_by('order', 'DESC');
            $todo_lists = $this->db->get_where('tbl_todo', array('user_id' => $user))->result_array();
            $array_length = count($todo_lists);

            // Create separate array for orders and todo_id's from above array.
            foreach ($todo_lists as $todo_list) {
                $id_list[] = $todo_list['todo_id'];
                $order_list[] = $todo_list['order'];
            }
        }

        // Swap orders between current and next/previous todo.
        for ($i = 0; $i < $array_length; $i++) {
            if ($temp_order == $order_list[$i]) {
                if ($counter > 0) {
                    $swap_order = $order_list[$i - 1];
                    $swap_id = $id_list[$i - 1];

                    // Update order of current todo.
                    $data['order'] = $swap_order;
                    $this->db->where('todo_id', $todo_id);
                    $this->db->update('tbl_todo', $data);

                    // Update order of next/previous todo.
                    $data['order'] = $temp_order;
                    $this->db->where('todo_id', $swap_id);
                    $this->db->update('tbl_todo', $data);
                }
            } else
                $counter++;
        }
    }

    function delete_todo($todo_id = '')
    {
        $this->db->where('todo_id', $todo_id);
        $this->db->delete('tbl_todo');
    }

    function get_incomplete_todo()
    {
        $user = $this->session->userdata('user_id');
        $this->db->where('user_id', $user);
        $this->db->where('status', 0);
        $query = $this->db->get('tbl_todo');

        $incomplete_todo_number = $query->num_rows();
        if ($incomplete_todo_number > 0) {
            echo '<span class="badge badge-secondary">';
            echo $incomplete_todo_number;
            echo '</span>';
        }
    }

}
