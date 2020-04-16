<?php
class Login_Model extends MY_Model
{

    public $_table_name;
    protected $_order_by;
    public $_primary_key;
    public $rules = array(
        'user_name' => array(
            'field' => 'user_name',
            'label' => 'User Name',
            'rules' => 'trim|required'
        ),
        'password' => array(
            'field' => 'password',
            'label' => 'Password',
            'rules' => 'trim|required'
        )
    );


    public function login()
    {
        //check user type
        $this->_table_name = 'tbl_users';
        $this->_order_by = 'user_id';

        $admin = $this->get_by(array(
            'username' => $this->input->post('user_name', true),
            'password' => $this->hash($this->input->post('password', true)),
        ), TRUE);
        if (!empty($admin) && $admin->activated == 1 && $admin->banned == 0) {
            $user_info = $this->check_by(array('user_id' => $admin->user_id), 'tbl_account_details');
            $url = $this->session->userdata('url');
            if (!empty($user_info->direction)) {
                $direction = $user_info->direction;
            } else {
                $RTL = config_item('RTL');
                if (!empty($RTL)) {
                    $direction = 'rtl';
                }
            }
            if (empty($direction)) {
                $direction = 'ltr';
            }
            if ($admin->role_id != '2') {
                $mark_attendance = $this->input->post('mark_attendance', true);
                if (!empty($mark_attendance)) {
                    $user_id = $admin->user_id;
                    $attendance_info = $this->db->where('user_id', $user_id)->get('tbl_attendance')->result();
                    foreach ($attendance_info as $v_info) {
                        $all_clocking[] = $this->admin_model->check_by(array('attendance_id' => $v_info->attendance_id, 'clocking_status' => 1), 'tbl_clock');
                    }
                    if (!empty($all_clocking)) {
                        foreach ($all_clocking as $v_clocking) {
                            if (!empty($v_clocking)) {
                                $clocking = $v_clocking;
                            }
                        }
                    }
                    if (!empty($clocking)) {
                        $this->set_clocking($clocking->clock_id, $admin->user_id, 0, true);
                        $clock_in = 'clock_out_message';
                    } else {
                        $clock_in = 'clock_in_message';
                        $this->set_clocking(0, $admin->user_id, true, true);
                    }
                    if ($admin->role_id == 1) {
                        $url = 'admin/mark_attendance';
                    } else {
                        $ss_data['c_message'] = $clock_in;
                        $this->session->set_userdata($ss_data);
                        redirect('login');

                    }
                }

                $data = array(
                    'user_name' => $admin->username,
                    'email' => $admin->email,
                    'name' => $user_info->fullname,
                    'photo' => $user_info->avatar,
                    'designations_id' => $user_info->designations_id,
                    'user_id' => $admin->user_id,
                    'last_login' => $admin->last_login,
                    'online_time' => time(),
                    'loggedin' => TRUE,
                    'user_type' => $admin->role_id,
                    'user_flag' => 1,
                    'direction' => $direction,
                    'url' => (!empty($url) ? $url : 'admin/dashboard'),
                );

                $this->session->set_userdata($data);
            } else {
                if ($user_info->company != '-') {
                    if (empty($url)) {
                        $client_menu = $this->global_model->select_client_roll($admin->user_id);
                        $url = $client_menu[0]->link;
                    }
                    $data = array(
                        'user_name' => $admin->username,
                        'email' => $admin->email,
                        'name' => $user_info->fullname,
                        'photo' => $user_info->avatar,
                        'client_id' => $user_info->company,
                        'user_id' => $admin->user_id,
                        'last_login' => $admin->last_login,
                        'online_time' => time(),
                        'loggedin' => TRUE,
                        'user_type' => $admin->role_id,
                        'user_flag' => 2,
                        'direction' => $direction,
                        'url' => (!empty($url) ? $url : 'client/dashboard'),
                    );
                    $this->session->set_userdata($data);
                }
            }
        }

    }

