<?php

/**
 * Description of bugs
 *
 * @author Nayeem
 */
class Bugs extends Client_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('bugs_model');
        $this->load->helper('ckeditor');
        $this->data['ckeditor'] = array(
            'id' => 'ck_editor',
            'path' => 'asset/js/ckeditor',
            'config' => array(
                'toolbar' => "Full",
                'width' => "90%",
                'height' => "200px"
            )
        );
    }

    public function index($id = NULL, $opt_id = NULL)
    {

        $data['title'] = lang('all_bugs');
        $data['breadcrumbs'] = lang('bugs');
        $data['page'] = lang('bugs');
        $data['all_bugs_info'] = $this->db->where('reporter', $this->session->userdata('user_id'))->get('tbl_bug')->result();

        if ($id) { // retrive data from db by id
            $data['active'] = 2;
            //get all bug information
            $data['bug_info'] = $this->db->where('bug_id', $id)->get('tbl_bug')->row();
        } else {
            $data['active'] = 1;
        }

        $data['editor'] = $this->data;
        $data['subview'] = $this->load->view('client/bugs/bugs', $data, TRUE);
        $this->load->view('client/_layout_main', $data);
    }

    public function bugsList($type = null)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_bug';
            $this->datatables->column_order = array('issue_no', 'bug_title', 'priority', 'bug_status');
            $this->datatables->column_search = array('issue_no', 'bug_title', 'priority', 'bug_status');
            $this->datatables->order = array('bug_id' => 'desc');
            if (!empty($type)) {
                $where = array('reporter' => $this->session->userdata('user_id'), 'bug_status' => $type);
            } else {
                $where = array('reporter' => $this->session->userdata('user_id'));
            }
            $fetch_data = array_reverse(make_datatables($where));

            $data = array();

            foreach ($fetch_data as $key => $v_bugs) {
                $action = null;

                $sub_array = array();
                $issue_no = null;
                $issue_no .= '<a class="text-info" href="' . base_url() . 'client/bugs/view_bug_details/' . $v_bugs->bug_id . '">' . $v_bugs->issue_no . '</a>';
                $sub_array[] = $issue_no;

                $name = null;
                $name .= '<a class="text-info" href="' . base_url() . 'client/bugs/view_bug_details/' . $v_bugs->bug_id . '">' . $v_bugs->bug_title . '</a>';
                $sub_array[] = $name;

                if ($v_bugs->bug_status == 'unconfirmed') {
                    $label = 'warning';
                } elseif ($v_bugs->bug_status == 'confirmed') {
                    $label = 'info';
                } elseif ($v_bugs->bug_status == 'in_progress') {
                    $label = 'primary';
                } else {
                    $label = 'success';
                }
                $sub_array[] = '<span class="label label-' . $label . '">' . lang($v_bugs->bug_status) . '</span>';

                if ($v_bugs->priority == 'High') {
                    $plabel = 'danger';
                } elseif ($v_bugs->priority == 'Medium') {
                    $plabel = 'info';
                } else {
                    $plabel = 'primary';
                }
                $sub_array[] = '<span class="label label-' . $plabel . '">' . ucfirst($v_bugs->priority) . '</span>';

                $custom_form_table = custom_form_table(6, $v_bugs->bug_id);
                if (!empty($custom_form_table)) {
                    foreach ($custom_form_table as $c_label => $v_fields) {
                        $sub_array[] = $v_fields;
                    }
                }
                $data[] = $sub_array;
            }
            render_table($data, $where);
        } else {
            redirect('client/dashboard');
        }
    }

    public function save_bug($id = NULL)
    {
        $data = $this->bugs_model->array_from_post(array(
            'issue_no',
            'bug_title',
            'bug_description',
            'priority'));

        $project_id = $this->input->post('project_id', TRUE);
        if (!empty($project_id)) {
            $data['project_id'] = $project_id;
        } else {
            $data['project_id'] = NULL;
        }
        $data['reporter'] = $this->session->userdata('user_id');

        $data['client_visible'] = 'Yes';
        $data['bug_status'] = 'unconfirmed';
        $data['severity'] = 'minor';
        $data['created_time'] = date("Y-m-d H:i:s");
        $data['permission'] = 'all';

        //save data into table.
        $this->bugs_model->_table_name = "tbl_bug"; // table name
        $this->bugs_model->_primary_key = "bug_id"; // $id
        $return_id = $this->bugs_model->save($data, $id);
        if (!empty($id)) {
            $msg = lang('update_bug');
            $activity = 'activity_update_bug';
        } else {
            $id = $return_id;
            $msg = lang('save_bug');
            $activity = 'activity_new_bug';
            $this->notify_bugs_reported($id);
        }
        save_custom_field(6, $id);
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'bugs',
            'module_field_id' => $id,
            'activity' => $activity,
            'icon' => 'fa-ticket',
            'value1' => $data['bug_title'],
        );
        // Update into tbl_project
        $this->bugs_model->_table_name = "tbl_activities"; //table name
        $this->bugs_model->_primary_key = "activities_id";
        $this->bugs_model->save($activities);

        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect($_SERVER['HTTP_REFERER']);
    }

    function notify_bugs_reported($bug_id)
    {

        $email_template = email_templates(array('email_group' => 'bug_reported'));
        $bugs_info = $this->bugs_model->check_by(array('bug_id' => $bug_id), 'tbl_bug');

        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $bug_title = str_replace("{BUG_TITLE}", $bugs_info->bug_title, $message);

        $assigned_by = str_replace("{ADDED_BY}", ucfirst($this->session->userdata('name')), $bug_title);
        $Link = str_replace("{BUG_URL}", base_url() . 'admin/bugs/view_bug_details/' . $bugs_info->bug_id, $assigned_by);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';

        $login_info = $this->db->where('role_id', 1)->get('tbl_users')->result();
        $notifyUsers = array();
        foreach ($login_info as $user) {
            $params['recipient'] = $user->email;
            $this->bugs_model->send_email($params);

            if ($user->user_id != $this->session->userdata('user_id')) {
                array_push($notifyUsers, $user->user_id);
                add_notification(array(
                    'to_user_id' => $user->user_id,
                    'from_user_id' => true,
                    'description' => 'not_bug_reported',
                    'link' => 'admin/bugs/view_bug_details/' . $bug_id,
                    'value' => $this->session->userdata('name'),
                ));
            }
        }
        if (!empty($notifyUsers)) {
            show_notification($notifyUsers);
        }
    }

    function notify_bugs($users, $bug_id, $update = NULL)
    {
        if (!empty($update)) {
            $email_template = email_templates(array('email_group' => 'bugs_updated'));
        } else {
            $email_template = email_templates(array('email_group' => 'bug_assigned'));
        }
        $bugs_info = $this->bugs_model->check_by(array('bug_id' => $bug_id), 'tbl_bug');
        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $bug_title = str_replace("{BUG_TITLE}", $bugs_info->bug_title, $message);

        $assigned_by = str_replace("{ASSIGNED_BY}", ucfirst($this->session->userdata('name')), $bug_title);
        $Link = str_replace("{BUG_URL}", base_url() . 'client/bugs/view_bug_details/' . $bugs_info->bug_id, $assigned_by);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';

        foreach ($users as $v_user) {
            $login_info = $this->bugs_model->check_by(array('user_id' => $v_user), 'tbl_users');
            $params['recipient'] = $login_info->email;
            $this->bugs_model->send_email($params);
        }
    }

    public function update_users($id)
    {
        // get all assign_user

        // get permission user by menu id
        $permission_user = $this->bugs_model->all_permission_user('58');
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
        $data['assign_user'] = array_merge($admin_user, $permission_user);
        $data['bugs_info'] = $this->bugs_model->check_by(array('bug_id' => $id), 'tbl_bug');
        $data['modal_subview'] = $this->load->view('client/bugs/_modal_users', $data, FALSE);
        $this->load->view('client/_layout_modal', $data);
    }

    public function update_member($id)
    {

        $bugs_info = $this->bugs_model->check_by(array('bug_id' => $id), 'tbl_bug');

        $permission = $this->input->post('permission', true);
        if (!empty($permission)) {

            if ($permission == 'everyone') {
                $assigned = 'all';
            } else {
                $assigned_to = $this->bugs_model->array_from_post(array('assigned_to'));
                if (!empty($assigned_to['assigned_to'])) {
                    foreach ($assigned_to['assigned_to'] as $assign_user) {
                        $assigned[$assign_user] = $this->input->post('action_' . $assign_user, true);
                    }
                }
            }
            if (!empty($assigned)) {
                if ($assigned != 'all') {
                    $assigned = json_encode($assigned);
                }
            } else {
                $assigned = 'all';
            }
            $data['permission'] = $assigned;
        } else {
            set_message('error', lang('assigned_to') . ' Field is required');
            redirect($_SERVER['HTTP_REFERER']);
        }

//save data into table.
        $this->bugs_model->_table_name = "tbl_bug"; // table name
        $this->bugs_model->_primary_key = "bug_id"; // $id
        $this->bugs_model->save($data, $id);

        $msg = lang('update_bug');
        $activity = 'activity_update_bug';
        if (!empty($assigned_to['assigned_to'])) {
            $this->notify_update_bugs($assigned_to['assigned_to'], $id);
        }

// save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'bugs',
            'module_field_id' => $id,
            'activity' => $activity,
            'icon' => 'fa-ticket',
            'value1' => $bugs_info->bug_title,
        );
