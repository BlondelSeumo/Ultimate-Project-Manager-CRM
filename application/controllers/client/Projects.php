<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Projects extends Client_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('items_model');
        $this->load->model('invoice_model');
        $this->load->model('estimates_model');

        $this->load->helper('ckeditor');
        $this->data['ckeditor'] = array(
            'id' => 'ck_editor',
            'path' => 'asset/js/ckeditor',
            'config' => array(
                'toolbar' => "Full",
                'width' => "99.8%",
                'height' => "400px"
            )
        );
    }

    public function index($id = NULL)
    {
        $data['title'] = lang('all_project');
        $data['breadcrumbs'] = lang('project');
        $data['page'] = lang('project');
        // get all assign_user
        $this->items_model->_table_name = 'tbl_users';
        $this->items_model->_order_by = 'user_id';
        $data['assign_user'] = $this->items_model->get_by(array('role_id !=' => '2'), FALSE);
        if (!empty($id)) {
            $data['active'] = 2;
            $data['project_info'] = $this->items_model->check_by(array('project_id' => $id), 'tbl_project');
            if (empty($data['project_info'])) {
                redirect('client/projects');
            }
        } else {
            $data['active'] = 1;
        }
        $data['subview'] = $this->load->view('client/projects/all_project', $data, TRUE);
        $this->load->view('client/_layout_main', $data); //page load
    }

    public function projectList($type = null)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_project';
            $this->datatables->column_order = array('project_name', 'start_date', 'end_date', 'project_status');
            $this->datatables->column_search = array('project_name', 'start_date', 'end_date', 'project_status');
            $this->datatables->order = array('project_id' => 'desc');

            if (!empty($type)) {
                $where = array('client_id' => client_id(), 'project_status' => $type);
            } else {
                $where = array('client_id' => client_id());
            }
            $fetch_data = array_reverse(make_datatables($where));

            $data = array();
            foreach ($fetch_data as $key => $v_project) {
                $action = null;
                $progress = $this->items_model->get_project_progress($v_project->project_id);

                $sub_array = array();
                $name = null;
                $name .= '<a class="text-info" href="' . base_url() . 'client/projects/project_details/' . $v_project->project_id . '">' . $v_project->project_name . '</a>';
                if (strtotime(date('Y-m-d')) > strtotime($v_project->end_date) && $progress < 100) {
                    $name .= '<span class="label label-danger pull-right">' . lang("overdue") . '</span>';
                }
                $name .= '<div class="progress progress-xs progress-striped active"><div class="progress-bar progress-bar-' . (($progress >= 100) ? "success" : "primary") . '"data-toggle = "tooltip" data-original-title = "' . $progress . '%" style = "width:' . $progress . '%" ></div></div>';
                $sub_array[] = $name;

                $sub_array[] = display_date($v_project->start_date);
                $sub_array[] = display_date($v_project->end_date);

                $statusss = null;
                if (!empty($v_project->project_status)) {
                    if ($v_project->project_status == 'completed') {
                        $statusss = "<span class='label label-success'>" . lang($v_project->project_status) . "</span>";
                    } elseif ($v_project->project_status == 'in_progress') {
                        $statusss = "<span class='label label-primary'>" . lang($v_project->project_status) . "</span>";
                    } elseif ($v_project->project_status == 'cancel') {
                        $statusss = "<span class='label label-danger'>" . lang($v_project->project_status) . "</span>";
                    } else {
                        $statusss = "<span class='label label-warning'>" . lang($v_project->project_status) . "</span>";
                    }
                }
                $sub_array[] = $statusss;
                $custom_form_table = custom_form_table(4, $v_project->project_id);

                if (!empty($custom_form_table)) {
                    foreach ($custom_form_table as $c_label => $v_fields) {
                        $sub_array[] = $v_fields;
                    }
                }
                $action .= btn_view('client/projects/project_details/' . $v_project->project_id) . ' ';
                $sub_array[] = $action;
                $data[] = $sub_array;
            }
            render_table($data, $where);
        } else {
            redirect('client/dashboard');
        }
    }

    public function saved_project($id = NULL)
    {
        $this->items_model->_table_name = 'tbl_project';
        $this->items_model->_primary_key = 'project_id';
        $data = $this->items_model->array_from_post(array('project_name', 'start_date', 'end_date', 'billing_type', 'project_cost', 'hourly_rate', 'demo_url', 'description'));
        $data['client_id'] = $this->session->userdata('client_id');
        $projects = '';
        if (empty(config_item('projects_number_format'))) {
            $projects .= config_item('projects_prefix');
        }
        $projects .= $this->items_model->generate_projects_number();
        $data['project_no'] = $projects;

        if (empty($data['project_cost'])) {
            $data['project_cost'] = '0';
        }
        if (empty($data['hourly_rate'])) {
            $data['hourly_rate'] = '0';
        }
        if (empty($id)) {
            $data['project_status'] = 'started';
            $data['progress'] = '0';
        }

        $return_id = $this->items_model->save($data, $id);

        $assigned_to['assigned_to'] = $this->items_model->allowed_user_id('57');

        if (!empty($id)) {
            $id = $id;
            $action = 'activity_update_project';
            $msg = lang('update_project');
        } else {
            $id = $return_id;
            $action = 'activity_save_project';
            $msg = lang('save_project');
            $this->send_project_notify_client($return_id);
            $this->send_project_notify_assign_user($return_id, $assigned_to['assigned_to']);
        }

        save_custom_field(4, $id);

        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'project',
            'module_field_id' => $id,
            'activity' => $action,
            'icon' => 'fa-circle-o',
            'value1' => $data['project_name']
        );
        $this->items_model->_table_name = 'tbl_activities';
        $this->items_model->_primary_key = 'activities_id';
        $this->items_model->save($activity);
        // messages for user
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('client/projects');
    }

    public function send_project_notify_assign_user($project_id, $users)
    {
        $project_info = $this->items_model->check_by(array('project_id' => $project_id), 'tbl_project');
        $email_template = email_templates(array('email_group' => 'assigned_project'), $project_info->client_id);
        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $project_name = str_replace("{PROJECT_NAME}", $project_info->project_name, $message);

        $assigned_by = str_replace("{ASSIGNED_BY}", ucfirst($this->session->userdata('name')), $project_name);
        $Link = str_replace("{PROJECT_LINK}", base_url() . 'client/projects/project_details/' . $project_id, $assigned_by);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';
        if (!empty($users)) {
            foreach ($users as $v_user) {
                $login_info = $this->items_model->check_by(array('user_id' => $v_user), 'tbl_users');
                $params['recipient'] = $login_info->email;
                $this->items_model->send_email($params);

                if ($v_user != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $v_user,
                        'from_user_id' => true,
                        'description' => 'assign_to_you_the_project',
                        'link' => 'admin/projects/project_details/' . $project_id,
                        'value' => $project_info->project_name,
                    ));
                }
            }
            show_notification($users);
        }
    }

    public function send_project_notify_client($project_id, $complete = NULL)
    {
        $project_info = $this->items_model->check_by(array('project_id' => $project_id), 'tbl_project');
        if (!empty($complete)) {
            $email_template = email_templates(array('email_group' => 'complete_projects'), $project_info->client_id);
        } else {
            $email_template = email_templates(array('email_group' => 'client_notification'), $project_info->client_id);
            $description = 'not_new_project_created';
        }
        $client_info = $this->items_model->check_by(array('client_id' => $project_info->client_id), 'tbl_client');
        if (!empty($client_info)) {
            $message = $email_template->template_body;
            $subject = $email_template->subject;
            $clientName = str_replace("{CLIENT_NAME}", $client_info->name, $message);
            $project_name = str_replace("{PROJECT_NAME}", $project_info->project_name, $clientName);

            $Link = str_replace("{PROJECT_LINK}", base_url() . 'client/projects/project_details/' . $project_id, $project_name);
            $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);

            $data['message'] = $message;
            $message = $this->load->view('email_template', $data, TRUE);

            $params['subject'] = $subject;
            $params['message'] = $message;
            $params['resourceed_file'] = '';

            $params['recipient'] = $client_info->email;
            $this->items_model->send_email($params);

            if (!empty($client_info->primary_contact)) {
                $notifyUser = array($client_info->primary_contact);
            } else {
                $user_info = $this->items_model->check_by(array('company' => $project_info->client_id), 'tbl_account_details');
                if (!empty($user_info)) {
                    $notifyUser = array($user_info->user_id);
                }
            }
            if (!empty($notifyUser)) {
                foreach ($notifyUser as $v_user) {
                    if ($v_user != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $v_user,
                            'from_user_id' => true,
                            'description' => $description,
                            'link' => 'client/projects/project_details/' . $project_id,
                            'value' => $project_info->project_name,
                        ));
                    }
                }
                show_notification($notifyUser);
            }
        }
    }

    public function project_details($id, $active = NULL, $op_id = NULL)
    {
        $data['title'] = lang('project_details');
        $data['breadcrumbs'] = lang('project_details');
        $data['page'] = lang('project');
        //get all task information
        $data['project_details'] = $this->items_model->check_by(array('project_id' => $id), 'tbl_project');
        if (empty($data['project_details'])) {
            redirect('client/projects');
        }
        $client_id = client_id();
        if ($data['project_details']->client_id == $client_id) {


            $this->items_model->_table_name = "tbl_task_attachment"; //table name
            $this->items_model->_order_by = "project_id";
            $data['files_info'] = $this->items_model->get_by(array('project_id' => $id), FALSE);

            if (!empty($data['files_info'])) {
                foreach ($data['files_info'] as $key => $v_files) {
                    $this->items_model->_table_name = "tbl_task_uploaded_files"; //table name
                    $this->items_model->_order_by = "task_attachment_id";
                    $data['project_files_info'][$key] = $this->items_model->get_by(array('task_attachment_id' => $v_files->task_attachment_id), FALSE);
                }
            }
            $data['dropzone'] = true;
            if ($active == 2) {
                $data['active'] = 2;
                $data['miles_active'] = 1;
                $data['task_active'] = 1;
                $data['bugs_active'] = 1;
                $data['time_active'] = 1;
            } elseif ($active == 3) {
                $data['active'] = 3;
                $data['miles_active'] = 1;
                $data['task_active'] = 1;
                $data['bugs_active'] = 1;
                $data['time_active'] = 1;
            } elseif ($active == 4) {
                $data['active'] = 4;
                $data['miles_active'] = 1;
                $data['task_active'] = 1;
                $data['bugs_active'] = 1;
                $data['time_active'] = 1;
            } elseif ($active == 5) {
                $data['active'] = 5;
                $data['miles_active'] = 1;
                $data['task_active'] = 1;
                $data['bugs_active'] = 1;
                $data['time_active'] = 1;
            } elseif ($active == 'milestone') {
                $data['active'] = 5;
                $data['miles_active'] = 2;
                $data['task_active'] = 1;
                $data['bugs_active'] = 1;
                $data['time_active'] = 1;
                $data['milestones_info'] = $this->items_model->check_by(array('milestones_id' => $op_id), 'tbl_milestones');
            } elseif ($active == 6) {
                $data['active'] = 6;
                $data['miles_active'] = 1;
                $data['task_active'] = 1;
                $data['bugs_active'] = 1;
                $data['time_active'] = 1;
            } elseif ($active == 7) {
                $data['active'] = 7;
                $data['miles_active'] = 1;
                $data['task_active'] = 1;
                $data['bugs_active'] = 1;
                if (!empty($op_id)) {
                    $data['time_active'] = 2;
                    $data['project_timer_info'] = $this->items_model->check_by(array('tasks_timer_id' => $op_id), 'tbl_tasks_timer');
                } else {
                    $data['time_active'] = 1;
                }
            } elseif ($active == 8) {
                $data['active'] = 8;
                $data['miles_active'] = 1;
                $data['task_active'] = 1;
                $data['bugs_active'] = 1;
                $data['time_active'] = 1;
            } elseif ($active == 10) {
                $data['active'] = 10;
                $data['miles_active'] = 1;
                $data['task_active'] = 1;
                $data['bugs_active'] = 1;
                $data['time_active'] = 1;
            } elseif ($active == 13) {
                $data['active'] = 13;
                $data['miles_active'] = 1;
                $data['task_active'] = 1;
                $data['bugs_active'] = 1;
                $data['time_active'] = 1;
            } elseif ($active == 15) {
                $data['active'] = 15;
                $data['miles_active'] = 1;
                $data['task_active'] = 1;
                $data['bugs_active'] = 1;
                $data['time_active'] = 1;
            } else {
                $data['active'] = 1;
                $data['miles_active'] = 1;
                $data['task_active'] = 1;
                $data['bugs_active'] = 1;
                $data['time_active'] = 1;
            }

            $data['subview'] = $this->load->view('client/projects/project_details', $data, TRUE);
            $this->load->view('client/_layout_main', $data);
        } else {
            redirect('client/projects');
        }
    }


    public function save_comments()
    {

        $data['project_id'] = $this->input->post('project_id', true);
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
        $this->items_model->_table_name = "tbl_task_comment"; // table name
        $this->items_model->_primary_key = "task_comment_id"; // $id
        $comment_id = $this->items_model->save($data);
        if (!empty($comment_id)) {
            $project_info = $this->items_model->check_by(array('project_id' => $data['project_id']), 'tbl_project');
            $notifiedUsers = array();
            if (!empty($project_info->permission) && $project_info->permission != 'all') {
                $permissionUsers = json_decode($project_info->permission);
                foreach ($permissionUsers as $user => $v_permission) {
                    array_push($notifiedUsers, $user);
                }
            } else {
                $notifiedUsers = $this->items_model->allowed_user_id('57');
            }
            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => 'not_new_comment',
                            'link' => 'client/projects/project_details/' . $project_info->project_id . '/3',
                            'value' => lang('project') . ' ' . $project_info->project_name,
                        ));
                    }
                }
            }
            show_notification($notifiedUsers);

            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'project',
                'module_field_id' => $data['project_id'],
                'activity' => 'activity_new_project_comment',
                'icon' => 'fa-folder-open-o',
                'link' => 'admin/projects/project_details/' . $project_info->project_id . '/3',
                'value1' => $data['comment'],
            );
            // Update into tbl_project
            $this->items_model->_table_name = "tbl_activities"; //table name
            $this->items_model->_primary_key = "activities_id";
            $this->items_model->save($activities);
            // send notification
            $this->notify_comments_project($comment_id);
            $response_data = "";
            $view_data['comment_details'] = $this->db->where(array('task_comment_id' => $comment_id))->order_by('comment_datetime', 'DESC')->get('tbl_task_comment')->result();
            $response_data = $this->load->view("client/projects/comments_list", $view_data, true);
            echo json_encode(array("status" => 'success', "data" => $response_data, 'message' => lang('project_comment_save')));
            exit();
