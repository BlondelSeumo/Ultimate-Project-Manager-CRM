<?php class MY_Model extends CI_Model
{
    protected $_table_name = '';
    protected $_primary_key = 'id';
    protected $_primary_filter = 'intval';
    protected $_order_by = '';
    public $rules = array();
    protected $_timestamps = FALSE;
    public $column_order; //set column field database for datatable orderable
    public $column_search; //set column field database for datatable searchable just firstname , lastname , address are searchable

    function __construct()
    {
        parent::__construct();
    }

    // CURD FUNCTION

    public function array_from_post($fields)
    {

        $data = array();
        foreach ($fields as $field) {
            $data[$field] = $this->input->post($field, true);
        }
        return $data;
    }

    public function get($id = NULL, $single = FALSE)
    {

        if ($id != NULL) {
            $filter = $this->_primary_filter;
            $id = $filter($id);
            $this->db->where($this->_primary_key, $id);
            $method = 'row';
        } elseif ($single == TRUE) {
            $method = 'row';
        } else {
            $method = 'result';
        }

        if (count($this->db->order_by($this->_order_by)) > 0) {
            $this->db->order_by($this->_order_by);
        }
        return $this->db->get($this->_table_name)->$method();
    }

    public function get_by($where, $single = FALSE)
    {
        $this->db->where($where);
        return $this->get(NULL, $single);
    }

    public function save($data, $id = NULL)
    {
        // Set timestamps
        if ($this->_timestamps == TRUE) {
            $now = date('Y-m-d H:i:s');
            $id || $data['created'] = $now;
            $data['modified'] = $now;
        }

        // Insert
        if ($id === NULL) {
            !isset($data[$this->_primary_key]) || $data[$this->_primary_key] = NULL;
            $this->db->set($data);
            $this->db->insert($this->_table_name);
            $id = $this->db->insert_id();
        } // Update
        else {
            $filter = $this->_primary_filter;
            $id = $filter($id);
            $this->db->set($data);
            $this->db->where($this->_primary_key, $id);
            $this->db->update($this->_table_name);
        }

        return $id;
    }

    public function delete($id)
    {
        $filter = $this->_primary_filter;
        $id = $filter($id);
        if (!$id) {
            return FALSE;
        }
        $this->db->where($this->_primary_key, $id);
        $this->db->limit(1);
        $this->db->delete($this->_table_name);
    }

    /**
     * Delete Multiple rows
     */
    public function delete_multiple($where)
    {
        $this->db->where($where);
        $this->db->delete($this->_table_name);
    }

    function uploadImage($field)
    {

        $config['upload_path'] = 'uploads/';
        $config['allowed_types'] = config_item('allowed_files');
        $config['max_size'] = config_item('max_file_size') * 1024;
        $config['overwrite'] = TRUE;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if (!$this->upload->do_upload($field)) {
            $error = $this->upload->display_errors();
            $type = "error";
            $message = $error;
            set_message($type, $message);
            return FALSE;
            // uploading failed. $error will holds the errors.
        } else {
            $fdata = $this->upload->data();
            $img_data['path'] = $config['upload_path'] . $fdata['file_name'];
            return $img_data;
            // uploading successfull, now do your further actions
        }
    }

    function uploadFile($field)
    {
        $config['upload_path'] = 'uploads/';
        $config['allowed_types'] = config_item('allowed_files');
        $config['max_size'] = config_item('max_file_size') * 1024;
        $config['overwrite'] = TRUE;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if (!$this->upload->do_upload($field)) {
            $error = $this->upload->display_errors();
            $type = "error";
            $message = $error;
            set_message($type, $message);
            return FALSE;
            // uploading failed. $error will holds the errors.
        } else {
            $fdata = $this->upload->data();
            $file_data['fileName'] = $fdata['file_name'];
            $file_data['path'] = $config['upload_path'] . $fdata['file_name'];
            $file_data['fullPath'] = $fdata['full_path'];
            $file_data['ext'] = $fdata['file_ext'];
            $file_data['size'] = $fdata['file_size'];
            $file_data['is_image'] = $fdata['is_image'];
            $file_data['image_width'] = $fdata['image_width'];
            $file_data['image_height'] = $fdata['image_height'];
            return $file_data;
        }
    }

    function uploadAllType($field)
    {
        $config['upload_path'] = 'uploads/';
        $config['allowed_types'] = config_item('allowed_files');
        $config['max_size'] = config_item('max_file_size') * 1024;
        $config['overwrite'] = TRUE;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if (!$this->upload->do_upload($field)) {
            $error = $this->upload->display_errors();
            $type = "error";
            $message = $error;
            set_message($type, $message);
            return FALSE;
            // uploading failed. $error will holds the errors.
        } else {
            $fdata = $this->upload->data();
            $file_data['fileName'] = $fdata['file_name'];
            $file_data['path'] = $config['upload_path'] . $fdata['file_name'];
            $file_data['fullPath'] = $fdata['full_path'];
            $file_data['ext'] = $fdata['file_ext'];
            $file_data['size'] = $fdata['file_size'];
            $file_data['is_image'] = $fdata['is_image'];
            $file_data['image_width'] = $fdata['image_width'];
            $file_data['image_height'] = $fdata['image_height'];
            return $file_data;
            // uploading successfull, now do your further actions
        }
    }

    function multi_uploadAllType($field)
    {
        $config['upload_path'] = 'uploads/';
        $config['allowed_types'] = config_item('allowed_files');
        $config['max_size'] = config_item('max_file_size') * 1024;
        $config['overwrite'] = TRUE;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if (!$this->upload->do_multi_upload($field)) {
            $error = $this->upload->display_errors();
            $type = "error";
            $message = $error;
            set_message($type, $message);
            return FALSE;
            // uploading failed. $error will holds the errors.
        } else {
            $multi_fdata = $this->upload->get_multi_upload_data();
            foreach ($multi_fdata as $fdata) {

                $file_data['fileName'] = $fdata['file_name'];
                $file_data['path'] = $config['upload_path'] . $fdata['file_name'];
                $file_data['fullPath'] = $fdata['full_path'];
                $file_data['ext'] = $fdata['file_ext'];
                $file_data['size'] = $fdata['file_size'];
                $file_data['is_image'] = $fdata['is_image'];
                $file_data['image_width'] = $fdata['image_width'];
                $file_data['image_height'] = $fdata['image_height'];

                $result[] = $file_data;
            }
            return $result;
            // uploading successfull, now do your further actions
        }
    }

    public function check_by($where, $tbl_name)
    {

        $this->db->select('*');
        $this->db->from($tbl_name);
        $this->db->where($where);
        $query_result = $this->db->get();
        $result = $query_result->row();
        return $result;
    }

    public function get_result($where, $tbl_name)
    {

        $this->db->select('*');
        $this->db->from($tbl_name);
        $this->db->where($where);
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    function count_rows($table, $where = null)
    {
        if (!empty($where)) {
            $this->db->where($where);
        }
        $query = $this->db->get($table);
        if ($query->num_rows() > 0) {
            return $query->num_rows();
        } else {
            return 0;
        }
    }

    function get_any_field($table, $where_criteria, $table_field)
    {
        $query = $this->db->select($table_field)->where($where_criteria)->get($table);
        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->$table_field;
        }
    }

    /**
     * @ Upadate row with duplicasi check
     */
    public function check_update($table, $where, $id = Null)
    {
        $this->db->select('*', FALSE);
        $this->db->from($table);
        if ($id != null) {
            $this->db->where($id);
        }
        $this->db->where($where);
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    // set actiion setting

    public function set_action($where, $value, $tbl_name)
    {
        $this->db->set($value);
        $this->db->where($where);
        $this->db->update($tbl_name);
    }

    function get_sum($table, $field, $where)
    {

        $this->db->where($where);
        $this->db->select_sum($field);
        $query = $this->db->get($table);
        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->$field;
        } else {
            return 0;
        }
    }

    public function get_limit($where, $tbl_name, $limit)
    {

        $this->db->select('*');
        $this->db->from($tbl_name);
        $this->db->where($where);
        $this->db->limit($limit);
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    function short_description($string = FALSE, $from_start = 30, $from_end = 10, $limit = FALSE)
    {
        if (!$string) {
            return FALSE;
        }
        if ($limit) {
            if (mb_strlen($string) < $limit) {
                return $string;
            }
        }
        return mb_substr($string, 0, $from_start - 1) . "..." . ($from_end > 0 ? mb_substr($string, -$from_end) : '');
    }

    function get_table_field($tableName, $where = array(), $field)
    {

        return $this->db->select($field)->where($where)->get($tableName)->row()->$field;
    }

    function get_time_different($from = null, $to)
    {
        if (empty($from)) {
            $from = time();
        }
        $time_elapsed = $from - $to;
        $seconds = $time_elapsed;
        $minutes = round($time_elapsed / 60);
        $hours = round($time_elapsed / 3600);
        $days = round($time_elapsed / 86400);
        $weeks = round($time_elapsed / 604800);
        $months = round($time_elapsed / 2600640);
        $years = round($time_elapsed / 31207680);

        // Seconds
        if ($seconds <= 60) {
            return lang('time_ago_just_now');
        } //Minutes
        elseif ($minutes <= 60) {
            if ($minutes == 1) {
                return lang('time_ago_minute');
            } else {
                return lang('time_ago_minutes', $minutes);
            }
        } //Hours
        elseif ($hours <= 24) {
            if ($hours == 1) {
                return lang('time_ago_hour');
            } else {
                return lang('time_ago_hours', $hours);
            }
        } //Days
        elseif ($days <= 7) {
            if ($days == 1) {
                return lang('time_ago_yesterday');
            } else {
                return lang('time_ago_days', $days);
            }
        } //Weeks
        elseif ($weeks <= 4.3) {
            if ($weeks == 1) {
                return lang('time_ago_week');
            } else {
                return lang('time_ago_weeks', $weeks);
            }
        } //Months
        elseif ($months <= 12) {
            if ($months == 1) {
                return lang('time_ago_month');
            } else {
                return lang('time_ago_months', $months);
            }
        } //Years
        else {
            if ($years == 1) {
                return lang('time_ago_year');
            } else {
                return lang('time_ago_years', $years);
            }
        }
    }


    public function client_currency_symbol($client_id)
    {
        $this->db->select('tbl_client.currency', FALSE);
        $this->db->select('tbl_currencies.*', FALSE);
        $this->db->from('tbl_client');
        $this->db->join('tbl_currencies', 'tbl_currencies.code = tbl_client.currency', 'left');
        $this->db->where('tbl_client.client_id', $client_id);
        $query_result = $this->db->get();
        $result = $query_result->row();
        if (empty($result)) {
            $result = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
        }
        return $result;
    }

    public function allowed_user_id($menu_id)
    {
        $permission_user = $this->all_permission_user($menu_id);
        // if not exist data show empty array.
        $user_id = array();
        // get all admin user
        $admin_user = $this->db->where('role_id', 1)->get('tbl_users')->result();
        if (!empty($admin_user)) {
            foreach ($admin_user as $v_user) {
                array_push($user_id, $v_user->user_id);
            }
        }
        if (!empty($permission_user)) {
            foreach ($permission_user as $p_user) {
                array_push($user_id, $p_user->user_id);
            }
        }
        return array_unique($user_id);
    }

    public function allowed_user($menu_id)
    {
        $permission_user = $this->all_permission_user($menu_id);
        // get all admin user
        $admin_user = $this->db->where('role_id', 1)->get('tbl_users')->result();
        // if not exist data show empty array.
        if (!empty($permission_user)) {
            $permission_user = $permission_user;
        } else {
            $permission_user = array();
        }
        if (!empty($admin_user)) {
            $admin_user = $admin_user;
        } else {
            $admin_user = array();
        }
        $result = array_merge($admin_user, $permission_user);
        $r_result = array();
        foreach ($result as $v_result) {
            array_push($r_result, $v_result->user_id);
        }
        $r_result = array_unique($r_result);
        $users = array();
        if (!empty($r_result)) {
            foreach ($r_result as $v_user) {
                array_push($users, $this->db->where('user_id', $v_user)->get('tbl_users')->row());
            }
        }
        return $users;
    }

    public function permitted_allowed_user($permission)
    {
        $users = array();
        $get_permission = json_decode($permission);
        foreach ($get_permission as $user_id => $v_permission) {
            array_push($users, $this->db->where('user_id', $user_id)->get('tbl_users')->row());
        }
        return $users;
    }

    public function all_permission_user($menu_id)
    {
        $this->db->select('tbl_user_role.designations_id', FALSE);
        $this->db->select('tbl_account_details.designations_id,tbl_account_details.fullname', FALSE);
        $this->db->select('tbl_users.*', FALSE);
        $this->db->from('tbl_user_role');
        $this->db->join('tbl_account_details', 'tbl_account_details.designations_id = tbl_user_role.designations_id', 'left');
        $this->db->join('tbl_users', 'tbl_users.user_id = tbl_account_details.user_id', 'left');
        $this->db->where('tbl_user_role.menu_id', $menu_id);
        $this->db->where('tbl_users.activated', 1);
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    public function get_permission($table, $where = null)
    {
        $role = $this->session->userdata('user_type');
        $user_id = $this->session->userdata('user_id');
        $this->db->from($table);
        if (!empty($where)) {
            $this->db->where($where);
        }
        if (!empty($_POST["length"]) && $_POST["length"] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $permission = $query->result();

        $return_result = array();
        if ($role != 1) {
            if (!empty($permission)) {
                foreach ($permission as $result) {
                    if ($result->permission == 'all') {
                        array_push($return_result, $result);
                    } else {
                        $get_permission = json_decode($result->permission);
                        if (is_object($get_permission)) {
                            foreach ($get_permission as $id => $v_permission) {
                                if ($user_id == $id) {
                                    array_push($return_result, $result);
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $return_result = $permission;
        }
        return $return_result;
    }

    public function my_permission($table, $user_id)
    {
        $this->db->from($table);
        if (!empty($_POST["length"]) && $_POST["length"] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $result_info = $query->result();
        if (!empty($result_info)) {
            foreach ($result_info as $result) {
                if ($result->permission == 'all') {
                    $permission[] = $result;
                } else {
                    $get_permission = json_decode($result->permission);
                    if (is_object($get_permission)) {
                        foreach ($get_permission as $id => $v_permission) {
                            if ($user_id == $id) {
                                $permission[] = $result;
                            }
                        }
                    }
                }
            }
        }
        if (empty($permission)) {
            $permission = array();
        }
        return $permission;
    }

    public function can_action($table, $action, $id, $permission = null)
    {
        $role = $this->session->userdata('user_type');
        $user_id = $this->session->userdata('user_id');
        $result_info = $this->db->where($id)->get($table)->row();
        if (!empty($permission) || $role != 1) {
            if (!empty($result_info)) {
                if ($result_info->permission != 'all') {
                    $get_permission = json_decode($result_info->permission);
                } else {
                    return true;
                }
                if (is_object($get_permission)) {
                    foreach ($get_permission as $user => $v_permission) {
                        if (!empty($v_permission)) {
                            foreach ($v_permission as $v_action) {
                                if ($user == $user_id) {
                                    if ($v_action == $action) {
                                        return true;
                                    }
                                }

                            }
                        }
                    }
                }
            } else {
                return false;
            }
        } else {
            return true;
        }
    }


    public function hash($string)
    {
        return hash('sha512', $string . config_item('encryption_key'));
    }

    public function generate_invoice_number()
    {
        $query = $this->db->query('SELECT reference_no, invoices_id FROM tbl_invoices WHERE invoices_id = (SELECT MAX(invoices_id) FROM tbl_invoices)');
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $ref_number = intval(substr($row->reference_no, -4));
            $next_number = ++$row->invoices_id;
            if ($next_number < $ref_number) {
                $next_number = $ref_number + 1;
            }
            if ($next_number < config_item('invoice_start_no')) {
                $next_number = config_item('invoice_start_no');
            }
            $next_number = $this->reference_no_exists($next_number);
            $next_number = sprintf('%04d', $next_number);
        } else {
            $next_number = sprintf('%04d', config_item('invoice_start_no'));
        }
        if (!empty(config_item('invoice_number_format'))) {
            $invoice_format = config_item('invoice_number_format');
            $invoice_prefix = str_replace("[" . config_item('invoice_prefix') . "]", config_item('invoice_prefix'), $invoice_format);
            $yyyy = str_replace("[yyyy]", date('Y'), $invoice_prefix);
            $yy = str_replace("[yy]", date('y'), $yyyy);
            $mm = str_replace("[mm]", date('M'), $yy);
            $m = str_replace("[m]", date('m'), $mm);
            $dd = str_replace("[dd]", date('d'), $m);
            $next_number = str_replace("[number]", $next_number, $dd);
        }
        return $next_number;
    }

    public function reference_no_exists($next_number)
    {
        $next_number = sprintf('%04d', $next_number);

        $records = $this->db->where('reference_no', config_item('invoice_prefix') . $next_number)->get('tbl_invoices')->num_rows();
        if ($records > 0) {
            return $this->reference_no_exists($next_number + 1);
        } else {
            return $next_number;
        }
    }

    public function generate_estimate_number()
    {
        $query = $this->db->query('SELECT reference_no, estimates_id FROM tbl_estimates WHERE estimates_id = (SELECT MAX(estimates_id) FROM tbl_estimates)');
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $ref_number = intval(substr($row->reference_no, -4));
            $next_number = ++$row->estimates_id;
            if ($next_number < $ref_number) {
                $next_number = $ref_number + 1;
            }
            if ($next_number < config_item('estimate_start_no')) {
                $next_number = config_item('estimate_start_no');
            }
            $next_number = $this->estimate_reference_no_exists($next_number);
            $next_number = sprintf('%04d', $next_number);
        } else {
            $next_number = sprintf('%04d', config_item('estimate_start_no'));
        }
        if (!empty(config_item('estimate_number_format'))) {
            $invoice_format = config_item('estimate_number_format');
            $invoice_prefix = str_replace("[" . config_item('estimate_prefix') . "]", config_item('estimate_prefix'), $invoice_format);
            $yyyy = str_replace("[yyyy]", date('Y'), $invoice_prefix);
            $yy = str_replace("[yy]", date('y'), $yyyy);
            $mm = str_replace("[mm]", date('M'), $yy);
            $m = str_replace("[m]", date('m'), $mm);
            $dd = str_replace("[dd]", date('d'), $m);
            $next_number = str_replace("[number]", $next_number, $dd);
        }
        return $next_number;
    }

    public function estimate_reference_no_exists($next_number)
    {
        $next_number = sprintf('%04d', $next_number);

        $records = $this->db->where('reference_no', config_item('estimate_prefix') . $next_number)->get('tbl_estimates')->num_rows();
        if ($records > 0) {
            return $this->reference_no_exists($next_number + 1);
        } else {
            return $next_number;
        }
    }

    public function generate_credit_note_number()
    {
        $query = $this->db->query('SELECT reference_no, credit_note_id FROM tbl_credit_note WHERE credit_note_id = (SELECT MAX(credit_note_id) FROM tbl_credit_note)');
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $ref_number = intval(substr($row->reference_no, -4));
            $next_number = ++$row->credit_note_id;
            if ($next_number < $ref_number) {
                $next_number = $ref_number + 1;
            }
            if ($next_number < config_item('credit_note_start_no')) {
                $next_number = config_item('credit_note_start_no');
            }
            $next_number = $this->credit_note_reference_no_exists($next_number);
            $next_number = sprintf('%04d', $next_number);
        } else {
            $next_number = sprintf('%04d', config_item('credit_note_start_no'));
        }
        if (!empty(config_item('credit_note_number_format'))) {
            $invoice_format = config_item('credit_note_number_format');
            $invoice_prefix = str_replace("[" . config_item('credit_note_prefix') . "]", config_item('credit_note_prefix'), $invoice_format);
            $yyyy = str_replace("[yyyy]", date('Y'), $invoice_prefix);
            $yy = str_replace("[yy]", date('y'), $yyyy);
            $mm = str_replace("[mm]", date('M'), $yy);
            $m = str_replace("[m]", date('m'), $mm);
            $dd = str_replace("[dd]", date('d'), $m);
            $next_number = str_replace("[number]", $next_number, $dd);
        }
        return $next_number;
    }

    public function credit_note_reference_no_exists($next_number)
    {
        $next_number = sprintf('%04d', $next_number);

        $records = $this->db->where('reference_no', config_item('credit_note_prefix') . $next_number)->get('tbl_credit_note')->num_rows();
        if ($records > 0) {
            return $this->reference_no_exists($next_number + 1);
        } else {
            return $next_number;
        }
    }

    public function generate_proposal_number()
    {
        $query = $this->db->query('SELECT reference_no, proposals_id FROM tbl_proposals WHERE proposals_id = (SELECT MAX(proposals_id) FROM tbl_proposals)');
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $next_number = ++$row->proposals_id;
            $next_number = $this->proposal_reference_no_exists($next_number);
            $next_number = sprintf('%04d', $next_number);
        } else {
            $next_number = sprintf('%04d', config_item('proposal_start_no'));
        }
        if (!empty(config_item('proposal_number_format'))) {
            $invoice_format = config_item('proposal_number_format');
            $invoice_prefix = str_replace("[" . config_item('proposal_prefix') . "]", config_item('proposal_prefix'), $invoice_format);
            $yyyy = str_replace("[yyyy]", date('Y'), $invoice_prefix);
            $yy = str_replace("[yy]", date('y'), $yyyy);
            $mm = str_replace("[mm]", date('M'), $yy);
            $m = str_replace("[m]", date('m'), $mm);
            $dd = str_replace("[dd]", date('d'), $m);
            $next_number = str_replace("[number]", $next_number, $dd);
        }
        return $next_number;
    }

    public function proposal_reference_no_exists($next_number)
    {
        $next_number = sprintf('%04d', $next_number);

        $records = $this->db->where('reference_no', config_item('proposal_prefix') . $next_number)->get('tbl_proposals')->num_rows();
        if ($records > 0) {
            return $this->proposal_reference_no_exists($next_number + 1);
        } else {
            return $next_number;
        }
    }

    public function generate_purchase_number()
    {
        $total_invoice = $this->count_rows('tbl_purchases');
        if ($total_invoice > 0) {
//            $ref_number = intval(substr($row->reference_no, -4));
            $next_number = ++$total_invoice;
//            if ($next_number < $ref_number) {
//                $next_number = $ref_number + 1;
//            }
            if ($next_number < config_item('purchase_start_no')) {
                $next_number = config_item('purchase_start_no');
            }
            $next_number = $this->purchase_reference_no_exists($next_number);
            $next_number = sprintf('%04d', $next_number);
        } else {
            $next_number = sprintf('%04d', config_item('purchase_start_no'));
        }
        if (!empty(config_item('purchase_number_format'))) {
            $invoice_format = config_item('purchase_number_format');
            $invoice_prefix = str_replace("[" . config_item('purchase_prefix') . "]", config_item('purchase_prefix'), $invoice_format);
            $yyyy = str_replace("[yyyy]", date('Y'), $invoice_prefix);
            $yy = str_replace("[yy]", date('y'), $yyyy);
            $mm = str_replace("[mm]", date('M'), $yy);
            $m = str_replace("[m]", date('m'), $mm);
            $dd = str_replace("[dd]", date('d'), $m);
            $next_number = str_replace("[number]", $next_number, $dd);
        }
        return $next_number;
    }

    public function purchase_reference_no_exists($next_number)
    {
        $next_number = sprintf('%04d', $next_number);
        $records = $this->db->where('reference_no', config_item('purchase_prefix') . $next_number)->get('tbl_purchases')->num_rows();
        if ($records > 0) {
            return $this->purchase_reference_no_exists($next_number + 1);
        } else {
            return $next_number;
        }
    }

    public function generate_return_stock_number()
    {
        $total_invoice = $this->count_rows('tbl_return_stock');
        if ($total_invoice > 0) {
//            $ref_number = intval(substr($row->reference_no, -4));
            $next_number = ++$total_invoice;
//            if ($next_number < $ref_number) {
//                $next_number = $ref_number + 1;
//            }
            if ($next_number < config_item('return_stock_start_no')) {
                $next_number = config_item('return_stock_start_no');
            }
            $next_number = $this->return_stock_reference_no_exists($next_number);
            $next_number = sprintf('%04d', $next_number);
        } else {
            $next_number = sprintf('%04d', config_item('return_stock_start_no'));
        }
        if (!empty(config_item('return_stock_number_format'))) {
            $invoice_format = config_item('return_stock_number_format');
            $invoice_prefix = str_replace("[" . config_item('return_stock_prefix') . "]", config_item('return_stock_prefix'), $invoice_format);
            $yyyy = str_replace("[yyyy]", date('Y'), $invoice_prefix);
            $yy = str_replace("[yy]", date('y'), $yyyy);
            $mm = str_replace("[mm]", date('M'), $yy);
            $m = str_replace("[m]", date('m'), $mm);
            $dd = str_replace("[dd]", date('d'), $m);
            $next_number = str_replace("[number]", $next_number, $dd);
        }
        return $next_number;
    }

    public function return_stock_reference_no_exists($next_number)
    {
        $next_number = sprintf('%04d', $next_number);
        $records = $this->db->where('reference_no', config_item('return_stock_prefix') . $next_number)->get('tbl_return_stock')->num_rows();
        if ($records > 0) {
            return $this->return_stock_reference_no_exists($next_number + 1);
        } else {
            return $next_number;
        }
    }

    function send_email($params, $test = null)
    {

        $config = array();
        // If postmark API is being used
        if (config_item('use_postmark') == 'TRUE') {
            $config = array(
                'api_key' => config_item('postmark_api_key')
            );
            $this->load->library('postmark', $config);
            $this->postmark->from(config_item('postmark_from_address'), config_item('company_name'));
            $this->postmark->to($params['recipient']);
            $this->postmark->subject($params['subject']);
            $this->postmark->message_plain($params['message']);
            $this->postmark->message_html($params['message']);
            // Check resourceed file
            if (isset($params['resourcement_url'])) {
                $this->postmark->resource($params['resourcement_url']);
            }
            $this->postmark->send();
        } else {
            // If using SMTP
//            if (config_item('protocol') == 'smtp') {
//                $this->load->library('encrypt');
//                $config = array(
//                    'protocol' => config_item('protocol'),
//                    'smtp_host' => config_item('smtp_host'),
//                    'smtp_port' => config_item('smtp_port'),
//                    'smtp_user' => config_item('smtp_user'),
//                    'smtp_pass' => config_item('smtp_pass'),
//                    'smtp_crypto' => config_item('email_encryption'),
//                    'crlf' => "\r\n"
//                );
//            }

            // Send email
            $config['useragent'] = 'UniqueCoder LTD';
            $config['mailpath'] = "/usr/bin/sendmail"; // or "/usr/sbin/sendmail"
            $config['wordwrap'] = TRUE;
            $config['mailtype'] = "html";
            $config['charset'] = 'utf-8';
            $config['newline'] = "\r\n";
            $config['crlf'] = "\r\n";
            $config['smtp_timeout'] = '30';
            $config['protocol'] = config_item('protocol');
            $config['smtp_host'] = config_item('smtp_host');
            $config['smtp_port'] = config_item('smtp_port');
            $config['smtp_user'] = trim(config_item('smtp_user'));
            $config['smtp_pass'] = decrypt(config_item('smtp_pass'));
            $config['smtp_crypto'] = config_item('smtp_encryption');

            $this->load->library('email', $config);
            $this->email->clear();
            $this->email->from(config_item('company_email'), config_item('company_name'));
            $this->email->to($params['recipient']);

            $this->email->subject($params['subject']);
            $this->email->message($params['message']);
            if ($params['resourceed_file'] != '') {
                $this->email->attach($params['resourceed_file']);
            }
            $send = $this->email->send();
            if (!empty($test)) {
                if ($send) {
                    return $send;
                } else {
                    $error = show_error($this->email->print_debugger());
                    return $error;
                }
            } else {
                if ($send) {
                    return $send;
                } else {
                    send_later($params);
                }

            }
            return true;
        }
    }

    public function all_files()
    {
        $language = array(
            "bugs_lang.php" => "./application/",
            "calendar_lang.php" => "./application/",
            "client_lang.php" => "./application/",
            "date_lang.php" => "./application/",
            "db_lang.php" => "./application/",
            "departments_lang.php" => "./application/",
            "email_lang.php" => "./application/",
            "form_validation_lang.php" => "./application/",
            "ftp_lang.php" => "./application/",
            "imglib_lang.php" => "./application/",
            "leads_lang.php" => "./application/",
            "leave_management_lang.php" => "./application/",
            "main_lang.php" => "./application/",
            "migration_lang.php" => "./application/",
            "number_lang.php" => "./application/",
            "opportunities_lang.php" => "./application/",
            "pagination_lang.php" => "./application/",
            "payroll_lang.php" => "./application/",
            "profiler_lang.php" => "./application/",
            "performance_lang.php" => "./application/",
            "projects_lang.php" => "./application/",
            "sales_lang.php" => "./application/",
            "settings_lang.php" => "./application/",
            "stock_lang.php" => "./application/",
            "tasks_lang.php" => "./application/",
            "tickets_lang.php" => "./application/",
            "transactions_lang.php" => "./application/",
            "unit_test_lang.php" => "./application/",
            "upload_lang.php" => "./application/",
            "utilities_lang.php" => "./application/",
        );
        return $language;
    }

    function task_spent_time_by_id($id)
    {
        $where = 'task_id = ' . $id;

        $total_time = "SELECT start_time,end_time,end_time - start_time time_spent
						FROM tbl_tasks_timer WHERE $where";
        $result = $this->db->query($total_time)->result();
        $time_spent = array();
        foreach ($result as $time) {
            if ($time->start_time != 0 && $time->end_time != 0) {
                $time_spent[] = $time->time_spent;
            }
        }
        if (is_array($time_spent)) {
            return array_sum($time_spent);
        } else {
            return 0;
        }
    }

    function my_spent_time($user_id)
    {
        $where = 'project_id IS NULL';

        $total_time = "SELECT start_time,end_time,end_time - start_time time_spent
						FROM tbl_tasks_timer WHERE user_id = $user_id AND $where";

        $result = $this->db->query($total_time)->result();
        $time_spent = array();
        foreach ($result as $time) {
            if ($time->start_time != 0 && $time->end_time != 0) {
                $time_spent[] = $time->time_spent;
            }
        }
        if (is_array($time_spent)) {
            return array_sum($time_spent);
        } else {
            return 0;
        }
    }

    function get_estime_time($hour)
    {
        if (!empty($hour)) {
            $total = explode(':', $hour);
            if (!empty($total[0])) {
                $hours = $total[0] * 3600;
                if (!empty($total[1])) {
                    $minute = ($total[1] * 60);
                } else {
                    $minute = 0;
                }
                return $hours + $minute;
            }
        }
    }

    function get_time_spent_result($seconds)
    {
        $init = $seconds;
        $hours = floor($init / 3600);
        $minutes = floor(($init / 60) % 60);
        $seconds = $init % 60;
        return "<ul class='timer'><li>" . $hours . "<span>" . lang('hours') . "</span></li>" . "<li class='dots'>" . ":</li><li>" . $minutes . "<span>" . lang('minutes') . "</span></li>" . "<li class='dots'>" . ":</li><li>" . $seconds . "<span>" . lang('seconds') . "</span></li></ul>";
    }

    function get_time_spent_pain_result($seconds)
    {
        $init = $seconds;
        $hours = floor($init / 3600);
        $minutes = floor(($init / 60) % 60);
        $seconds = $init % 60;
        return "$hours:$minutes:$seconds";
    }

    function get_spent_time($seconds, $result = null)
    {
        $init = $seconds;
        $hours = floor($init / 3600);
        $minutes = floor(($init / 60) % 60);
        $seconds = $init % 60;
        if (!empty($result)) {
            return $hours . " : " . $minutes . " : " . $seconds;
        } else {
            return $hours . " <strong> " . lang('hours') . " </strong>" . " : " . $minutes . " <strong> " . lang('minutes') . "</strong>" . " : " . $seconds . "<strong> " . lang('seconds') . "</strong>";
        }


    }

    public function get_progress($goal_info, $currency = null)
    {

        $goal_type_info = $this->db->where('goal_type_id', $goal_info->goal_type_id)->get('tbl_goal_type')->row();

        $start_date = $goal_info->start_date;
        $end_date = $goal_info->end_date;
        $achievement = round($goal_info->achievement);
        if ($goal_type_info->tbl_name == 'tbl_transactions') {
            if ($goal_type_info->type_name == 'achive_total_income_by_bank' || $goal_type_info->type_name == 'achive_total_expense_by_bank') {
                if ($goal_info->account_id != '0') {
                    $where = array(
                        'account_id' => $goal_info->account_id,
                        'date >=' => $start_date,
                        'date <=' => $end_date,
                        'type' => $goal_type_info->query
                    );
                } else {
                    $where = array(
                        'date >=' => $start_date,
                        'date <=' => $end_date,
                        'type' => $goal_type_info->query
                    );
                }
            } else {

                $where = array(
                    'date >=' => $start_date,
                    'date <=' => $end_date,
                    'type' => $goal_type_info->query
                );
            }
            $curency = $this->check_by(array(
                'code' => config_item('default_currency')
            ), 'tbl_currencies');
            $transactions_result = $this->db->select_sum('amount')->where($where)->get($goal_type_info->tbl_name)->row()->amount;
            $tr_amount = round($transactions_result);
            if ($achievement <= $tr_amount) {
                $result['progress'] = 100;
            } else {
                $progress = ($tr_amount / $achievement) * 100;
                $result['progress'] = round($progress);

            }
            if (!empty($currency)) {
                $result['achievement'] = $tr_amount;
            } else {
                $result['achievement'] = display_money($tr_amount, $curency->symbol);
            }
        }
        if ($goal_type_info->tbl_name == 'tbl_invoices' || $goal_type_info->tbl_name == 'tbl_estimates') {
            $where = array(
                'date_saved >=' => $start_date . " 00:00:00",
                'date_saved <=' => $end_date . " 23:59:59"
            );
            $invoice_result = $this->db->where($where)->get($goal_type_info->tbl_name)->result();
            if (count($invoice_result) > 0) {
                $invoice_result = count($invoice_result);
            } else {
                $invoice_result = 0;
            }
            if ($achievement <= $invoice_result) {
                $result['progress'] = 100;
            } else {
                $progress = ($invoice_result / $achievement) * 100;
                $result['progress'] = round($progress);
            }
            $result['achievement'] = $invoice_result;
        }
        if ($goal_type_info->tbl_name == 'tbl_task') {
            $where = array(
                'task_created_date >=' => $start_date . " 00:00:00",
                'task_created_date <=' => $end_date . " 23:59:59",
                'task_status' => 'completed'
            );
            $task_result = $this->db->where($where)->get($goal_type_info->tbl_name)->result();
            if (count($task_result) > 0) {
                $task_result = count($task_result);
            } else {
                $task_result = 0;
            }

            if ($achievement <= $task_result) {
                $result['progress'] = 100;
            } else {
                $progress = ($task_result / $achievement) * 100;
                $result['progress'] = round($progress);
            }
            $result['achievement'] = $task_result;
        }
        if ($goal_type_info->tbl_name == 'tbl_client') {
            if ($goal_type_info->type_name = 'convert_leads_to_client') {
                $where = array(
                    'date_added >=' => $start_date . " 00:00:00",
                    'date_added <=' => $end_date . " 23:59:59",
                    'leads_id !=' => '0'
                );
            } else {
                $where = array(
                    'date_added >=' => $start_date . " 00:00:00",
                    'date_added <=' => $end_date . " 23:59:59",
                    'leads_id' => '0'
                );
            }
            $client_result = $this->db->where($where)->get($goal_type_info->tbl_name)->result();
            if (count($client_result) > 0) {
                $client_result = count($client_result);
            } else {
                $client_result = 0;
            }
            if ($achievement <= $client_result) {
                $result['progress'] = 100;
            } else {
                $progress = ($client_result / $achievement) * 100;
                $result['progress'] = round($progress);
            }
            $result['achievement'] = $client_result;
        }
        if ($goal_type_info->tbl_name == 'tbl_payments') {
            $where = array(
                'payment_date >=' => $start_date,
                'payment_date <=' => $end_date
            );

            $payments_result = $this->db->select('currency')->select_sum('amount')->where($where)->get($goal_type_info->tbl_name)->row();

            if ($achievement <= $payments_result->amount) {
                $result['progress'] = 100;
            } else {
                $progress = ($payments_result->amount / $achievement) * 100;
                $result['progress'] = round($progress);
            }
            if (!empty($currency)) {
                $result['achievement'] = $payments_result->amount;
            } else {
                $result['achievement'] = display_money($payments_result->amount, $payments_result->currency);
            }
        }
        if (!empty($result)) {
            return $result;
        } else {
            $result['progress'] = 0;
            $result['achievement'] = 0;
            return $result;
        }
    }

    public function send_goal_mail($type, $goal_info)
    {
        $email_template = email_templates(array('email_group' => $type));


        $goal_type_info = $this->db->where('goal_type_id', $goal_info->goal_type_id)->get('tbl_goal_type')->row();
        $progress = $this->get_progress($goal_info);

        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $Type = str_replace("{Goal_Type}", lang($goal_type_info->type_name), $message);
        $achievement = str_replace("{achievement}", $goal_info->achievement, $Type);
        $total_achievement = str_replace("{total_achievement}", $progress['achievement'], $achievement);
        $start_date = str_replace("{start_date}", $goal_info->start_date, $total_achievement);
        $message = str_replace("{End_date}", $goal_info->end_date, $start_date);
        $data['message'] = $message;

        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';

        if (!empty($goal_info->permission) && $goal_info->permission != 'all') {
            $user = json_decode($goal_info->permission);
            foreach ($user as $key => $v_user) {
                $allowed_user[] = $key;
            }
        } else {
            $allowed_user = $this->allowed_user_id('69');
        }

        if (!empty($allowed_user)) {
            foreach ($allowed_user as $v_user) {
                if (!empty($v_user)) {
                    $login_info = $this->check_by(array(
                        'user_id' => $v_user
                    ), 'tbl_users');
                    $params['recipient'] = $login_info->email;
                    $this->send_email($params);

                    if ($v_user != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $v_user,
                            'icon' => 'shield',
                            'description' => 'not_' . $type,
                            'link' => 'admin/goal_tracking/goal_details/' . $goal_info->goal_tracking_id,
                            'value' => $goal_info->subject
                        ));
                    }
                }
            }
        }

        $udate['email_send'] = 'yes';
        $this->_table_name = "tbl_goal_tracking"; //table name
        $this->_primary_key = "goal_tracking_id";
        $this->save($udate, $goal_info->goal_tracking_id);

        return true;
    }

    function GetDays($start_date, $end_date, $step = '+1 day', $output_format = 'Y-m-d')
    {
        $dates = array();
        $current = strtotime($start_date);
        if (!empty($end_date) && $start_date != $end_date) {
            $end_date = strtotime($end_date);
            while ($current < $end_date) {
                $dates[] = date($output_format, $current);
                $current = strtotime($step, $current);
            }
        } else {
            array_push($dates, $start_date);
        }
        return $dates;
    }

    public function all_designation()
    {
        $all_department = $this->db->get('tbl_departments')->result();
        if (!empty($all_department)) {
            foreach ($all_department as $v_department) {
                $designation[$v_department->deptname] = $this->db->where('departments_id', $v_department->departments_id)->get('tbl_designations')->result();
            }
            return $designation;
        }
    }

    public function get_all_employee()
    {
        $all_department = $this->db->get('tbl_departments')->result();
        if (!empty($all_department)) {
            foreach ($all_department as $v_department) {
                $designation[$v_department->deptname] = $this->all_employee($v_department->departments_id);
            }
            return $designation;
        }
    }

    function all_employee($department_id)
    {
        $this->db->select('tbl_account_details.*', FALSE);
        $this->db->select('tbl_designations.*', FALSE);
        $this->db->select('tbl_departments.*', FALSE);
        $this->db->from('tbl_account_details');
        $this->db->join('tbl_designations', 'tbl_account_details.designations_id = tbl_designations.designations_id', 'left');
        $this->db->join('tbl_departments', 'tbl_departments.departments_id = tbl_designations.departments_id', 'left');
        $this->db->where('tbl_departments.departments_id', $department_id);
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    public function get_all_items()
    {
        $all_items = $this->db->get('tbl_saved_items')->result();
        if (!empty($all_items)) {
            foreach ($all_items as $v_items) {
                $saved_items[$v_items->customer_group_id][] = $v_items;
            }
        }
        if (!empty($saved_items)) {
            return $saved_items;
        } else {
            return array();
        }
    }

    public function get_invoice_item_taxes($items_id, $type = null)
    {
        if ($type == 'estimate') {
            $item_info = $this->db->where('estimate_items_id', $items_id)->get('tbl_estimate_items')->row();
        } else if ($type == 'proposal') {
            $item_info = $this->db->where('proposals_items_id', $items_id)->get('tbl_proposals_items')->row();
        } else if ($type == 'purchase') {
            $item_info = $this->db->where('items_id', $items_id)->get('tbl_purchase_items')->row();
        } else if ($type == 'return_stock') {
            $item_info = $this->db->where('items_id', $items_id)->get('tbl_return_stock_items')->row();
        } else if ($type == 'credit_note') {
            $item_info = $this->db->where('credit_note_items_id', $items_id)->get('tbl_credit_note_items')->row();
        } else {
            $item_info = $this->db->where('items_id', $items_id)->get('tbl_items')->row();
        }
        if (!empty($item_info)) {
            return json_decode($item_info->item_tax_name);
        } else {
            return false;
        }


    }

    public function reduce_items($id, $qty)
    {
        $this->db->set('quantity', 'quantity -' . $qty, FALSE);
        $this->db->where('saved_items_id', $id);
        $this->db->update('tbl_saved_items');
    }

    public function return_items($id, $qty)
    {
        $this->db->set('quantity', 'quantity +' . $qty, FALSE);
        $this->db->where('saved_items_id', $id);
        $this->db->update('tbl_saved_items');
    }

    function get_online_users()
    {
        $profile = profile();
        $online['online_time'] = time();
        update('tbl_users', array('user_id' => $profile->user_id), $online);

        if ($profile->role_id == 2 || $profile->role_id == 3) {
            $where = array('role_id !=' => '2', 'activated' => '1');
        } else {
            $where = array('activated' => '1');
        }
        $users = get_result('tbl_users', $where);
        $result = array();
        $now = time() - 60 * 10;
        foreach ($users as $v_user) {
            if ($v_user->user_id != $this->session->userdata('user_id')) {
                $time = $v_user->online_time;
                if ($time > $now) {
                    $result['online'][] = $v_user;
                } else {
                    $result['offline'][] = $v_user;
                }
            }
        }
        return $result;
    }

}
