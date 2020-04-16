<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Goal_Tracking extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('items_model');

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
        $data['title'] = lang('goal_tracking');
        if ($id) {
            $data['active'] = 2;
            $can_edit = $this->items_model->can_action('tbl_goal_tracking', 'edit', array('goal_tracking_id' => $id));
            $edited = can_action('69', 'edited');
            if (!empty($can_edit) && !empty($edited)) {
                $data['goal_info'] = $this->items_model->check_by(array('goal_tracking_id' => $id), 'tbl_goal_tracking');

            }
        } else {
            $data['active'] = 1;
        }
        // get permission user by menu id
        $data['permission_user'] = $this->items_model->all_permission_user('69');

        $data['subview'] = $this->load->view('admin/goal_tracking/manage_goal', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function goal_trackingList($type = null)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_goal_tracking';
            $this->datatables->join_table = array('	tbl_goal_type');
            $this->datatables->join_where = array('tbl_goal_type.goal_type_id=tbl_goal_tracking.goal_type_id');
            $this->datatables->column_order = array('subject', 'tbl_goal_type.type_name', 'achievement', 'start_date', 'end_date', 'tbl_goal_tracking.description');
            $this->datatables->column_search = array('subject', 'tbl_goal_type.type_name', 'achievement', 'start_date', 'end_date', 'tbl_goal_tracking.description');
            $this->datatables->order = array('goal_tracking_id' => 'desc');
            if (!empty($type)) {
                $where = array('tbl_goal_tracking.goal_type_id' => $type);
            } else {
                $where = null;
            }
            $fetch_data = array_reverse($this->datatables->get_goal_tracking($where));

            $edited = can_action('72', 'edited');
            $deleted = can_action('72', 'deleted');
            $data = array();
            foreach ($fetch_data as $_key => $v_goal_tracking) {

                $goal_type_info = $this->db->where('goal_type_id', $v_goal_tracking->goal_type_id)->get('tbl_goal_type')->row();

                $progress = $this->items_model->get_progress($v_goal_tracking);

                $can_edit = $this->items_model->can_action('tbl_goal_tracking', 'edit', array('goal_tracking_id' => $v_goal_tracking->goal_tracking_id));
                $can_delete = $this->items_model->can_action('tbl_goal_tracking', 'delete', array('goal_tracking_id' => $v_goal_tracking->goal_tracking_id));

                $action = null;
                $sub_array = array();
                $name = null;
                $name .= '<a class="text-info" href="' . base_url() . 'admin/goal_tracking/goal_details/' . $v_goal_tracking->goal_tracking_id . '">' . $v_goal_tracking->subject . '</a>';

                if ($progress['progress'] == 100) {
                    $sprogress = 'success';
                } elseif ($progress['progress'] >= 40 && $progress['progress'] <= 50) {
                    $sprogress = 'primary';
                } elseif ($progress['progress'] >= 51 && $progress['progress'] <= 99) {
                    $sprogress = 'purple';
                } else {
                    $sprogress = 'danger';
                }

                $name .= '<div class="progress progress-xs progress-striped active"><div class="progress-bar progress-bar-' . $sprogress . '"data-toggle = "tooltip" data-original-title = "' . $progress['progress'] . '%" style = "width:' . $progress['progress'] . '%" ></div></div>';

                $sub_array[] = $name;
                $sub_array[] = '<span data-toggle="tooltip" data-placement="top" title="' . $goal_type_info->description . '">' . lang($goal_type_info->type_name) . '</span>';
                $sub_array[] = $v_goal_tracking->achievement;
                $sub_array[] = display_date($v_goal_tracking->start_date);
                $sub_array[] = display_date($v_goal_tracking->end_date);

                $action .= btn_view('admin/goal_tracking/goal_details/' . $v_goal_tracking->goal_tracking_id) . ' ';
                if (!empty($can_edit) && !empty($edited)) {
                    $action .= btn_edit('admin/goal_tracking/index/' . $v_goal_tracking->goal_tracking_id) . ' ';
                }
                if (!empty($can_delete) && !empty($deleted)) {
                    $action .= ajax_anchor(base_url("admin/goal_tracking/delete_goal/" . $v_goal_tracking->goal_tracking_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                }
                $sub_array[] = $action;


                $data[] = $sub_array;
            }
            render_table($data, $where);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function save_goal_tracking($id = NULL)
    {
        $created = can_action('69', 'created');
        $edited = can_action('69', 'edited');
        if (!empty($created) || !empty($edited) && !empty($id)) {
            $this->items_model->_table_name = 'tbl_goal_tracking';
            $this->items_model->_primary_key = 'goal_tracking_id';

            $data = $this->items_model->array_from_post(array('subject', 'goal_type_id', 'achievement', 'start_date', 'end_date', 'account_id', 'description', 'notify_goal_achive', 'notify_goal_not_achive'));

            $permission = $this->input->post('permission', true);

            if (!empty($permission)) {
                if ($permission == 'everyone') {
                    $assigned = 'all';
                } else {
                    $assigned_to = $this->items_model->array_from_post(array('assigned_to'));

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
                if (empty($_SERVER['HTTP_REFERER'])) {
                    redirect('admin/goal_tracking');
                } else {
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }

            $return_id = $this->items_model->save($data, $id);
            if (!empty($id)) {
                $id = $id;
                $action = 'activity_update_goal_tracking';
                $msg = lang('update_goal_tracking');
            } else {
                $id = $return_id;
                $action = 'activity_save_goal_tracking';
                $msg = lang('save_goal_tracking');
            }
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'goal_tracking',
                'module_field_id' => $id,
                'activity' => $action,
                'icon' => 'fa-circle-o',
                'value1' => $data['subject']
            );
            $this->items_model->_table_name = 'tbl_activities';
            $this->items_model->_primary_key = 'activities_id';
            $this->items_model->save($activity);

            // messages for user
            $type = "success";
            $message = $msg;
            set_message($type, $message);
        }
        redirect('admin/goal_tracking');

    }

    public
    function goal_details($id, $active = NULL)
    {
        $data['title'] = lang('goal_tracking');
        if ($active == 2) {
            $data['active'] = 2;
        } else {
            $data['active'] = 1;
        }
        $data['task_active'] = 1;

        $data['goal_info'] = $this->items_model->check_by(array('goal_tracking_id' => $id), 'tbl_goal_tracking');
        $data['subview'] = $this->load->view('admin/goal_tracking/goal_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load

    }

    public
    function send_notifier($id, $type)
    {
        if ($type == 'success') {
            $email_template = email_templates(array('email_group' => 'goal_achieve'));
        } else {
            $email_template = email_templates(array('email_group' => 'goal_not_achieve'));
        }

        $goal_info = $this->items_model->check_by(array('goal_tracking_id' => $id), 'tbl_goal_tracking');
        $goal_type_info = $this->db->where('goal_type_id', $goal_info->goal_type_id)->get('tbl_goal_type')->row();
        $progress = $this->items_model->get_progress($goal_info);

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
            $allowed_user = $this->items_model->allowed_user_id('69');
        }

        if (!empty($allowed_user)) {
            foreach ($allowed_user as $v_user) {
                $login_info = $this->items_model->check_by(array('user_id' => $v_user), 'tbl_users');
                $params['recipient'] = $login_info->email;
                $this->items_model->send_email($params);
            }
        }

        $type = "success";
        $message = lang('email_successfully_send');

        set_message($type, $message);
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('admin/goal_tracking');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public
    function save_comments()
    {
        $data['goal_tracking_id'] = $this->input->post('goal_tracking_id', TRUE);
        $data['comment'] = $this->input->post('comment', TRUE);
        $data['user_id'] = $this->session->userdata('user_id');

        //save data into table.
        $this->items_model->_table_name = "tbl_task_comment"; // table name
        $this->items_model->_primary_key = "task_comment_id"; // $id
        $task_comment_id = $this->items_model->save($data);
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'goal_tracking',
            'module_field_id' => $data['goal_tracking_id'],
            'activity' => 'activity_new_task_comment',
            'icon' => 'fa-ticket',
            'value1' => $data['comment'],
        );

        // Update into tbl_project
        $this->items_model->_table_name = "tbl_activities"; //table name
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);

        $goal_info = $this->items_model->check_by(array('goal_tracking_id' => $data['goal_tracking_id']), 'tbl_goal_tracking');
        $comments_info = $this->items_model->check_by(array('task_comment_id' => $task_comment_id), 'tbl_task_comment');

        $notifiedUsers = array($comments_info->user_id);

        if (!empty($notifiedUsers)) {
            foreach ($notifiedUsers as $users) {
                if ($users != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $users,
                        'from_user_id' => true,
                        'description' => 'not_comment_reply',
                        'link' => 'admin/goal_tracking/goal_details/' . $data['goal_tracking_id'] . '/2',
                        'value' => $goal_info->subject,
                    ));
                }
            }
            show_notification($notifiedUsers);
        }

        $type = "success";
        $message = lang('task_comment_save');
        set_message($type, $message);
        redirect('admin/goal_tracking/goal_details/' . $data['goal_tracking_id'] . '/' . $data['active'] = 2);
    }

    public
    function delete_comments($goal_tracking_id, $task_comment_id)
    {
        //save data into table.
        $this->items_model->_table_name = "tbl_task_comment"; // table name
        $this->items_model->_primary_key = "task_comment_id"; // $id
        $this->items_model->delete($task_comment_id);

        $type = "success";
        $message = lang('task_comment_deleted');
        set_message($type, $message);
        redirect('admin/goal_tracking/goal_details/' . $goal_tracking_id . '/' . '2');
    }

    public
    function delete_goal($id)
    {
        $deleted = can_action('69', 'deleted');
        if (!empty($deleted)) {
            $action = 'activity_delete_goal';
            $msg = lang('delete_goal');
            $acc_info = $this->items_model->check_by(array('goal_tracking_id' => $id), 'tbl_goal_tracking');
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'goal_tracking',
                'module_field_id' => $id,
                'activity' => $action,
                'icon' => 'fa-circle-o',
                'value1' => $acc_info->subject
            );
            $this->items_model->_table_name = 'tbl_activities';
            $this->items_model->_primary_key = 'activities_id';
            $this->items_model->save($activity);

            $this->items_model->_table_name = 'tbl_goal_tracking';
            $this->items_model->_primary_key = 'goal_tracking_id';
            $this->items_model->delete($id);

            $type = "success";
            $message = $msg;
            set_message($type, $message);
        }
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('admin/goal_tracking');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public
    function update_users($id)
    {

        // get all assign_user
        $data['title'] = lang('goal_tracking');
        $data['permission_user'] = $this->items_model->all_permission_user('69');
        $data['goal_info'] = $this->items_model->check_by(array('goal_tracking_id' => $id), 'tbl_goal_tracking');
        $data['modal_subview'] = $this->load->view('admin/goal_tracking/_modal_users', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public
    function update_member($id)
    {
        $edited = can_action('69', 'edited');
        if (!empty($edited)) {
            $goal_info = $this->items_model->check_by(array('goal_tracking_id' => $id), 'tbl_goal_tracking');
            $permission = $this->input->post('permission', true);
            if (!empty($permission)) {

                if ($permission == 'everyone') {
                    $assigned = 'all';
                } else {
                    $assigned_to = $this->items_model->array_from_post(array('assigned_to'));
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
                if (empty($_SERVER['HTTP_REFERER'])) {
                    redirect('admin/goal_tracking');
                } else {
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }

//save data into table.
            $this->items_model->_table_name = "tbl_goal_tracking"; // table name
            $this->items_model->_primary_key = "goal_tracking_id"; // $id
            $this->items_model->save($data, $id);

            $action = 'activity_update_goal_tracking';
            $msg = lang('update_goal_tracking');

// save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'goal_tracking',
                'module_field_id' => $id,
                'activity' => $action,
                'icon' => 'fa-circle-o',
                'value1' => $goal_info->subject
            );
// Update into tbl_project
            $this->items_model->_table_name = "tbl_activities"; //table name
            $this->items_model->_primary_key = "activities_id";
            $this->items_model->save($activities);
            if (!empty($goal_info->permission) && $goal_info->permission != 'all') {
                $user = json_decode($goal_info->permission);
                foreach ($user as $key => $v_user) {
                    $allowed_user[] = $key;
                }
            } else {
                $allowed_user = $this->items_model->allowed_user_id('69');
            }
            if (!empty($allowed_user)) {
                foreach ($allowed_user as $v_user) {
                    if ($v_user != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $v_user,
                            'from_user_id' => true,
                            'description' => 'not_goal_assign_to_you',
                            'link' => 'admin/goal_tracking/goal_details/' . $data['goal_tracking_id'],
                            'value' => $goal_info->subject,
                        ));
                    }
                }
                show_notification($allowed_user);
            }


            $type = "success";
            $message = $msg;
            set_message($type, $message);
        }
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('admin/goal_tracking');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }

    }

}