    public function set_clocking($id = NULL, $user_id = null, $row = null, $redirect = null)
    {
        if (!empty(attendance_access())) {
            if ($id == 0) {
                $id = null;
            }
            if ($row == 0) {
                $row = null;
            }
            // sate into attendance table
            if (!empty($user_id)) {
                $adata['user_id'] = $user_id;
            } else {
                $adata['user_id'] = $this->session->userdata('user_id');
            }
            if (!empty($row)) {
                $clocktime = 1;
            } elseif (!empty($id)) {
                $clocktime = 2;
            } else {
                $clocktime = 1;
            }

            $date = date('Y-m-d');
            $time = date('H:i:s');;
            if ($clocktime == 1) {
                $adata['date_in'] = $date;
                $adata['date_out'] = $date;
            } else {
                $adata['date_out'] = $date;
            }
            if (!empty($adata['date_in'])) {
                // check existing date is here or not
                $check_date = $this->admin_model->check_by(array('user_id' => $adata['user_id'], 'date_in' => $adata['date_in']), 'tbl_attendance');
            }
            if (!empty($check_date)) { // if exis do not save date and return id
                $this->admin_model->_table_name = "tbl_attendance"; // table name
                $this->admin_model->_primary_key = "attendance_id"; // $id

                if ($check_date->attendance_status != '1') {
                    $udata['attendance_status'] = 1;
                    $this->admin_model->save($udata, $check_date->attendance_id);
                }
                if ($check_date->clocking_status == 0) {
                    $udata['date_out'] = $date;
                    $udata['clocking_status'] = 1;
                    $this->admin_model->save($udata, $check_date->attendance_id);
                }
                $data['attendance_id'] = $check_date->attendance_id;
            } else { // else save data into tbl attendance
                // get attendance id by clock id into tbl clock
                // if attendance id exist that means he/she clock in
                // return the id
                // and update the day out time
                $check_existing_data = $this->admin_model->check_by(array('clock_id' => $id), 'tbl_clock');
                $this->admin_model->_table_name = "tbl_attendance"; // table name
                $this->admin_model->_primary_key = "attendance_id"; // $id
                if (!empty($check_existing_data)) {
                    $adata['clocking_status'] = 0;
                    $this->admin_model->save($adata, $check_existing_data->attendance_id);
                } else {
                    $adata['attendance_status'] = 1;
                    $adata['clocking_status'] = 1;
                    //save data into attendance table
                    $data['attendance_id'] = $this->admin_model->save($adata);
                }
            }
            // save data into clock table
            if ($clocktime == 1) {
                $data['clockin_time'] = $time;
            } else {
                $data['clockout_time'] = $time;
                $data['comments'] = $this->input->post('comments', TRUE);
            }
            $data['ip_address'] = $this->input->ip_address();
            //save data in database
            $this->admin_model->_table_name = "tbl_clock"; // table name
            $this->admin_model->_primary_key = "clock_id"; // $id
            if (!empty($id)) {
                $data['clocking_status'] = 0;
                $this->admin_model->save($data, $id);
            } else {
                $data['clocking_status'] = 1;
                $id = $this->admin_model->save($data);
                if (!empty($check_date)) {
                    if ($check_date->clocking_status == 1) {
                        $data['clockout_time'] = $time;
                        $data['clocking_status'] = 0;
                        $this->admin_model->save($data, $id);
                    }
                }
            }
        } else {
            set_message('error', 'please contact with admin to clock in');
        }
        if (empty($redirect)) {
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            return true;
        }
    }

    public function activate_user($user_id, $activation_key, $activate_by_email = TRUE)
    {
        $this->purge_na($this->config->item('email_activation_expire'));
        if ((strlen($user_id) > 0) && (strlen($activation_key) > 0)) {
            return $this->activated_user($user_id, $activation_key, $activate_by_email);
        }
        return FALSE;
    }