// Update into tbl_project
        $this->bugs_model->_table_name = "tbl_activities"; //table name
        $this->bugs_model->_primary_key = "activities_id";
        $this->bugs_model->save($activities);

        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect($_SERVER['HTTP_REFERER']);

    }

    public function change_status($id, $status)
    {

        $bugs_info = $this->bugs_model->check_by(array('bug_id' => $id), 'tbl_bug');
        if (!empty($bugs_info->permission) && $bugs_info->permission != 'all') {
            $user = json_decode($bugs_info->permission);
            foreach ($user as $key => $v_user) {
                $allowed_user[] = $key;
            }
        } else {
            $allowed_user = $this->bugs_model->allowed_user_id('58');
        }
        if (!empty($allowed_user)) {
            $this->notify_update_bugs($allowed_user, $id, TRUE);
        }
        $data['bug_status'] = $status;

//save data into table.
        $this->bugs_model->_table_name = "tbl_bug"; // table name
        $this->bugs_model->_primary_key = "bug_id"; // $id
        $id = $this->bugs_model->save($data, $id);
// save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'bugs',
            'module_field_id' => $id,
            'activity' => 'activity_update_bug',
            'icon' => 'fa-ticket',
            'value1' => lang($data['bug_status']),
        );
// Update into tbl_project
        $this->bugs_model->_table_name = "tbl_activities"; //table name
        $this->bugs_model->_primary_key = "activities_id";
        $this->bugs_model->save($activities);

        $type = "success";
        $message = lang('update_bug');
        set_message($type, $message);
        redirect($_SERVER['HTTP_REFERER']);

    }

    public function save_bugs_notes($id = NULL)
    {

        $data = $this->bugs_model->array_from_post(array('notes'));

//save data into table.
        $this->bugs_model->_table_name = "tbl_bug"; // table name
        $this->bugs_model->_primary_key = "bug_id"; // $id
        $id = $this->bugs_model->save($data, $id);
// save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'bugs',
            'module_field_id' => $id,
            'activity' => 'activity_update_bug',
            'icon' => 'fa-ticket',
            'value1' => $data['bugs_notes'],
        );