//            $type = "success";
//            $message = lang('project_comment_save');
//            set_message($type, $message);
        } else {
            echo json_encode(array("status" => 'error', 'message' => lang('error_occurred')));
            exit();
        }
    }

    public function save_comments_reply($task_comment_id)
    {
        $data['project_id'] = $this->input->post('project_id', TRUE);
        $data['comment'] = $this->input->post('reply_comments', TRUE);
        $data['user_id'] = $this->session->userdata('user_id');
        $data['comments_reply_id'] = $task_comment_id;
        //save data into table.
        $this->items_model->_table_name = "tbl_task_comment"; // table name
        $this->items_model->_primary_key = "task_comment_id"; // $id
        $comment_id = $this->items_model->save($data);
        if (!empty($comment_id)) {
            $comments_info = $this->items_model->check_by(array('task_comment_id' => $task_comment_id), 'tbl_task_comment');

            $project_info = $this->items_model->check_by(array('project_id' => $data['project_id']), 'tbl_project');
            $notifiedUsers = array($comments_info->user_id);
            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => 'not_comment_reply',
                            'link' => 'admin/projects/project_details/' . $project_info->project_id . '/3',
                            'value' => lang('project') . ' ' . $project_info->project_name,
                        ));
                    }
                }
            }
            show_notification($notifiedUsers);

            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'project',
                'module_field_id' => $data['project_id'],
                'activity' => 'activity_new_comment_reply',
                'icon' => 'fa-folder-open-o',
                'link' => 'admin/projects/project_details/' . $project_info->project_id . '/3',
                'value1' => $this->db->where('task_comment_id', $task_comment_id)->get('tbl_task_comment')->row()->comment,
                'value2' => $data['comment'],
            );
            // Update into tbl_project
            $this->items_model->_table_name = "tbl_activities"; //table name
            $this->items_model->_primary_key = "activities_id";
            $this->items_model->save($activities);

            // send notification
            $this->notify_comments_project($comment_id);
            $response_data = "";
            $view_data['comment_reply_details'] = $this->db->where(array('task_comment_id' => $comment_id))->order_by('comment_datetime', 'ASC')->get('tbl_task_comment')->result();
            $response_data = $this->load->view("client/projects/comments_reply", $view_data, true);
            echo json_encode(array("status" => 'success', "data" => $response_data, 'message' => lang('project_comment_save')));
            exit();