    function purge_na($expire_period = 172800)
    {
        $this->db->where('activated', 0);
        $this->db->where('UNIX_TIMESTAMP(created) <', time() - $expire_period);
        $this->db->delete('tbl_users');
    }

    function activated_user($user_id, $activation_key, $activate_by_email)
    {

        $this->db->select('1', FALSE);
        $this->db->where('user_id', $user_id);
        if ($activate_by_email) {
            $this->db->where('new_email_key', $activation_key);
        } else {
            $this->db->where('new_password_key', $activation_key);
        }
        $this->db->where('activated', 0);
        $query = $this->db->get('tbl_users');

        if ($query->num_rows() == 1) {
            $this->db->set('activated', 1);
            $this->db->set('new_email_key', NULL);
            $this->db->where('user_id', $user_id);
            $this->db->update('tbl_users');
            return TRUE;
        }
        return FALSE;
    }

    function get_user_details($login)
    {
        $this->db->where('LOWER(username)=', strtolower($login));
        $this->db->or_where('LOWER(email)=', strtolower($login));

        $query = $this->db->get('tbl_users');
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function set_password_key($user_id, $new_pass_key)
    {
        $this->db->set('new_password_key', $new_pass_key);
        $this->db->set('new_password_requested', date('Y-m-d H:i:s'));
        $this->db->where('user_id', $user_id);
        $this->db->update('tbl_users');
        return $this->db->affected_rows() > 0;
    }

    function get_user_by_id($user_id, $activated)
    {
        $this->db->where('id', $user_id);
        $this->db->where('activated', $activated ? 1 : 0);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function can_reset_password($user_id, $new_pass_key, $expire_period = 900)
    {
//        $this->db->select('1', FALSE);
        $this->db->where('user_id', $user_id);
        $this->db->where('new_password_key', $new_pass_key);
        $this->db->where('UNIX_TIMESTAMP(new_password_requested) >', time() - $expire_period);
        $query = $this->db->get('tbl_users');
        return $query->num_rows() == 1;
    }

    function get_reset_password($user_id, $new_password, $new_pass_key, $expire_period = 900)
    {
        $this->db->set('password', $this->hash($new_password));
        $this->db->set('new_password_key', NULL);
        $this->db->set('new_password_requested', NULL);
        $this->db->where('user_id', $user_id);
        $this->db->where('new_password_key', $new_pass_key);
        $this->db->where('UNIX_TIMESTAMP(new_password_requested) >=', time() - $expire_period);
        $this->db->update('tbl_users');
        return $this->db->affected_rows() > 0;
    }

    function activate_new_email($user_id, $new_email_key)
    {
        $this->db->set('email', 'new_email', FALSE);
        $this->db->set('new_email', NULL);
        $this->db->set('new_email_key', NULL);
        $this->db->where('user_id', $user_id);
        $this->db->where('new_email_key', $new_email_key);
        $this->db->update('tbl_users');
        return $this->db->affected_rows() > 0;
    }

    public function logout($not_clock = null)
    {
//        $this->output->clear_all_cache();
        if (empty($not_clock)) {
            $this->clock_out();
        }
        $this->tasks_timer_stop();
        $this->project_timer_stop();
        if (!empty($not_clock)) {
            $sessionData = $this->session->all_userdata();
            foreach ($sessionData as $key => $val) {
                if ($key != 'c_message') {
                    $this->session->unset_userdata($key);
                }
            }
        } else {
            $user_id = $this->session->userdata('user_id');
            update('tbl_users', array('user_id' => $user_id), array('last_login' => date("Y-m-d H:i:s")));
            $this->session->sess_destroy();
        }
    }

    function clock_out()
    {
        $a_where = array('user_id' => $this->session->userdata('user_id'), 'clocking_status' => '1');
        $all_attendance = $this->db->where($a_where)->get('tbl_attendance')->result();
        $all_clock_out = array();
        if (!empty($all_attendance)) {
            foreach ($all_attendance as $v_attendance) {
                $where = array('attendance_id' => $v_attendance->attendance_id, 'clocking_status' => 1);
                array_push($all_clock_out, $this->db->where($where)->get('tbl_clock')->row());
            }
        }

        if (!empty($all_clock_out)) {
            foreach ($all_clock_out as $clock_out) {
                if (!empty($clock_out)) {
                    $attendance_info = $this->db->where('attendance_id', $clock_out->attendance_id)->get('tbl_attendance')->row();

                    if (empty($attendance_info->date_in)) {
                        $adata['date_in'] = date('Y-m-d');
                    }
                    $adata['clocking_status'] = 0;
                    $adata['date_out'] = date('Y-m-d');

                    $this->_table_name = "tbl_attendance"; // table name
                    $this->_primary_key = "attendance_id"; // $id
                    $this->save($adata, $clock_out->attendance_id);
                    if (empty($clock_out->clockin_time)) {
                        $data['clockin_time'] = date('H:i:s');
                    }
                    $data['clockout_time'] = date('H:i:s');
                    $data['clocking_status'] = 0;

                    $this->_table_name = "tbl_clock"; // table name
                    $this->_primary_key = "clock_id"; // $id
                    $this->save($data, $clock_out->clock_id);
                }
            }
        }
        return true;
    }

    function tasks_timer_stop()
    {
        $user_id = $this->session->userdata('user_id');
        $all_task_info = $this->db->where('timer_started_by', $user_id)->get('tbl_task')->result();
        if (!empty($all_task_info)) {
            foreach ($all_task_info as $task_start) {
                $task_logged_time = $this->task_spent_time_by_id($task_start->task_id);
                $time_logged = (time() - $task_start->start_time) + $task_logged_time; //time already logged

                $data = array(
                    'timer_status' => 'off',
                    'logged_time' => $time_logged,
                    'start_time' => ''
                );
                // Update into tbl_task
                $this->_table_name = "tbl_task"; //table name
                $this->_primary_key = "task_id";
                $this->save($data, $task_start->task_id);
                // save into tbl_task_timer
                $t_data = array(
                    'task_id' => $task_start->task_id,
                    'user_id' => $this->session->userdata('user_id'),
                    'start_time' => $task_start->start_time,
                    'end_time' => time()
                );
                // insert into tbl_task_timer
                $this->_table_name = "tbl_tasks_timer"; //table name
                $this->_primary_key = "tasks_timer_id";
                $this->save($t_data);
                return true;
            }
        }
    }

    function project_timer_stop()
    {
        $user_id = $this->session->userdata('user_id');
        $all_task_info = $this->db->where('timer_started_by', $user_id)->get('tbl_project')->result();
        if (!empty($all_task_info)) {
            foreach ($all_task_info as $task_start) {

                $task_logged_time = $this->task_spent_time_by_id($task_start->project_id);

                $time_logged = (time() - $task_start->start_time) + $task_logged_time; //time already logged

                $data = array(
                    'timer_status' => 'off',
                    'logged_time' => $time_logged,
                    'start_time' => ''
                );
                // Update into tbl_task
                $this->_table_name = "tbl_project"; //table name
                $this->_primary_key = "project_id";
                $this->save($data, $task_start->project_id);
                // save into tbl_task_timer
                $t_data = array(
                    'project_id' => $task_start->project_id,
                    'user_id' => $this->session->userdata('user_id'),
                    'start_time' => $task_start->start_time,
                    'end_time' => time()
                );

                // insert into tbl_task_timer
                $this->_table_name = "tbl_tasks_timer"; //table name
                $this->_primary_key = "tasks_timer_id";
                $this->save($t_data);
                return true;
            }
        }
    }

    public function loggedin()
    {
        return (bool)$this->session->userdata('loggedin');
    }

    public function hash($string)
    {
        return hash('sha512', $string . config_item('encryption_key'));
    }
}