// Update into tbl_project
        $this->bugs_model->_table_name = "tbl_activities"; //table name
        $this->bugs_model->_primary_key = "activities_id";
        $this->bugs_model->save($activities);

        $type = "success";
        $message = lang('update_bug');
        set_message($type, $message);
        redirect('client/bugs/view_bug_details/' . $id . '/' . $data['active'] = 4);
    }

    public function save_comments()
    {

        $data['bug_id'] = $this->input->post('bug_id', TRUE);
        $data['comment'] = $this->input->post('comment', TRUE);
        $data['user_id'] = $this->session->userdata('user_id');

        $files = $this->input->post("files", true);
        $target_path = getcwd() . "/uploads/";
        //process the fiiles which has been uploaded by dropzone
        if (!empty($files) && is_array($files)) {
            foreach ($files as $key => $file) {
                if (!empty($file)) {
                    $file_name = $this->input->post('file_name_' . $file, true);
                    $new_file_name = move_temp_file($file_name, $target_path);
                    $file_ext = explode(".", $new_file_name);
                    $is_image = check_image_extension($new_file_name);
                    $size = $this->input->post('file_size_' . $file, true) / 1000;
                    if ($new_file_name) {
                        $up_data[] = array(
                            "fileName" => $new_file_name,
                            "path" => "uploads/" . $new_file_name,
                            "fullPath" => getcwd() . "/uploads/" . $new_file_name,
                            "ext" => '.' . end($file_ext),
                            "size" => round($size, 2),
                            "is_image" => $is_image,
                        );
                        $success = true;
                    } else {
                        $success = false;
                    }
                }
            }
        }
        //process the files which has been submitted manually
        if ($_FILES) {
            $files = $_FILES['manualFiles'];
            if ($files && count($files) > 0) {
                foreach ($files["tmp_name"] as $key => $file) {
                    $temp_file = $file;
                    $file_name = $files["name"][$key];
                    $file_size = $files["size"][$key];
                    $new_file_name = move_temp_file($file_name, $target_path, "", $temp_file);
                    if ($new_file_name) {
                        $file_ext = explode(".", $new_file_name);
                        $is_image = check_image_extension($new_file_name);
                        $up_data[] = array(
                            "fileName" => $new_file_name,
                            "path" => "uploads/" . $new_file_name,
                            "fullPath" => getcwd() . "/uploads/" . $new_file_name,
                            "ext" => '.' . end($file_ext),
                            "size" => round($file_size, 2),
                            "is_image" => $is_image,
                        );
                    }
                }
            }
        }
        if (!empty($up_data)) {
            $data['comments_attachment'] = json_encode($up_data);
        }

//save data into table.
        $this->bugs_model->_table_name = "tbl_task_comment"; // table name
        $this->bugs_model->_primary_key = "task_comment_id"; // $id
        $comment_id = $this->bugs_model->save($data);
        if (!empty($comment_id)) {
// save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'bugs',
                'module_field_id' => $data['bug_id'],
                'activity' => 'activity_new_bug_comment',
                'icon' => 'fa-bug',
                'link' => 'client/bugs/view_bug_details/' . $data['bug_id'] . '/2',
                'value1' => $data['comment'],
            );
// Update into tbl_project
            $this->bugs_model->_table_name = "tbl_activities"; //table name
            $this->bugs_model->_primary_key = "activities_id";
            $this->bugs_model->save($activities);

            $bugs_info = $this->bugs_model->check_by(array('bug_id' => $data['bug_id']), 'tbl_bug');

            if (!empty($bugs_info->permission) && $bugs_info->permission != 'all') {
                $user = json_decode($bugs_info->permission);
                foreach ($user as $key => $v_user) {
                    $notifiedUsers[] = $key;
                }
            } else {
                $notifiedUsers = $this->bugs_model->allowed_user_id('58');
            }

            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => 'not_new_comment',
                            'link' => 'admin/bugs/view_bug_details/' . $data['bug_id'] . '/2',
                            'value' => lang('bug') . ' : ' . $bugs_info->bug_title,
                        ));
                    }
                }
                show_notification($notifiedUsers);
            }

            // send notification
            $this->notify_comments_bugs($comment_id);

            $response_data = "";
            $view_data['comment_details'] = $this->db->where(array('task_comment_id' => $comment_id))->order_by('comment_datetime', 'DESC')->get('tbl_task_comment')->result();
            $response_data = $this->load->view("client/bugs/comments_list", $view_data, true);
            echo json_encode(array("status" => 'success', "data" => $response_data, 'message' => lang('bug_comment_save')));
            exit();
        } else {
            echo json_encode(array("status" => 'error', 'message' => lang('error_occurred')));
            exit();
        }
    }

    public function save_comments_reply($task_comment_id)
    {
        $data['bug_id'] = $this->input->post('bug_id', TRUE);
        $data['comment'] = $this->input->post('reply_comments', TRUE);
        $data['user_id'] = $this->session->userdata('user_id');
        $data['comments_reply_id'] = $task_comment_id;
        $comments_info = $this->bugs_model->check_by(array('task_comment_id' => $task_comment_id), 'tbl_task_comment');
        $user = $this->bugs_model->check_by(array('user_id' => $comments_info->user_id), 'tbl_users');
        if ($user->role_id == 2) {
            $url = 'client/';
        } else {
            $url = 'admin/';
        }
        //save data into table.
        $this->bugs_model->_table_name = "tbl_task_comment"; // table name
        $this->bugs_model->_primary_key = "task_comment_id"; // $id
        $comment_id = $this->bugs_model->save($data);
        if (!empty($comment_id)) {
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'bugs',
                'module_field_id' => $data['bug_id'],
                'activity' => 'activity_new_comment_reply',
                'icon' => 'fa-bug',
                'link' => $url . 'bugs/view_bug_details/' . $data['bug_id'] . '/2',
                'value1' => $this->db->where('task_comment_id', $task_comment_id)->get('tbl_task_comment')->row()->comment,
                'value2' => $data['comment'],
            );
            // Update into tbl_project
            $this->bugs_model->_table_name = "tbl_activities"; //table name
            $this->bugs_model->_primary_key = "activities_id";
            $this->bugs_model->save($activities);

            $bugs_info = $this->bugs_model->check_by(array('bug_id' => $data['bug_id']), 'tbl_bug');

            $notifiedUsers = array($comments_info->user_id);

            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => 'not_comment_reply',
                            'link' => $url . 'bugs/view_bug_details/' . $data['bug_id'] . '/2',
                            'value' => lang('bug') . ' : ' . $bugs_info->bug_title,
                        ));
                    }
                }
                show_notification($notifiedUsers);
            }

            // send notification
            $this->notify_comments_bugs($comment_id);

            $response_data = "";
            $view_data['comment_reply_details'] = $this->db->where(array('task_comment_id' => $comment_id))->order_by('comment_datetime', 'ASC')->get('tbl_task_comment')->result();
            $response_data = $this->load->view("client/bugs/comments_reply", $view_data, true);
            echo json_encode(array("status" => 'success', "data" => $response_data, 'message' => lang('bug_comment_save')));
            exit();
        } else {
            echo json_encode(array("status" => 'error', 'message' => lang('error_occurred')));
            exit();
        }
    }


    function notify_comments_bugs($comment_id)
    {
        $email_template = email_templates(array('email_group' => 'bug_comments'));
        $bugs_comment_info = $this->bugs_model->check_by(array('task_comment_id' => $comment_id), 'tbl_task_comment');
        $user = $this->bugs_model->check_by(array('user_id' => $bugs_comment_info->user_id), 'tbl_users');
        if ($user->role_id == 2) {
            $url = 'client/';
        } else {
            $url = 'admin/';
        }

        $bugs_info = $this->bugs_model->check_by(array('bug_id' => $bugs_comment_info->bug_id), 'tbl_bug');
        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $bug_name = str_replace("{BUG_TITLE}", $bugs_info->bug_title, $message);
        $assigned_by = str_replace("{POSTED_BY}", ucfirst($this->session->userdata('name')), $bug_name);
        $Link = str_replace("{COMMENT_URL}", base_url() . $url . 'bugs/view_bug_details/' . $bugs_info->bug_id . '/' . $data['active'] = 2, $assigned_by);
        $comments = str_replace("{COMMENT_MESSAGE}", $bugs_comment_info->comment, $Link);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $comments);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';

        if (!empty($bugs_info->permission) && $bugs_info->permission != 'all') {
            $user = json_decode($bugs_info->permission);
            foreach ($user as $key => $v_user) {
                $allowed_user[] = $key;
            }
        } else {
            $allowed_user = $this->bugs_model->allowed_user_id('58');
        }
        if (!empty($allowed_user)) {
            foreach ($allowed_user as $v_user) {
                $login_info = $this->bugs_model->check_by(array('user_id' => $v_user), 'tbl_users');
                $params['recipient'] = $login_info->email;
                $this->bugs_model->send_email($params);
            }
        }
    }

    public function delete_bug_comments($task_comment_id)
    {
        $comments_info = $this->bugs_model->check_by(array('task_comment_id' => $task_comment_id), 'tbl_task_comment');
        if (empty($comments_info)) {
            echo json_encode(array("status" => 'error', 'message' => "No Record Found"));
            exit();
        }

        if (!empty($comments_info->comments_attachment)) {
            $attachment = json_decode($comments_info->comments_attachment);
            foreach ($attachment as $v_file) {
                remove_files($v_file->fileName);
            }
        }
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'bugs',
            'module_field_id' => $comments_info->bug_id,
            'activity' => 'activity_comment_deleted',
            'icon' => 'fa-bug',
            'link' => 'client/bugs/view_bug_details/' . $comments_info->bug_id . '/2',
            'value1' => $comments_info->comment,
        );
        // Update into tbl_project
        $this->bugs_model->_table_name = "tbl_activities"; //table name
        $this->bugs_model->_primary_key = "activities_id";
        $this->bugs_model->save($activities);