//
//            $type = "success";
//            $message = lang('project_comment_save');
//            set_message($type, $message);
        } else {
            echo json_encode(array("status" => 'error', 'message' => lang('error_occurred')));
            exit();
        }
    }

    function notify_comments_project($comment_id)
    {

        $comment_info = $this->items_model->check_by(array('task_comment_id' => $comment_id), 'tbl_task_comment');
        $project_info = $this->items_model->check_by(array('project_id' => $comment_info->project_id), 'tbl_project');
        $email_template = email_templates(array('email_group' => 'project_comments'), $project_info->client_id);

        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $projectName = str_replace("{PROJECT_NAME}", $project_info->project_name, $message);
        $assigned_by = str_replace("{POSTED_BY}", ucfirst($this->session->userdata('name')), $projectName);
        $Link = str_replace("{COMMENT_URL}", base_url() . 'client/projects/project_details/' . $project_info->project_id . '/' . $data['active'] = 3, $assigned_by);
        $comments = str_replace("{COMMENT_MESSAGE}", $comment_info->comment, $Link);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $comments);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';

        if (!empty($project_info->permission) && $project_info->permission != 'all') {
            $user = json_decode($project_info->permission);
            foreach ($user as $key => $v_user) {
                $allowed_user[] = $key;
            }
        } else {
            $allowed_user = $this->items_model->allowed_user_id('57');
        }
        if (!empty($allowed_user)) {
            foreach ($allowed_user as $v_user) {
                $login_info = $this->items_model->check_by(array('user_id' => $v_user), 'tbl_users');
                $params['recipient'] = $login_info->email;
                $this->items_model->send_email($params);
            }
        }
    }

    public function delete_comments($task_comment_id)
    {
        $comments_info = $this->items_model->check_by(array('task_comment_id' => $task_comment_id), 'tbl_task_comment');
        if (empty($comments_info)) {
            $type = "error";
            $message = "No Record Found";
            echo json_encode(array("status" => $type, 'message' => $message));
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
            'module' => 'projects',
            'module_field_id' => $comments_info->project_id,
            'activity' => 'activity_comment_deleted',
            'icon' => 'fa-folder-open-o',
            'link' => 'admin/projects/project_details/' . $comments_info->project_id . '/3',
            'value1' => $comments_info->comment,
        );
        // Update into tbl_project
        $this->items_model->_table_name = "tbl_activities"; //table name
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);


        $this->items_model->_table_name = "tbl_task_comment"; // table name
        $this->items_model->_primary_key = "task_comment_id"; // $id
        $this->items_model->delete($task_comment_id);

        //save data into table.
        $this->items_model->_table_name = "tbl_task_comment"; // table name
        $this->items_model->delete_multiple(array('comments_reply_id' => $task_comment_id));

        echo json_encode(array("status" => 'success', 'message' => lang('task_comment_deleted')));
        exit();
    }

    public function save_attachment($task_attachment_id = NULL)
    {
        $data = $this->items_model->array_from_post(array('title', 'description', 'project_id'));
        $data['user_id'] = $this->session->userdata('user_id');

        // save and update into tbl_files
        $this->items_model->_table_name = "tbl_task_attachment"; //table name
        $this->items_model->_primary_key = "task_attachment_id";
        if (!empty($task_attachment_id)) {
            $id = $task_attachment_id;
            $this->items_model->save($data, $id);
            $msg = lang('project_file_updated');
        } else {
            $id = $this->items_model->save($data);
            $msg = lang('project_file_added');
        }
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
                        $this->items_model->_table_name = "tbl_task_uploaded_files"; // table name
                        $this->items_model->_primary_key = "uploaded_files_id"; // $id
                        $uploaded_files_id = $this->items_model->save($up_data);

                        // saved into comments
                        $comment = $this->input->post('comment_' . $file, true);
                        $u_cdata = array(
                            "comment" => $comment,
                            "user_id" => $this->session->userdata('user_id'),
                            "project_id" => $data['project_id'],
                            "uploaded_files_id" => $uploaded_files_id,
                        );
                        $this->items_model->_table_name = "tbl_task_comment"; // table name
                        $this->items_model->_primary_key = "task_comment_id"; // $id
                        $this->items_model->save($u_cdata);
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
                        $this->items_model->_table_name = "tbl_task_uploaded_files"; // table name
                        $this->items_model->_primary_key = "uploaded_files_id"; // $id
                        $uploaded_files_id = $this->items_model->save($up_data);

                        // saved into comments
                        if (!empty($comment[$key])) {
                            $u_cdata = array(
                                "comment" => $comment[$key],
                                "user_id" => $this->session->userdata('user_id'),
                                "uploaded_files_id" => $uploaded_files_id,
                            );
                            $this->items_model->_table_name = "tbl_task_comment"; // table name
                            $this->items_model->_primary_key = "task_comment_id"; // $id
                            $this->items_model->save($u_cdata);
                        }

                    }
                }
            }
        }
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'project',
            'module_field_id' => $id,
            'activity' => 'activity_new_project_attachment',
            'icon' => 'fa-folder-open-o',
            'link' => 'client/projects/project_details/' . $data['project_id'] . '/4',
            'value1' => $data['title'],
        );
        // Update into tbl_project
        $this->items_model->_table_name = "tbl_activities"; //table name
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);

        // send notification message
        $this->notify_attchemnt_project($id);
        // messages for user
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('client/projects/project_details/' . $data['project_id'] . '/' . '4');
    }

    function notify_attchemnt_project($task_attachment_id)
    {
        $email_template = email_templates(array('email_group' => 'project_attachment'));
        $comment_info = $this->items_model->check_by(array('task_attachment_id' => $task_attachment_id), 'tbl_task_attachment');

        $project_info = $this->items_model->check_by(array('project_id' => $comment_info->project_id), 'tbl_project');
        $message = $email_template->template_body;

        $subject = $email_template->subject;
        $projectName = str_replace("{PROJECT_NAME}", $project_info->project_name, $message);
        $assigned_by = str_replace("{UPLOADED_BY}", ucfirst($this->session->userdata('name')), $projectName);
        $Link = str_replace("{PROJECT_URL}", base_url() . 'admin/projects/project_details/' . $comment_info->project_id . '/' . $data['active'] = 4, $assigned_by);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';
        if (!empty($project_info->permission) && $project_info->permission != 'all') {
            $user = json_decode($project_info->permission);
            foreach ($user as $key => $v_user) {
                $allowed_user[] = $key;
            }
        } else {
            $allowed_user = $this->items_model->allowed_user_id('57');
        }
        if (!empty($allowed_user)) {
            foreach ($allowed_user as $v_user) {
                $login_info = $this->items_model->check_by(array('user_id' => $v_user), 'tbl_users');
                $params['recipient'] = $login_info->email;
                $this->items_model->send_email($params);

                if ($v_user != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $v_user,
                        'from_user_id' => true,
                        'description' => 'not_uploaded_attachment',
                        'link' => 'admin/projects/project_details/' . $project_info->project_id . '/4',
                        'value' => lang('project') . ' ' . $project_info->project_name,
                    ));
                }

            }
            show_notification($allowed_user);
        }
    }

    public function delete_files($task_attachment_id)
    {
        $file_info = $this->items_model->check_by(array('task_attachment_id' => $task_attachment_id), 'tbl_task_attachment');
        if (empty($file_info)) {
            $type = "error";
            $message = "No Record Found";
            echo json_encode(array("status" => $type, 'message' => $message));
            exit();
        }
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'projects',
            'module_field_id' => $file_info->project_id,
            'activity' => 'activity_project_attachfile_deleted',
            'icon' => 'fa-folder-open-o',
            'link' => 'admin/projects/project_details/' . $file_info->project_id . '/4',
            'value1' => $file_info->title,
        );
        // Update into tbl_project
        $this->items_model->_table_name = "tbl_activities"; //table name
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);

        //save data into table.
        $this->items_model->_table_name = "tbl_task_attachment"; // table name
        $this->items_model->delete_multiple(array('task_attachment_id' => $task_attachment_id));

        $uploadFileinfo = $this->db->where('task_attachment_id', $task_attachment_id)->get('tbl_task_uploaded_files')->result();
        if (!empty($uploadFileinfo)) {
            foreach ($uploadFileinfo as $Fileinfo) {
                remove_files($Fileinfo->file_name);
            }
        }
        //save data into table.
        $this->items_model->_table_name = "tbl_task_uploaded_files"; // table name
        $this->items_model->delete_multiple(array('task_attachment_id' => $task_attachment_id));

        echo json_encode(array("status" => 'success', 'message' => lang('project_attachment_file_deleted')));
        exit();

    }


    public function download_files($project_id, $uploaded_files_id, $comments = null)
    {

        $this->load->helper('download');
        if (!empty($comments)) {
            if ($project_id) {
                $down_data = file_get_contents('uploads/' . $uploaded_files_id); // Read the file's contents
                force_download($uploaded_files_id, $down_data);
            } else {
                $type = "error";
                $message = 'Operation Fieled !';
                set_message($type, $message);
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $uploaded_files_info = $this->items_model->check_by(array('uploaded_files_id' => $uploaded_files_id), 'tbl_task_uploaded_files');
            if (!empty($uploaded_files_info->uploaded_path)) {
                $data = file_get_contents($uploaded_files_info->uploaded_path); // Read the file's contents
                if (!empty($data)) {
                    force_download($uploaded_files_info->file_name, $data);
                } else {
                    $type = "error";
                    $message = lang('operation_failed');
                    set_message($type, $message);
                    redirect('client/projects/project_details/' . $project_id . '/3');
                }

            } else {
                $type = "error";
                $message = lang('operation_failed');
                set_message($type, $message);
                redirect('client/projects/project_details/' . $project_id . '/3');
            }
        }
    }

    public
    function download_all_files($attachment_id)
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
            redirect('client/projects/project_details/' . $attachment_info->project_id . '/4');
        }
    }

    public function new_attachment($id)
    {
        $data['dropzone'] = true;
        $data['project_info'] = $this->items_model->check_by(array('project_id' => $id), 'tbl_project');
        $data['modal_subview'] = $this->load->view('client/projects/new_attachment', $data, FALSE);
        $this->load->view('client/_layout_modal', $data);
    }

    public function attachment_details($type, $id)
    {
        $data['type'] = $type;
        $data['attachment_info'] = $this->items_model->check_by(array('task_attachment_id' => $id), 'tbl_task_attachment');
        $data['modal_subview'] = $this->load->view('client/projects/attachment_details', $data, FALSE);
        $this->load->view('client/_layout_modal_extra_lg', $data);
    }

    public function save_attachment_comments()
    {
        $task_attachment_id = $this->input->post('task_attachment_id', true);
        if (!empty($task_attachment_id)) {
            $data['task_attachment_id'] = $task_attachment_id;
        } else {
            $data['uploaded_files_id'] = $this->input->post('uploaded_files_id', true);
        }
        $data['project_id'] = $this->input->post('project_id', true);
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
        $this->items_model->_table_name = "tbl_task_comment"; // table name
        $this->items_model->_primary_key = "task_comment_id"; // $id
        $comment_id = $this->items_model->save($data);
        if (!empty($comment_id)) {
            $project_info = $this->items_model->check_by(array('project_id' => $data['project_id']), 'tbl_project');
            $notifiedUsers = array();
            if (!empty($project_info->permission) && $project_info->permission != 'all') {
                $permissionUsers = json_decode($project_info->permission);
                foreach ($permissionUsers as $user => $v_permission) {
                    array_push($notifiedUsers, $user);
                }
            } else {
                $notifiedUsers = $this->items_model->allowed_user_id('57');
            }
            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => 'not_new_comment',
                            'link' => 'admin/projects/project_details/' . $project_info->project_id . '/3',
                            'value' => lang('project') . ' ' . $project_info->project_name,
                        ));
                    }
                }
            }
            show_notification($notifiedUsers);

            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'projects',
                'module_field_id' => $data['project_id'],
                'activity' => 'activity_new_project_comment',
                'icon' => 'fa-folder-open-o',
                'link' => 'admin/projects/project_details/' . $data['project_id'] . '/3',
                'value1' => $data['comment'],
            );
            // Update into tbl_project
            $this->items_model->_table_name = "tbl_activities"; //table name
            $this->items_model->_primary_key = "activities_id";
            $this->items_model->save($activities);

            // send notification
            $this->notify_comments_project($comment_id);
            $response_data = "";
            $view_data['comment_details'] = $this->db->where(array('task_comment_id' => $comment_id))->order_by('comment_datetime', 'DESC')->get('tbl_task_comment')->result();
            $response_data = $this->load->view("client/projects/comments_list", $view_data, true);
            echo json_encode(array("status" => 'success', "data" => $response_data, 'message' => lang('project_comment_save')));
            exit();
//            $type = "success";
//            $message = lang('project_comment_save');
//            set_message($type, $message);
        } else {
            echo json_encode(array("status" => 'error', 'message' => lang('error_occurred')));
            exit();
        }
    }

}
