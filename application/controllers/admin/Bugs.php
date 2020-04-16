<?php

/**
 * Description of bugs
 *
 * @author Nayeem
 */
class Bugs extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('bugs_model');
    }

    public function index($id = NULL, $opt_id = NULL)
    {
        $data['title'] = lang('all_bugs');
        // get permission user by menu id
        $data['assign_user'] = $this->bugs_model->allowed_user('58');
        $data['all_bugs_info'] = $this->bugs_model->get_permission('tbl_bug');
        if ($id) { // retrive data from db by id
            $data['active'] = 2;
            $can_edit = $this->bugs_model->can_action('tbl_bug', 'edit', array('bug_id' => $id));
            $edited = can_action('58', 'edited');
            if ($id == 'project') {
                $data['project_id'] = $opt_id;
                $project_info = get_row('tbl_project', array('project_id' => $opt_id));
                if ($project_info->permission == 'all') {
                    $data['assign_user'] = $this->bugs_model->allowed_user('57');
                } else {
                    $data['assign_user'] = $this->bugs_model->permitted_allowed_user($project_info->permission);
                }
            } elseif ($id == 'opportunities') {
                $data['opportunities_id'] = $opt_id;
                $option_info = get_row('tbl_opportunities', array('opportunities_id' => $opt_id));
                if ($option_info->permission == 'all') {
                    $data['assign_user'] = $this->bugs_model->allowed_user('56');
                } else {
                    $data['assign_user'] = $this->bugs_model->permitted_allowed_user($option_info->permission);
                }
            } else {
                if (!empty($can_edit) && !empty($edited)) {
                    if (is_numeric($id)) {
                        // get all bug information
                        $data['bug_info'] = $this->db->where('bug_id', $id)->get('tbl_bug')->row();
                    }
                }
            }
            $data['all_opportunities_info'] = $this->bugs_model->get_permission('tbl_opportunities');
        } else {
            $data['active'] = 1;
        }
        $data['editor'] = $this->data;
        $data['subview'] = $this->load->view('admin/bugs/bugs', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function bugsList($filterBy = null, $search_by = null)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_bug';
            $this->datatables->join_table = array('tbl_account_details');
            $this->datatables->join_where = array('tbl_account_details.user_id=tbl_bug.reporter');

            $custom_field = custom_form_table_search(6);
            $main_column = array('bug_title', 'tbl_account_details.fullname', 'bug_status', 'severity', 'reporter', 'permission');
            $action_array = array('bug_id');
            $result = array_merge($main_column, $custom_field, $action_array);
            $this->datatables->column_order = $result;
            $this->datatables->column_search = $result;
            $this->datatables->order = array('bug_id' => 'desc');
            $where = array();
            if (!empty($search_by)) {
                if ($search_by == 'by_project') {
                    $where = array('project_id' => $filterBy);
                }
                if ($search_by == 'by_opportunity') {
                    $where = array('opportunities_id' => $filterBy);
                }
                if ($search_by == 'from_reporter') {
                    $where = array('reporter' => $filterBy);
                }
                if ($search_by == 'by_staff') {
                    if ($filterBy == 'everyone') {
                        $where = array('permission' => 'all');
                    } else {
                        $where = $filterBy;
                    }
                }
            } else {
                if ($filterBy == 'assigned_to_me') {
                    $user_id = $this->session->userdata('user_id');
                    $where = $user_id;
                } else if (!empty($filterBy)) {
                    $where = array('bug_status' => $filterBy);
                }
            }

            $fetch_data = $this->datatables->get_bugs($filterBy, $search_by);
            $data = array();

            $edited = can_action('58', 'edited');
            $deleted = can_action('58', 'deleted');
            foreach ($fetch_data as $_key => $v_bugs) {
                if (!empty($v_bugs)) {
                    $action = null;
                    $can_edit = $this->bugs_model->can_action('tbl_bug', 'edit', array('bug_id' => $v_bugs->bug_id));
                    $can_delete = $this->bugs_model->can_action('tbl_bug', 'delete', array('bug_id' => $v_bugs->bug_id));

                    $sub_array = array();

                    $name = null;
                    $name .= '<a class="text-info" href="' . base_url() . 'admin/bugs/view_bug_details/' . $v_bugs->bug_id . '">' . $v_bugs->bug_title . '</a>';

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
                    $change_status = null;
                    $ch_url = base_url() . 'admin/bugs/change_status/';
                    $tasks_status = array_reverse($this->bugs_model->get_statuses());
                    $change_status .= '<div class="btn-group">
        <button class="btn btn-xs btn-default dropdown-toggle"
                data-toggle="dropdown">
                    ' . lang('change') . '
            <span class="caret"></span></button>
        <ul class="dropdown-menu animated zoomIn">';
                    foreach ($tasks_status as $v_status) {
                        $change_status .= '<li><a href="' . $ch_url . $v_bugs->bug_id . '/' . ($v_status['value']) . '">' . lang($v_status['value']) . '</a></li>';
                    }
                    $change_status .= '</ul></div>';

                    $sub_array[] = (!empty($v_bugs->created_time) ? display_datetime($v_bugs->created_time) : '-');
                    $sub_array[] = '<span class="label label-' . $label . '">' . lang($v_bugs->bug_status) . '</span>' . ' ' . $change_status;
                    if ($v_bugs->priority == 'High') {
                        $plabel = 'danger';
                    } elseif ($v_bugs->priority == 'Medium') {
                        $plabel = 'info';
                    } else {
                        $plabel = 'primary';
                    }

                    $sub_array[] = '<span class="label label-' . $plabel . '">' . lang($v_bugs->severity) . '</span>';

                    if ($this->session->userdata('user_type') == '1') {
                        $sub_array[] = fullname($v_bugs->reporter);
                    }
                    $assigned = null;
                    if ($v_bugs->permission != 'all') {
                        $get_permission = json_decode($v_bugs->permission);
                        if (!empty($get_permission)) :
                            foreach ($get_permission as $permission => $v_permission) :
                                $user_info = $this->db->where(array('user_id' => $permission))->get('tbl_users')->row();
                                if (!empty($user_info)) {
                                    if ($user_info->role_id == 1) {
                                        $label = 'circle-danger';
                                    } else {
                                        $label = 'circle-success';
                                    }
                                    $assigned .= '<a href="#" data-toggle="tooltip"
                                                               data-placement="top"
                                                               title="' . fullname($permission) . '"><img
                                                                    src="' . base_url() . staffImage($permission) . '"
                                                                    class="img-circle img-xs" alt="">
                                                <span style="margin: 0px 0 8px -10px;"
                                                      class="circle ' . $label . '  circle-lg"></span>
                                                            </a>';
                                }
                            endforeach;
                        endif;
                    } else {
                        $assigned .= '<strong>' . lang("everyone") . '</strong><i title="' . lang('permission_for_all') . '" class="fa fa-question-circle" data-toggle="tooltip" data-placement="top"></i>';
                    };
                    if (!empty($can_edit) && !empty($edited)) {
                        $assigned .= '<span data-placement="top" data-toggle="tooltip" title="' . lang('add_more') . '"><a data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/bugs/update_users/' . $v_bugs->bug_id . '" class="text-default ml"><i class="fa fa-plus"></i></a></span>';
                    };

                    $sub_array[] = $assigned;

                    $custom_form_table = custom_form_table(6, $v_bugs->bug_id);

                    if (!empty($custom_form_table)) {
                        foreach ($custom_form_table as $c_label => $v_fields) {
                            $sub_array[] = $v_fields;
                        }
                    }
                    if (!empty($can_edit) && !empty($edited)) {
                        $action .= btn_edit('admin/bugs/index/' . $v_bugs->bug_id) . ' ';
                    }
                    if (!empty($can_delete) && !empty($deleted)) {
                        $action .= ajax_anchor(base_url("admin/bugs/delete_bug/$v_bugs->bug_id"), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                    }
                    $sub_array[] = $action;
                    $data[] = $sub_array;
                }
            }
            render_table($data, $where);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function save_bug($id = NULL)
    {
        $created = can_action('58', 'created');
        $edited = can_action('58', 'edited');
        if (!empty($created) || !empty($edited) && !empty($id)) {
            $data = $this->bugs_model->array_from_post(array(
                'issue_no',
                'bug_title',
                'bug_description',
                'reproducibility',
                'priority',
                'severity',
                'reporter',
                'client_visible',
                'bug_status'));

            if (empty($id)) {
                $data['created_time'] = date("Y-m-d H:i:s");
            }
            $result = 0;
            $project_id = $this->input->post('project_id', TRUE);

            if (!empty($project_id)) {
                $data['project_id'] = $project_id;
            } else {
                $data['project_id'] = NULL;
                $result += count(1);
            }
            $opportunities_id = $this->input->post('opportunities_id', TRUE);
            if (!empty($opportunities_id)) {
                $data['opportunities_id'] = $opportunities_id;
            } else {
                $data['opportunities_id'] = NULL;
                $result += count(1);
            }
            if ($result == 2) {
                if (!empty($id)) {
                    $bugs_info = $this->db->where('bug_id', $id)->get('tbl_bug')->row();
                    $data['project_id'] = $bugs_info->project_id;
                    $data['opportunities_id'] = $bugs_info->opportunities_id;
                } else {
                    $data['project_id'] = $this->input->post('un_project_id', TRUE);
                    $data['opportunities_id'] = $this->input->post('un_opportunities_id', TRUE);
                }
            }

            $permission = $this->input->post('permission', true);
            if (!empty($permission)) {

                if ($permission == 'everyone') {
                    $assigned = 'all';
                    $assigned_to['assigned_to'] = $this->bugs_model->allowed_user_id('58');
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
                if (empty($_SERVER['HTTP_REFERER'])) {
                    redirect('admin/bugs');
                } else {
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }


            //save data into table.
            $this->bugs_model->_table_name = "tbl_bug"; // table name
            $this->bugs_model->_primary_key = "bug_id"; // $id
            if (!empty($id)) {
                $can_edit = $this->bugs_model->can_action('tbl_bug', 'edit', array('bug_id' => $id));
                if (!empty($can_edit)) {
                    $return_id = $this->bugs_model->save($data, $id);
                } else {
                    set_message('error', lang('there_in_no_value'));
                    redirect('admin/projects');
                }
            } else {
                $return_id = $this->bugs_model->save($data);
            }

            if ($assigned == 'all') {
                $assigned_to['assigned_to'] = $this->bugs_model->allowed_user_id('58');
            }
            if (!empty($id)) {
                $msg = lang('update_bug');
                $activity = 'activity_update_bug';
                $id = $id;
                if (!empty($assigned_to['assigned_to'])) {
                    // send update
                    $this->notify_update_bugs($assigned_to['assigned_to'], $id, TRUE);
                }
            } else {
                $id = $return_id;
                $msg = lang('save_bug');
                $activity = 'activity_new_bug';
                if (!empty($assigned_to['assigned_to'])) {
                    $this->notify_bugs($assigned_to['assigned_to'], $id);
                    $this->notify_bugs_reported($id);
                }
            }
            save_custom_field(6, $id);
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'bugs',
                'module_field_id' => $id,
                'activity' => $activity,
                'icon' => 'fa-bug',
                'link' => 'admin/bugs/view_bug_details/' . $id,
                'value1' => $data['bug_title'],
            );
            // Update into tbl_project
            $this->bugs_model->_table_name = "tbl_activities"; //table name
            $this->bugs_model->_primary_key = "activities_id";
            $this->bugs_model->save($activities);

            $type = "success";
            $message = $msg;
            set_message($type, $message);
        }
        if (!empty($data['project_id']) && is_numeric($data['project_id'])) {
            redirect('admin/projects/project_details/' . $data['project_id']);
        } elseif (!empty($opportunities_id) && is_numeric($opportunities_id)) {
            redirect('admin/opportunities/opportunity_details/' . $opportunities_id);
        } else if (!empty($id)) {
            redirect('admin/bugs/view_bug_details/' . $id);
        } else {
            redirect('admin/bugs');
        }

    }

    function notify_update_bugs($users, $bug_id)
    {
        $email_template = email_templates(array('email_group' => 'bug_updated'));

        $bugs_info = $this->bugs_model->check_by(array('bug_id' => $bug_id), 'tbl_bug');
        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $bug_title = str_replace("{BUG_TITLE}", $bugs_info->bug_title, $message);
        $bug_status = str_replace("{STATUS}", lang($bugs_info->bug_status), $bug_title);

        $assigned_by = str_replace("{MARKED_BY}", ucfirst($this->session->userdata('name')), $bug_status);
        $Link = str_replace("{BUG_URL}", base_url() . 'admin/bugs/view_bug_details/' . $bugs_info->bug_id, $assigned_by);
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

            if ($v_user != $this->session->userdata('user_id')) {
                add_notification(array(
                    'to_user_id' => $v_user,
                    'from_user_id' => true,
                    'description' => 'assign_to_you_the_bugs',
                    'link' => 'admin/bugs/view_bug_details/' . $bug_id,
                    'value' => $bugs_info->bug_title,
                ));
            }
        }
        show_notification($users);
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

        $login_info = $this->bugs_model->check_by(array('user_id' => $bugs_info->reporter), 'tbl_users');
        $params['recipient'] = $login_info->email;
        $this->bugs_model->send_email($params);
    }

    function notify_bugs($users, $bug_id, $update = NULL)
    {
        if (!empty($update)) {
            $email_template = email_templates(array('email_group' => 'bugs_updated'));
            $description = 'not_bug_update';
        } else {
            $email_template = email_templates(array('email_group' => 'bug_assigned'));
            $description = 'assign_to_you_the_bugs';
        }
        $bugs_info = $this->bugs_model->check_by(array('bug_id' => $bug_id), 'tbl_bug');
        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $bug_title = str_replace("{BUG_TITLE}", $bugs_info->bug_title, $message);

        $assigned_by = str_replace("{ASSIGNED_BY}", ucfirst($this->session->userdata('name')), $bug_title);
        $Link = str_replace("{BUG_URL}", base_url() . 'admin/bugs/view_bug_details/' . $bugs_info->bug_id, $assigned_by);
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

            if ($v_user != $this->session->userdata('user_id')) {
                add_notification(array(
                    'to_user_id' => $v_user,
                    'from_user_id' => true,
                    'description' => $description,
                    'link' => 'admin/bugs/view_bug_details/' . $bug_id,
                    'value' => $bugs_info->bug_title,
                ));
            }
        }
        show_notification($users);
    }

    public function update_users($id)
    {
        // get all assign_user
        $can_edit = $this->bugs_model->can_action('tbl_bug', 'edit', array('bug_id' => $id));
        $edited = can_action('58', 'edited');
        if (!empty($can_edit) && !empty($edited)) {
            $data['assign_user'] = $this->bugs_model->allowed_user('58');

            $data['bugs_info'] = $this->bugs_model->check_by(array('bug_id' => $id), 'tbl_bug');
            $data['modal_subview'] = $this->load->view('admin/bugs/_modal_users', $data, FALSE);
            $this->load->view('admin/_layout_modal', $data);
        } else {
            set_message('error', lang('there_in_no_value'));
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/bugs');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public function update_member($id)
    {
        $can_edit = $this->bugs_model->can_action('tbl_bug', 'edit', array('bug_id' => $id));
        $edited = can_action('58', 'edited');
        if (!empty($can_edit) && !empty($edited)) {
            $bugs_info = $this->bugs_model->check_by(array('bug_id' => $id), 'tbl_bug');

            $permission = $this->input->post('permission', true);
            if (!empty($permission)) {
                if ($permission == 'everyone') {
                    $assigned = 'all';
                    $assigned_to['assigned_to'] = $this->bugs_model->allowed_user_id('58');
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
                if (empty($_SERVER['HTTP_REFERER'])) {
                    redirect('admin/bugs');
                } else {
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }

            //save data into table.
            $this->bugs_model->_table_name = "tbl_bug"; // table name
            $this->bugs_model->_primary_key = "bug_id"; // $id
            $this->bugs_model->save($data, $id);
            if ($assigned == 'all') {
                $assigned_to['assigned_to'] = $this->bugs_model->allowed_user_id('58');
            }

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
                'icon' => 'fa-bug',
                'link' => 'admin/bugs/view_bug_details/' . $id,
                'value1' => $bugs_info->bug_title,
            );
            // Update into tbl_project
            $this->bugs_model->_table_name = "tbl_activities"; //table name
            $this->bugs_model->_primary_key = "activities_id";
            $this->bugs_model->save($activities);

            $type = "success";
            $message = $msg;
            set_message($type, $message);
        } else {
            set_message('error', lang('there_in_no_value'));

        }
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('admin/bugs');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }

    }

    public function change_status($id, $status)
    {
        $can_edit = $this->bugs_model->can_action('tbl_bug', 'edit', array('bug_id' => $id));
        $edited = can_action('58', 'edited');
        if (!empty($can_edit) && !empty($edited)) {

            $bugs_info = $this->bugs_model->check_by(array('bug_id' => $id), 'tbl_bug');
            if (!empty($bugs_info->permission) && $bugs_info->permission != 'all') {
                $user = json_decode($bugs_info->permission);
                foreach ($user as $key => $v_user) {
                    $allowed_user[] = $key;
                }
            } else {
                $allowed_user = $this->bugs_model->allowed_user_id('58');
            }

            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => 'not_changed_status',
                            'link' => 'admin/bugs/view_bug_details/' . $id,
                            'value' => lang('status') . ' : ' . $bugs_info->bug_status . ' to ' . $status,
                        ));
                    }
                }
                show_notification($notifiedUsers);
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
                'icon' => 'fa-bug',
                'link' => 'admin/bugs/view_bug_details/' . $id,
                'value1' => lang($data['bug_status']),
            );
// Update into tbl_project
            $this->bugs_model->_table_name = "tbl_activities"; //table name
            $this->bugs_model->_primary_key = "activities_id";
            $this->bugs_model->save($activities);

            $type = "success";
            $message = lang('update_bug');
            set_message($type, $message);
        } else {
            set_message('error', lang('there_in_no_value'));

        }
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('admin/bugs');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }

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
            'icon' => 'fa-bug',
            'link' => 'admin/bugs/view_bug_details/' . $id . '/4',
            'value1' => $data['notes'],
        );
        // Update into tbl_project
        $this->bugs_model->_table_name = "tbl_activities"; //table name
        $this->bugs_model->_primary_key = "activities_id";
        $this->bugs_model->save($activities);

        $type = "success";
        $message = lang('update_bug');
        set_message($type, $message);
        redirect('admin/bugs/view_bug_details/' . $id . '/4');
    }

    public function save_comments()
    {

        $data['bug_id'] = $this->input->post('bug_id', TRUE);
        $data['comment'] = $this->input->post('comment', TRUE);

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
                'link' => 'admin/bugs/view_bug_details/' . $data['bug_id'] . '/2',
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
            $response_data = $this->load->view("admin/bugs/comments_list", $view_data, true);
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
                'link' => 'admin/bugs/view_bug_details/' . $data['bug_id'] . '/2',
                'value1' => $this->db->where('task_comment_id', $task_comment_id)->get('tbl_task_comment')->row()->comment,
                'value2' => $data['comment'],
            );
            // Update into tbl_project
            $this->bugs_model->_table_name = "tbl_activities"; //table name
            $this->bugs_model->_primary_key = "activities_id";
            $this->bugs_model->save($activities);

            $bugs_info = $this->bugs_model->check_by(array('bug_id' => $data['bug_id']), 'tbl_bug');
            $comments_info = $this->bugs_model->check_by(array('task_comment_id' => $task_comment_id), 'tbl_task_comment');
            $user = $this->bugs_model->check_by(array('user_id' => $comments_info->user_id), 'tbl_users');
            if ($user->role_id == 2) {
                $url = 'client/';
            } else {
                $url = 'admin/';
            }
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
            $response_data = $this->load->view("admin/bugs/comments_reply", $view_data, true);
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

        $bugs_info = $this->bugs_model->check_by(array('bug_id' => $bugs_comment_info->bug_id), 'tbl_bug');
        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $bug_name = str_replace("{BUG_TITLE}", $bugs_info->bug_title, $message);
        $assigned_by = str_replace("{POSTED_BY}", ucfirst($this->session->userdata('name')), $bug_name);
        $Link = str_replace("{COMMENT_URL}", base_url() . 'admin/bugs/view_bug_details/' . $bugs_info->bug_id . '/' . $data['active'] = 2, $assigned_by);
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
            'link' => 'admin/bugs/view_bug_details/' . $comments_info->bug_id . '/2',
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

    public function new_attachment($id)
    {
        $data['dropzone'] = true;
        $data['bugs_details'] = $this->bugs_model->check_by(array('bug_id' => $id), 'tbl_bug');
        $data['modal_subview'] = $this->load->view('admin/bugs/new_attachment', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function attachment_details($type, $id)
    {
        $data['type'] = $type;
        $data['attachment_info'] = $this->bugs_model->check_by(array('task_attachment_id' => $id), 'tbl_task_attachment');
        $data['modal_subview'] = $this->load->view('admin/bugs/attachment_details', $data, FALSE);
        $this->load->view('admin/_layout_modal_extra_lg', $data);
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
        redirect('admin/bugs/view_bug_details/' . $data['bug_id'] . '/' . '3');
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
                'link' => 'admin/bugs/view_bug_details/' . $data['bug_id'] . '/2',
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
            $response_data = $this->load->view("admin/bugs/comments_list", $view_data, true);
            echo json_encode(array("status" => 'success', "data" => $response_data, 'message' => lang('bug_comment_save')));
            exit();
        } else {
            echo json_encode(array("status" => 'error', 'message' => lang('error_occurred')));
            exit();
        }

    }

    public function download_files($uploaded_files_id, $comments = null)
    {
        $this->load->helper('download');
        if (!empty($comments)) {
            if ($uploaded_files_id) {
                $down_data = file_get_contents('uploads/' . $uploaded_files_id); // Read the file's contents
                force_download($uploaded_files_id, $down_data);
            } else {
                echo json_encode(array("status" => 'error', 'message' => lang('error_occurred')));
                exit();
            }
        } else {
            $uploaded_files_info = $this->bugs_model->check_by(array('uploaded_files_id' => $uploaded_files_id), 'tbl_task_uploaded_files');
            if ($uploaded_files_info->uploaded_path) {
                $data = file_get_contents($uploaded_files_info->uploaded_path); // Read the file's contents
                force_download($uploaded_files_info->file_name, $data);
            } else {
                echo json_encode(array("status" => 'error', 'message' => lang('error_occurred')));
                exit();
            }
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
            redirect('admin/bugs/view_bug_details/' . $attachment_info->bug_id . '/3');
        }
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
                $val == TRUE || redirect('admin/bugs/view_bug_details/3/' . $data['bug_id']);
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
            'icon' => 'fa-bug',
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
        redirect('admin/bugs/view_bug_details/' . $data['bug_id'] . '/3');
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
        $Link = str_replace("{BUG_URL}", base_url() . 'admin/bugs/view_bug_details/' . $bugs_info->bug_id . '/' . $data['active'] = 3, $assigned_by);
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

                if ($v_user != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $v_user,
                        'from_user_id' => true,
                        'description' => 'not_uploaded_attachment',
                        'link' => 'admin/bugs/view_bug_details/' . $bugs_info->bug_id . '/2',
                        'value' => lang('bug') . ' : ' . $bugs_info->bug_title,
                    ));
                }
            }
            show_notification($allowed_user);
        }
    }

    public function view_bug_details($id, $active = NULL, $edit = NULL)
    {
        $data['title'] = lang('bug_details');
        $data['page_header'] = lang('bug_management');

        //get all bug information
        $data['bug_details'] = $this->bugs_model->check_by(array('bug_id' => $id), 'tbl_bug');
        if (empty($data['bug_details'])) {
            $type = "error";
            $message = "No Record Found";
            set_message($type, $message);
            redirect('admin/bugs');
        }

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

        $data['subview'] = $this->load->view('admin/bugs/view_bugs', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }


    public function delete_bug($id)
    {
        $can_delete = $this->bugs_model->can_action('tbl_bug', 'delete', array('bug_id' => $id));
        $deleted = can_action('58', 'deleted');
        if (!empty($can_delete) && !empty($deleted)) {
            $bug_info = $this->bugs_model->check_by(array('bug_id' => $id), 'tbl_bug');
            if (empty($bug_info)) {
                $type = "error";
                $message = "No Record Found";
                echo json_encode(array("status" => $type, 'message' => $message));
                exit();
            }

// save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'bugs',
                'module_field_id' => $bug_info->bug_id,
                'activity' => 'activity_bug_deleted',
                'icon' => 'fa-bug',
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
                $uploadFileinfo = $this->db->where('task_attachment_id', $v_files->task_attachment_id)->get('tbl_task_uploaded_files')->result();
                if (!empty($uploadFileinfo)) {
                    foreach ($uploadFileinfo as $Fileinfo) {
                        remove_files($Fileinfo->file_name);
                    }
                }
                $this->bugs_model->_table_name = "tbl_task_uploaded_files"; //table name
                $this->bugs_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
            }
            //delete into table.
            $this->bugs_model->_table_name = "tbl_task_attachment"; // table name
            $this->bugs_model->delete_multiple(array('bug_id' => $id));

            // deleted comments with file
            $all_comments_info = $this->db->where(array('bug_id' => $id))->get('tbl_task_comment')->result();
            if (!empty($all_comments_info)) {
                foreach ($all_comments_info as $comments_info) {
                    if (!empty($comments_info->comments_attachment)) {
                        $attachment = json_decode($comments_info->comments_attachment);
                        foreach ($attachment as $v_file) {
                            remove_files($v_file->fileName);
                        }
                    }
                }
            }
            //delete data into table.
            $this->bugs_model->_table_name = "tbl_task_comment"; // table name
            $this->bugs_model->delete_multiple(array('bug_id' => $id));

            $pin_info = $this->bugs_model->check_by(array('module_name' => 'bugs', 'module_id' => $id), 'tbl_pinaction');
            if (!empty($pin_info)) {
                $this->bugs_model->_table_name = 'tbl_pinaction';
                $this->bugs_model->delete_multiple(array('module_name' => 'bugs', 'module_id' => $id));
            }
            $this->bugs_model->_table_name = "tbl_bug"; // table name
            $this->bugs_model->_primary_key = "bug_id"; // $id
            $this->bugs_model->delete($id);

            $type = "success";
            $message = lang('bug_deleted');
        } else {
            $type = "error";
            $message = lang('there_in_no_value');
        }
        echo json_encode(array("status" => $type, 'message' => $message));
        exit();
    }


}