//save data into table.
        $this->bugs_model->_table_name = "tbl_task_comment"; // table name
        $this->bugs_model->_primary_key = "task_comment_id"; // $id
        $this->bugs_model->delete($task_comment_id);

        //save data into table.
        $this->bugs_model->_table_name = "tbl_task_comment"; // table name
        $this->bugs_model->delete_multiple(array('comments_reply_id' => $task_comment_id));

        echo json_encode(array("status" => 'success', 'message' => lang('bug_comment_deleted')));
        exit();
    }

    public function save_bug_attachment($task_attachment_id = NULL)
    {
        $data = $this->bugs_model->array_from_post(array('title', 'description', 'bug_id'));
        $data['user_id'] = $this->session->userdata('user_id');

// save and update into tbl_files
        $this->bugs_model->_table_name = "tbl_task_attachment"; //table name
        $this->bugs_model->_primary_key = "task_attachment_id";
        if (!empty($task_attachment_id)) {
            $id = $task_attachment_id;
            $this->bugs_model->save($data, $id);
            $msg = lang('bug_file_updated');
        } else {
            $id = $this->bugs_model->save($data);
            $msg = lang('bug_file_added');
        }

        if (!empty($_FILES['bug_files']['name']['0'])) {
            $old_path_info = $this->input->post('uploaded_path', true);
            if (!empty($old_path_info)) {
                foreach ($old_path_info as $old_path) {
                    unlink($old_path);
                }
            }
            $mul_val = $this->bugs_model->multi_uploadAllType('bug_files');

            foreach ($mul_val as $val) {
                $val == TRUE || redirect('client/bugs/view_bug_details/3/' . $data['bug_id']);
                $fdata['files'] = $val['path'];
                $fdata['file_name'] = $val['fileName'];
                $fdata['uploaded_path'] = $val['fullPath'];
                $fdata['size'] = $val['size'];
                $fdata['ext'] = $val['ext'];
                $fdata['is_image'] = $val['is_image'];
                $fdata['image_width'] = $val['image_width'];
                $fdata['image_height'] = $val['image_height'];
                $fdata['task_attachment_id'] = $id;
                $this->bugs_model->_table_name = "tbl_task_uploaded_files"; // table name
                $this->bugs_model->_primary_key = "uploaded_files_id"; // $id
                $this->bugs_model->save($fdata);
            }
        }
// save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'bugs',
            'module_field_id' => $data['bug_id'],
            'activity' => 'activity_new_bug_attachment',
            'icon' => 'fa-ticket',
            'value1' => $data['title'],
        );
