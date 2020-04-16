<?php

/**
 * Description of Tasks
 *
 * @author Nayeem
 */
class Tasks extends Client_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('tasks_model');
    }

    public function save_comments()
    {

        $data['task_id'] = $this->input->post('task_id', TRUE);
        $data['comment'] = $this->input->post('comment', TRUE);
        $data['user_id'] = $this->session->userdata('user_id');

//save data into table.
        $this->tasks_model->_table_name = "tbl_task_comment"; // table name
        $this->tasks_model->_primary_key = "task_comment_id"; // $id
        $comment_id = $this->tasks_model->save($data);
// save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'tasks',
            'module_field_id' => $data['task_id'],
            'activity' => 'activity_new_task_comment',
            'icon' => 'fa-ticket',
            'value1' => $data['comment'],
        );
// Update into tbl_project
        $this->tasks_model->_table_name = "tbl_activities"; //table name
        $this->tasks_model->_primary_key = "activities_id";
        $this->tasks_model->save($activities);

// send notification
        $this->notify_comments_tasks($comment_id);

        $type = "success";
        $message = lang('task_comment_save');
        set_message($type, $message);
        redirect('client/tasks/view_task_details/' . $data['task_id'] . '/' . $data['active'] = 2);
    }

    function notify_comments_tasks($comment_id)
    {
        $email_template = email_templates(array('email_group' => 'tasks_comments'));
        $tasks_comment_info = $this->tasks_model->check_by(array('task_comment_id' => $comment_id), 'tbl_task_comment');

        $tasks_info = $this->tasks_model->check_by(array('task_id' => $tasks_comment_info->task_id), 'tbl_task');
        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $task_name = str_replace("{TASK_NAME}", $tasks_info->task_name, $message);
        $assigned_by = str_replace("{POSTED_BY}", ucfirst($this->session->userdata('name')), $task_name);
        $Link = str_replace("{COMMENT_URL}", base_url() . 'client/tasks/view_task_details/' . $tasks_info->task_id . '/' . $data['active'] = 2, $assigned_by);
        $comments = str_replace("{COMMENT_MESSAGE}", $tasks_comment_info->comment, $Link);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $comments);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';


        if (!empty($tasks_info->permission) && $tasks_info->permission != 'all') {
            $user = json_decode($tasks_info->permission);
            foreach ($user as $key => $v_user) {
                $allowed_user[] = $key;
            }
        } else {
            $allowed_user = $this->tasks_model->allowed_user_id('54');
        }
        if (!empty($allowed_user)) {
            foreach ($allowed_user as $v_user) {
                $login_info = $this->tasks_model->check_by(array('user_id' => $v_user), 'tbl_users');
                $params['recipient'] = $login_info->email;
                $this->tasks_model->send_email($params);
            }
        }
    }

    public function delete_task_comments($task_id, $task_comment_id)
    {
//save data into table.
        $this->tasks_model->_table_name = "tbl_task_comment"; // table name
        $this->tasks_model->_primary_key = "task_comment_id"; // $id
        $this->tasks_model->delete($task_comment_id);

        $type = "success";
        $message = lang('task_comment_deleted');
        set_message($type, $message);
        redirect('client/tasks/view_task_details/' . $task_id . '/' . $data['active'] = 2);
    }

    public function delete_task_files($task_id, $task_attachment_id)
    {
        $file_info = $this->tasks_model->check_by(array('task_attachment_id' => $task_attachment_id), 'tbl_task_attachment');
// save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'tasks',
            'module_field_id' => $task_id,
            'activity' => 'activity_task_attachfile_deleted',
            'icon' => 'fa-ticket',
            'value1' => $file_info->title,
        );
// Update into tbl_project
        $this->tasks_model->_table_name = "tbl_activities"; //table name
        $this->tasks_model->_primary_key = "activities_id";
        $this->tasks_model->save($activities);

//save data into table.
        $this->tasks_model->_table_name = "tbl_task_attachment"; // table name
        $this->tasks_model->delete_multiple(array('task_attachment_id' => $task_attachment_id));

        $type = "success";
        $message = lang('task_attachfile_deleted');
        set_message($type, $message);
        redirect('client/tasks/view_task_details/' . $task_id . '/' . $data['active'] = 3);
    }

    public function save_task_attachment($task_attachment_id = NULL)
    {
        $data = $this->tasks_model->array_from_post(array('title', 'description', 'task_id'));
        $data['user_id'] = $this->session->userdata('user_id');

// save and update into tbl_files
        $this->tasks_model->_table_name = "tbl_task_attachment"; //table name
        $this->tasks_model->_primary_key = "task_attachment_id";
        if (!empty($task_attachment_id)) {
            $id = $task_attachment_id;
            $this->tasks_model->save($data, $id);
            $msg = lang('task_file_updated');
        } else {
            $id = $this->tasks_model->save($data);
            $msg = lang('task_file_added');
        }

        if (!empty($_FILES['task_files']['name']['0'])) {
            $old_path_info = $this->input->post('uploaded_path', true);
            if (!empty($old_path_info)) {
                foreach ($old_path_info as $old_path) {
                    unlink($old_path);
                }
            }
            $mul_val = $this->tasks_model->multi_uploadAllType('task_files');

            foreach ($mul_val as $val) {
                $val == TRUE || redirect('client/tasks/view_task_details/3/' . $data['task_id']);
                $fdata['files'] = $val['path'];
                $fdata['file_name'] = $val['fileName'];
                $fdata['uploaded_path'] = $val['fullPath'];
                $fdata['size'] = $val['size'];
                $fdata['ext'] = $val['ext'];
                $fdata['is_image'] = $val['is_image'];
                $fdata['image_width'] = $val['image_width'];
                $fdata['image_height'] = $val['image_height'];
                $fdata['task_attachment_id'] = $id;
                $this->tasks_model->_table_name = "tbl_task_uploaded_files"; // table name
                $this->tasks_model->_primary_key = "uploaded_files_id"; // $id
                $this->tasks_model->save($fdata);
            }
        }
// save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'tasks',
            'module_field_id' => $data['task_id'],
            'activity' => 'activity_new_task_attachment',
            'icon' => 'fa-ticket',
            'value1' => $data['title'],
        );