// Update into tbl_project
        $this->bugs_model->_table_name = "tbl_activities"; //table name
        $this->bugs_model->_primary_key = "activities_id";
        $this->bugs_model->save($activities);
// send notification message
        $this->notify_attchemnt_bugs($id);
// messages for user
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('client/bugs/view_bug_details/' . $data['bug_id'] . '/3');
    }

    function notify_attchemnt_bugs($task_attachment_id)
    {
        $email_template = email_templates(array('email_group' => 'bug_attachment'));
        $bugs_comment_info = $this->bugs_model->check_by(array('task_attachment_id' => $task_attachment_id), 'tbl_task_attachment');

        $bugs_info = $this->bugs_model->check_by(array('bug_id' => $bugs_comment_info->bug_id), 'tbl_bug');

        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $bug_name = str_replace("{BUG_TITLE}", $bugs_info->bug_title, $message);
        $assigned_by = str_replace("{UPLOADED_BY}", ucfirst($this->session->userdata('name')), $bug_name);
        $Link = str_replace("{BUG_URL}", base_url() . 'client/bugs/view_bug_details/' . $bugs_info->bug_id . '/' . $data['active'] = 3, $assigned_by);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';
        if (!empty($bugs_info->permission) && $bugs_info->permission != 'all') {
            $user = json_decode($bugs_info->permission);
            foreach ($user as $key => $v_user) {
                $allowed_user[] = $key;
            }
        } else {
            $allowed_user = $this->bugs_model->allowed_user_id('58');
        }
        if (!empty($allowed_user)) {
            foreach ($allowed_user as $v_user) {
                $login_info = $this->bugs_model->check_by(array('user_id' => $v_user), 'tbl_users');
                $params['recipient'] = $login_info->email;
                $this->bugs_model->send_email($params);
            }
        }
    }

    public function view_bug_details($id, $active = NULL, $edit = NULL)
    {
        $data['title'] = lang('bug_details');
        $data['breadcrumbs'] = lang('bugs');
        $data['page'] = lang('bugs');

        //get all bug information
        $data['bug_details'] = $this->bugs_model->check_by(array('bug_id' => $id), 'tbl_bug');
        $user_id = $this->session->userdata('user_id');
        if ($user_id == $data['bug_details']->reporter) {
//        //get all comments info
            //        $data['comment_details'] = $this->bugs_model->get_all_comment_info($id);
            // get all assign_user
            $this->bugs_model->_table_name = 'tbl_users';
            $this->bugs_model->_order_by = 'user_id';
            $data['assign_user'] = $this->bugs_model->get_by(array('role_id !=' => '2'), FALSE);

            $this->bugs_model->_table_name = "tbl_task_attachment"; //table name
            $this->bugs_model->_order_by = "bug_id";
            $data['files_info'] = $this->bugs_model->get_by(array('bug_id' => $id), FALSE);

            foreach ($data['files_info'] as $key => $v_files) {
                $this->bugs_model->_table_name = "tbl_task_uploaded_files"; //table name
                $this->bugs_model->_order_by = "task_attachment_id";
                $data['project_files_info'][$key] = $this->bugs_model->get_by(array('task_attachment_id' => $v_files->task_attachment_id), FALSE);
            }
            $data['dropzone'] = true;

            if ($active == 2) {
                $data['active'] = 2;
            } elseif ($active == 3) {
                $data['active'] = 3;
            } elseif ($active == 4) {
                $data['active'] = 4;
            } else {
                $data['active'] = 1;
            }

            $data['subview'] = $this->load->view('client/bugs/view_bugs', $data, TRUE);
            $this->load->view('client/_layout_main', $data);
        } else {
            redirect('client/bugs');
        }
    }

    public function download_files($bug_id, $uploaded_files_id, $comments = null)
    {
        $this->load->helper('download');
        if (!empty($comments)) {
            if ($bug_id) {
                $down_data = file_get_contents('uploads/' . $uploaded_files_id); // Read the file's contents
                force_download($uploaded_files_id, $down_data);
            } else {
                $type = "error";
                $message = 'Operation Fieled !';
                set_message($type, $message);
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $uploaded_files_info = $this->bugs_model->check_by(array('uploaded_files_id' => $uploaded_files_id), 'tbl_task_uploaded_files');

            if ($uploaded_files_info->uploaded_path) {
                $data = file_get_contents($uploaded_files_info->uploaded_path); // Read the file's contents
                force_download($uploaded_files_info->file_name, $data);
            } else {
                $type = "error";
                $message = lang('operation_failed');
                set_message($type, $message);
                redirect('client/bugs/view_bug_details/' . $bug_id . '/3');
            }
        }
    }

    public function delete_bug($id)
    {

        $bug_info = $this->bugs_model->check_by(array('bug_id' => $id), 'tbl_bug');

// save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'bugs',
            'module_field_id' => $bug_info->bug_id,
            'activity' => 'activity_bug_deleted',
            'icon' => 'fa-ticket',
            'value1' => $bug_info->bug_title,
        );
// Update into tbl_project
        $this->bugs_model->_table_name = "tbl_activities"; //table name
        $this->bugs_model->_primary_key = "activities_id";
        $this->bugs_model->save($activities);

        $this->bugs_model->_table_name = "tbl_task_attachment"; //table name
        $this->bugs_model->_order_by = "bug_id";
        $files_info = $this->bugs_model->get_by(array('bug_id' => $id), FALSE);

        foreach ($files_info as $v_files) {
            $this->bugs_model->_table_name = "tbl_task_uploaded_files"; //table name
            $this->bugs_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
        }
        //delete into table.
        $this->bugs_model->_table_name = "tbl_task_attachment"; // table name
        $this->bugs_model->delete_multiple(array('bug_id' => $id));

        //delete data into table.
        $this->bugs_model->_table_name = "tbl_task_comment"; // table name
        $this->bugs_model->delete_multiple(array('bug_id' => $id));

        $this->bugs_model->_table_name = "tbl_bug"; // table name
        $this->bugs_model->_primary_key = "bug_id"; // $id
        $this->bugs_model->delete($id);

        $type = "success";
        $message = lang('bug_deleted');
        set_message($type, $message);
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function new_attachment($id)
    {
        $data['dropzone'] = true;
        $data['bugs_details'] = $this->bugs_model->check_by(array('bug_id' => $id), 'tbl_bug');
        $data['modal_subview'] = $this->load->view('client/bugs/new_attachment', $data, FALSE);
        $this->load->view('client/_layout_modal', $data);
    }

    public function attachment_details($type, $id)
    {
        $data['type'] = $type;
        $data['attachment_info'] = $this->bugs_model->check_by(array('task_attachment_id' => $id), 'tbl_task_attachment');
        $data['modal_subview'] = $this->load->view('client/bugs/attachment_details', $data, FALSE);
        $this->load->view('client/_layout_modal_extra_lg', $data);
    }

    public function save_attachment($task_attachment_id = NULL)
    {

        $data = $this->bugs_model->array_from_post(array('title', 'description', 'bug_id'));

        $data['user_id'] = $this->session->userdata('user_id');

        // save and update into tbl_files
        $this->bugs_model->_table_name = "tbl_task_attachment"; //table name
        $this->bugs_model->_primary_key = "task_attachment_id";
        if (!empty($task_attachment_id)) {
            $id = $task_attachment_id;
            $this->bugs_model->save($data, $id);
            $msg = lang('project_file_updated');
        } else {
            $id = $this->bugs_model->save($data);
            $msg = lang('project_file_added');
        }
        $files = $this->input->post("files");
        $target_path = getcwd() . "/uploads/";
        //process the fiiles which has been uploaded by dropzone
        if (!empty($files) && is_array($files)) {
            foreach ($files as $key => $file) {
                if (!empty($file)) {
                    $file_name = $this->input->post('file_name_' . $file, true);
                    $new_file_name = move_temp_file($file_name, $target_path);
                    $file_ext = explode(".", $new_file_name);
                    $is_image = check_image_extension($new_file_name);

                    if ($new_file_name) {
                        $up_data = array(
                            "files" => "uploads/" . $new_file_name,
                            "uploaded_path" => getcwd() . "/uploads/" . $new_file_name,
                            "file_name" => $new_file_name,
                            "size" => $this->input->post('file_size_' . $file, true),
                            "ext" => end($file_ext),
                            "is_image" => $is_image,
                            "image_width" => 0,
                            "image_height" => 0,
                            "task_attachment_id" => $id
                        );
                        $this->bugs_model->_table_name = "tbl_task_uploaded_files"; // table name
                        $this->bugs_model->_primary_key = "uploaded_files_id"; // $id
                        $uploaded_files_id = $this->bugs_model->save($up_data);

                        // saved into comments
                        $comment = $this->input->post('comment_' . $file, true);
                        $u_cdata = array(
                            "comment" => $comment,
                            "bug_id" => $data['bug_id'],
                            "user_id" => $this->session->userdata('user_id'),
                            "uploaded_files_id" => $uploaded_files_id,
                        );
                        $this->bugs_model->_table_name = "tbl_task_comment"; // table name
                        $this->bugs_model->_primary_key = "task_comment_id"; // $id
                        $this->bugs_model->save($u_cdata);

                        $success = true;

                    } else {
                        $success = false;
                    }
                }
            }
        }
        //process the files which has been submitted manually
        if ($_FILES) {
            $files = $_FILES['manualFiles'];
            if ($files && count($files) > 0) {
                $comment = $this->input->post('comment', true);
                foreach ($files["tmp_name"] as $key => $file) {
                    $temp_file = $file;
                    $file_name = $files["name"][$key];
                    $file_size = $files["size"][$key];
                    $new_file_name = move_temp_file($file_name, $target_path, "", $temp_file);
                    if ($new_file_name) {
                        $file_ext = explode(".", $new_file_name);
                        $is_image = check_image_extension($new_file_name);
                        $up_data = array(
                            "files" => "uploads/" . $new_file_name,
                            "uploaded_path" => getcwd() . "/uploads/" . $new_file_name,
                            "file_name" => $new_file_name,
                            "size" => $file_size,
                            "ext" => end($file_ext),
                            "is_image" => $is_image,
                            "image_width" => 0,
                            "image_height" => 0,
                            "task_attachment_id" => $id
                        );
                        $this->bugs_model->_table_name = "tbl_task_uploaded_files"; // table name
                        $this->bugs_model->_primary_key = "uploaded_files_id"; // $id
                        $uploaded_files_id = $this->bugs_model->save($up_data);

                        // saved into comments
                        if (!empty($comment[$key])) {
                            $u_cdata = array(
                                "comment" => $comment[$key],
                                "user_id" => $this->session->userdata('user_id'),
                                "uploaded_files_id" => $uploaded_files_id,
                            );
                            $this->bugs_model->_table_name = "tbl_task_comment"; // table name
                            $this->bugs_model->_primary_key = "task_comment_id"; // $id
                            $this->bugs_model->save($u_cdata);
                        }

                    }
                }
            }
        }

        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'bugs',
            'module_field_id' => $data['bug_id'],
            'activity' => 'activity_new_project_attachment',
            'icon' => 'fa-folder-open-o',
            'link' => 'admin/bugs/view_bug_details/' . $data['bug_id'] . '/3',
            'value1' => $data['title'],
        );
        // Update into tbl_project
        $this->bugs_model->_table_name = "tbl_activities"; //table name
        $this->bugs_model->_primary_key = "activities_id";
        $this->bugs_model->save($activities);

        // send notification message
        $this->notify_attchemnt_bugs($id);

        // messages for user
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('client/bugs/view_bug_details/' . $data['bug_id'] . '/' . '3');
    }


    public function save_attachment_comments()
    {
        $task_attachment_id = $this->input->post('task_attachment_id', true);
        if (!empty($task_attachment_id)) {
            $data['task_attachment_id'] = $task_attachment_id;
        } else {
            $data['uploaded_files_id'] = $this->input->post('uploaded_files_id', true);
        }
        $data['bug_id'] = $this->input->post('bug_id', true);
        $data['comment'] = $this->input->post('description', true);

        $files = $this->input->post("files", true);
        $target_path = getcwd() . "/uploads/";
        //process the fiiles which has been uploaded by dropzone
        if (!empty($files) && is_array($files)) {
            foreach ($files as $key => $file) {
                if (!empty($file)) {
                    $file_name = $this->input->post('file_name_' . $file, true);
                    $new_file_name = move_temp_file($file_name, $target_path);
                    $file_ext = explode(".", $new_file_name);
                    $is_image = check_image_extension($new_file_name);
                    $size = $this->input->post('file_size_' . $file, true) / 1000;
                    if ($new_file_name) {
                        $up_data[] = array(
                            "fileName" => $new_file_name,
                            "path" => "uploads/" . $new_file_name,
                            "fullPath" => getcwd() . "/uploads/" . $new_file_name,
                            "ext" => '.' . end($file_ext),
                            "size" => round($size, 2),
                            "is_image" => $is_image,
                        );
                        $success = true;
                    } else {
                        $success = false;
                    }
                }
            }
        }
        //process the files which has been submitted manually
        if ($_FILES) {
            $files = $_FILES['manualFiles'];
            if ($files && count($files) > 0) {
                $comment = $this->input->post('comment', true);
                foreach ($files["tmp_name"] as $key => $file) {
                    $temp_file = $file;
                    $file_name = $files["name"][$key];
                    $file_size = $files["size"][$key];
                    $new_file_name = move_temp_file($file_name, $target_path, "", $temp_file);
                    if ($new_file_name) {
                        $file_ext = explode(".", $new_file_name);
                        $is_image = check_image_extension($new_file_name);
                        $up_data[] = array(
                            "fileName" => $new_file_name,
                            "path" => "uploads/" . $new_file_name,
                            "fullPath" => getcwd() . "/uploads/" . $new_file_name,
                            "ext" => '.' . end($file_ext),
                            "size" => round($file_size, 2),
                            "is_image" => $is_image,
                        );
                    }
                }
            }
        }
        if (!empty($up_data)) {
            $data['comments_attachment'] = json_encode($up_data);
        }
        $data['user_id'] = $this->session->userdata('user_id');

        //save data into table.
        $this->bugs_model->_table_name = "tbl_task_comment"; // table name
        $this->bugs_model->_primary_key = "task_comment_id"; // $id
        $comment_id = $this->bugs_model->save($data);
        if (!empty($comment_id)) {
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'bugs',
                'module_field_id' => $data['bug_id'],
                'activity' => 'activity_new_bug_comment',
                'icon' => 'fa-bug',
                'link' => 'client/bugs/view_bug_details/' . $data['bug_id'] . '/2',
                'value1' => $data['comment'],
            );
            // Update into tbl_project
            $this->bugs_model->_table_name = "tbl_activities"; //table name
            $this->bugs_model->_primary_key = "activities_id";
            $this->bugs_model->save($activities);

            $notifiedUsers = array();
            $bugs_info = $this->bugs_model->check_by(array('bug_id' => $data['bug_id']), 'tbl_bug');

            if (!empty($bugs_info->permission) && $bugs_info->permission != 'all') {
                $user = json_decode($bugs_info->permission);
                foreach ($user as $key => $v_user) {
                    $notifiedUsers[] = $key;
                }
            } else {
                $notifiedUsers = $this->bugs_model->allowed_user_id('58');
            }
            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => 'not_new_comment',
                            'link' => 'admin/bugs/view_bug_details/' . $data['bug_id'] . '/2',
                            'value' => lang('bug') . ' : ' . $bugs_info->bug_title,
                        ));
                    }
                }
                show_notification($notifiedUsers);
            }
            $response_data = "";
            $view_data['comment_details'] = $this->db->where(array('task_comment_id' => $comment_id))->order_by('comment_datetime', 'DESC')->get('tbl_task_comment')->result();
            $response_data = $this->load->view("client/bugs/comments_list", $view_data, true);
            echo json_encode(array("status" => 'success', "data" => $response_data, 'message' => lang('bug_comment_save')));
            exit();
        } else {
            echo json_encode(array("status" => 'error', 'message' => lang('error_occurred')));
            exit();
        }

    }

    public function download_all_files($attachment_id)
    {
        $uploaded_files_info = $this->db->where('task_attachment_id', $attachment_id)->get('tbl_task_uploaded_files')->result();
        $attachment_info = $this->db->where('task_attachment_id', $attachment_id)->get('tbl_task_attachment')->row();
        $this->load->library('zip');
        if (!empty($uploaded_files_info)) {
            $filename = slug_it($attachment_info->title);
            foreach ($uploaded_files_info as $v_files) {
                $down_data = ($v_files->files); // Read the file's contents
                $this->zip->read_file($down_data);
            }
            $this->zip->download($filename . '.zip');
        } else {
            $type = "error";
            $message = lang('operation_failed');
            set_message($type, $message);
            redirect('client/bugs/view_bug_details/' . $attachment_info->bug_id . '/3');
        }
    }

    public function delete_bug_files($task_attachment_id)
    {
        $file_info = $this->bugs_model->check_by(array('task_attachment_id' => $task_attachment_id), 'tbl_task_attachment');
        if (empty($file_info)) {
            echo json_encode(array("status" => 'error', 'message' => "No Record Found"));
            exit();
        }
// save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'bugs',
            'module_field_id' => $file_info->bug_id,
            'activity' => 'activity_bug_attachfile_deleted',
            'icon' => 'fa-bug',
            'link' => 'admin/bugs/view_bug_details/' . $file_info->bug_id . '/3',
            'value1' => $file_info->title,
        );
// Update into tbl_project
        $this->bugs_model->_table_name = "tbl_activities"; //table name
        $this->bugs_model->_primary_key = "activities_id";
        $this->bugs_model->save($activities);

//save data into table.
        $this->bugs_model->_table_name = "tbl_task_attachment"; // table name
        $this->bugs_model->delete_multiple(array('task_attachment_id' => $task_attachment_id));

        $uploadFileinfo = $this->db->where('task_attachment_id', $task_attachment_id)->get('tbl_task_uploaded_files')->result();
        if (!empty($uploadFileinfo)) {
            foreach ($uploadFileinfo as $Fileinfo) {
                remove_files($Fileinfo->file_name);
            }
        }
        //save data into table.
        $this->bugs_model->_table_name = "tbl_task_uploaded_files"; // table name
        $this->bugs_model->delete_multiple(array('task_attachment_id' => $task_attachment_id));

        echo json_encode(array("status" => 'success', 'message' => lang('bug_attachfile_deleted')));
        exit();
    }


}