// Update into tbl_project
        $this->tasks_model->_table_name = "tbl_activities"; //table name
        $this->tasks_model->_primary_key = "activities_id";
        $this->tasks_model->save($activities);
// send notification message
        $this->notify_attchemnt_tasks($id);
// messages for user
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('client/tasks/view_task_details/' . $data['task_id'] . '/3');
    }

    function notify_attchemnt_tasks($task_attachment_id)
    {
        $email_template = email_templates(array('email_group' => 'tasks_attachment'));
        $tasks_comment_info = $this->tasks_model->check_by(array('task_attachment_id' => $task_attachment_id), 'tbl_task_attachment');

        $tasks_info = $this->tasks_model->check_by(array('task_id' => $tasks_comment_info->task_id), 'tbl_task');
        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $task_name = str_replace("{TASK_NAME}", $tasks_info->task_name, $message);
        $assigned_by = str_replace("{UPLOADED_BY}", ucfirst($this->session->userdata('name')), $task_name);
        $Link = str_replace("{TASK_URL}", base_url() . 'client/tasks/view_task_details/' . $tasks_info->task_id . '/' . $data['active'] = 3, $assigned_by);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';

        if (!empty($tasks_info->permission) && $tasks_info->permission != 'all') {
            $user = json_decode($tasks_info->permission);
            foreach ($user as $key => $v_user) {
                $allowed_user[] = $key;
            }
        } else {
            $allowed_user = $this->tasks_model->allowed_user_id('54');
        }
        if (!empty($allowed_user)) {
            foreach ($allowed_user as $v_user) {
                $login_info = $this->tasks_model->check_by(array('user_id' => $v_user), 'tbl_users');
                $params['recipient'] = $login_info->email;
                $this->tasks_model->send_email($params);
            }
        }
    }

    public function view_task_details($id, $active = NULL, $edit = NULL)
    {
        $data['task_details'] = $this->tasks_model->check_by(array('task_id' => $id), 'tbl_task');
        if (!empty($data['task_details']) && !empty($data['task_details']->project_id)) {
            $project_info = $this->db->where('project_id', $data['task_details']->project_id)->get('tbl_project')->row();

            $client_id = client_id();
            if ($project_info->client_id == $client_id) {
                if (!empty($edit)) {
                    $tasks_timer_id = $id;
                    $id = $this->db->where(array('tasks_timer_id' => $id))->get('tbl_tasks_timer')->row()->task_id;
                } else {
                    $id = $id;
                }
                $data['title'] = lang('task_details');
                $data['page'] = lang('project');
                $data['breadcrumbs'] = lang('task');
//get all task information
//        //get all comments info
//        $data['comment_details'] = $this->tasks_model->get_all_comment_info($id);
                $this->tasks_model->_table_name = "tbl_task_attachment"; //table name
                $this->tasks_model->_order_by = "task_id";
                $data['files_info'] = $this->tasks_model->get_by(array('task_id' => $id), FALSE);

                foreach ($data['files_info'] as $key => $v_files) {
                    $this->tasks_model->_table_name = "tbl_task_uploaded_files"; //table name
                    $this->tasks_model->_order_by = "task_attachment_id";
                    $data['project_files_info'][$key] = $this->tasks_model->get_by(array('task_attachment_id' => $v_files->task_attachment_id), FALSE);
                }
                if ($active == 2) {
                    $data['active'] = 2;
                    $data['time_active'] = 1;
                } elseif ($active == 3) {
                    $data['active'] = 3;
                    $data['time_active'] = 1;
                } elseif ($active == 4) {
                    $data['active'] = 4;
                    $data['time_active'] = 1;
                } elseif ($active == 5) {
                    $data['active'] = 5;
                    if (!empty($edit)) {
                        $data['time_active'] = 2;
                    } else {
                        $data['time_active'] = 1;
                    }
                } else {
                    $data['active'] = 1;
                    $data['time_active'] = 1;
                }
                $data['subview'] = $this->load->view('client/tasks/view_task', $data, TRUE);
                $this->load->view('client/_layout_main', $data);
            }
        } else {
            redirect('client/projects');
        }
    }

    public function download_files($task_id, $uploaded_files_id)
    {
        $this->load->helper('download');
        $uploaded_files_info = $this->tasks_model->check_by(array('uploaded_files_id' => $uploaded_files_id), 'tbl_task_uploaded_files');

        if ($uploaded_files_info->uploaded_path) {
            $data = file_get_contents($uploaded_files_info->uploaded_path); // Read the file's contents            
            force_download($uploaded_files_info->file_name, $data);
        } else {
            $type = "error";
            $message = lang('operation_failed');
            set_message($type, $message);
            redirect('client/tasks/view_task_details/' . $task_id . '/3');
        }
    }

    public function claer_activities($module, $id)
    {
        //save data into table.
        $where = array('module' => $module, 'module_field_id' => $id);
        $this->tasks_model->_table_name = "tbl_activities"; // table name
        $this->tasks_model->delete_multiple($where);
        redirect($_SERVER['HTTP_REFERER']);

    }
}
