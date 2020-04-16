<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class projects extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('items_model');
        $this->load->model('invoice_model');
        $this->load->model('estimates_model');
        $this->load->model('credit_note_model');
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

        $edited = can_action('57', 'edited');
        $data['title'] = lang('all_project');
        // get permission user by menu id
        $data['assign_user'] = $this->items_model->allowed_user('57');
        if (!empty($id)) {
            if (is_numeric($id)) {
                $data['active'] = 2;
                $can_edit = $this->items_model->can_action('tbl_project', 'edit', array('project_id' => $id));
                if (!empty($can_edit) && !empty($edited)) {
                    $data['project_info'] = $this->items_model->check_by(array('project_id' => $id), 'tbl_project');
                }
                $data['tab'] = 'projects';
            } else {
                if ($id == 'client_project') {
                    $data['active'] = 2;
                } else {
                    $data['active'] = $this->uri->segment(4);
                }
                $data['tab'] = $id;
            }
        } else {
            $data['active'] = 1;
            $data['tab'] = 'projects';
        }
//        $data['all_project_info'] = $this->items_model->get_all_project($this->uri->segment(4));
        $data['subview'] = $this->load->view('admin/projects/all_project', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function projectList($filterBy = null, $search_by = null)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_project';
            $this->datatables->join_table = array('tbl_client');
            $this->datatables->join_where = array('tbl_project.client_id=tbl_client.client_id');
            $custom_field = custom_form_table_search(4);
            $main_column = array('tbl_project.project_name', 'tbl_client.name', 'tbl_project.end_date', 'tbl_project.permission', 'tbl_project.project_status');
            $action_array = array('tbl_project.project_id');
            $result = array_merge($main_column, $custom_field, $action_array);
            $this->datatables->column_order = $result;
            $this->datatables->column_search = $result;
            $this->datatables->order = array('tbl_project.project_id' => 'desc');
            $where = array();
            if (empty($filterBy)) {
                $where = array('project_status !=' => 'completed');
            }
            if (!empty($search_by)) {
                if ($search_by == 'by_client') {
                    $where = array('tbl_project.client_id' => $filterBy, 'project_status !=' => 'completed');
                }
                if ($search_by == 'by_staff') {
                    if ($filterBy == 'everyone') {
                        $where = array('permission' => 'all');
                    } else {
                        $where = $filterBy;
                    }
                }
            } else {
                if ($filterBy == 'overdue') {
                    $where = array('UNIX_TIMESTAMP(end_date) <' => strtotime(date('Y-m-d')), 'project_status !=' => 'completed');
                } elseif (!empty($filterBy)) {
                    $where = array('project_status' => $filterBy);
                }
            }
            $fetch_data = $this->datatables->get_all_project($filterBy, $search_by);

            $data = array();
            $edited = can_action('57', 'edited');
            $deleted = can_action('57', 'deleted');
            foreach ($fetch_data as $key => $v_project) {
                if (!empty($v_project)) {
                    $action = null;
                    $progress = $this->items_model->get_project_progress($v_project->project_id);

                    $can_edit = $this->items_model->can_action('tbl_project', 'edit', array('project_id' => $v_project->project_id));
                    $can_delete = $this->items_model->can_action('tbl_project', 'delete', array('project_id' => $v_project->project_id));

                    $sub_array = array();
                    if (!empty($deleted) || !empty($can_delete)) {
                        $sub_array[] = '<div class="checkbox c-checkbox" ><label class="needsclick"> <input value="' . $v_project->project_id . '" type="checkbox"><span class="fa fa-check"></span></label></div>';
                    }
                    $name = null;
                    $name .= '<a class="text-info" href="' . base_url() . 'admin/projects/project_details/' . $v_project->project_id . '">' . $v_project->project_name . '</a>';
                    if (strtotime(date('Y-m-d')) > strtotime($v_project->end_date) && $progress < 100) {
                        $name .= '<span class="label label-danger pull-right">' . lang("overdue") . '</span>';
                    }
                    $name .= '<div class="progress progress-xs progress-striped active"><div class="progress-bar progress-bar-' . (($progress <= 100) ? "success" : "primary") . '"data-toggle = "tooltip" data-original-title = "' . $progress . '%" style = "width:' . $progress . '%" ></div></div>';
                    $sub_array[] = $name;
                    $sub_array[] = client_name($v_project->client_id);
                    $sub_array[] = strftime(config_item('date_format'), strtotime($v_project->end_date));
                    $assigned = null;
                    if ($v_project->permission != 'all') {
                        $get_permission = json_decode($v_project->permission);
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
                        $assigned .= '<span data-placement="top" data-toggle="tooltip" title="' . lang('add_more') . '"><a data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/projects/update_users/' . $v_project->project_id . '" class="text-default ml"><i class="fa fa-plus"></i></a></span>';
                    };

                    $sub_array[] = $assigned;
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
                    $change_status = null;
                    if (!empty($can_edit) && !empty($edited)) {
                        $ch_url = base_url() . 'admin/projects/change_status/';
                        $change_status = '<div class="btn-group">
        <button class="btn btn-xs btn-default dropdown-toggle"
                data-toggle="dropdown">
            ' . lang('change_status') . '
            <span class="caret"></span></button>
        <ul class="dropdown-menu animated zoomIn">
            <li>
                <a href="' . $ch_url . $v_project->project_id . '/started' . '">' . lang('started') . '</a>
                </li>
            <li>
                <a href="' . $ch_url . $v_project->project_id . '/in_progress' . '">' . lang('in_progress') . '</a>
                </li>
            <li>
                <a href="' . $ch_url . $v_project->project_id . '/cancel' . '">' . lang('cancel') . '</a>
                </li>
            <li>
                <a href="' . $ch_url . $v_project->project_id . '/on_hold' . '">' . lang('on_hold') . '</a>
                </li>
            <li>
                <a href="' . $ch_url . $v_project->project_id . '/completed' . '">' . lang('completed') . '</a>
            </li>
        </ul>
    </div>';
                    }
                    $sub_array[] = $statusss . ' ' . $change_status;
                    $custom_form_table = custom_form_table(4, $v_project->project_id);

                    if (!empty($custom_form_table)) {
                        foreach ($custom_form_table as $c_label => $v_fields) {
                            $sub_array[] = $v_fields;
                        }
                    }
                    $action .= btn_view('admin/projects/project_details/' . $v_project->project_id) . ' ';

                    if (!empty($can_edit) && !empty($edited)) {
                        $action .= '<a data-toggle="modal" data-target="#myModal"
       title="' . lang('clone_project') . '"
       href="' . base_url() . 'admin/projects/clone_project/' . $v_project->project_id . '"
       class="btn btn-xs btn-purple"><i class="fa fa-copy"></i></a>' . ' ';
                        $action .= btn_edit('admin/projects/index/' . $v_project->project_id) . ' ';
                    }
                    if (!empty($can_delete) && !empty($deleted)) {
                        $action .= ajax_anchor(base_url("admin/projects/delete_project/$v_project->project_id"), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $key)) . ' ';
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

    public function import()
    {
        $data['title'] = lang('import') . ' ' . lang('project');
        $data['assign_user'] = $this->items_model->allowed_user('57');
        $data['subview'] = $this->load->view('admin/projects/import_project', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function save_imported()
    {
        $created = can_action('57', 'created');
        if (!empty($created)) {
            //load the excel library
            $this->load->library('excel');
            ob_start();
            $file = $_FILES["upload_file"]["tmp_name"];
            if (!empty($file)) {
                $valid = false;
                $types = array('Excel2007', 'Excel5', 'CSV');
                foreach ($types as $type) {
                    $reader = PHPExcel_IOFactory::createReader($type);
                    if ($reader->canRead($file)) {
                        $valid = true;
                    }
                }
                if (!empty($valid)) {
                    try {
                        $objPHPExcel = PHPExcel_IOFactory::load($file);
                    } catch (Exception $e) {
                        die("Error loading file :" . $e->getMessage());
                    }
                    //All data from excel
                    $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

                    for ($x = 2; $x <= count($sheetData); $x++) {
                        // **********************
                        // Save Into tasks table
                        // **********************
                        $projects = '';
                        $data = $this->items_model->array_from_post(array('client_id'));
                        if (empty(config_item('projects_number_format'))) {
                            $projects .= config_item('projects_prefix');
                        }
                        $projects .= $this->items_model->generate_projects_number();

                        $data['project_no'] = $projects;
                        $data['project_name'] = trim($sheetData[$x]["A"]);
                        $data['progress'] = trim($sheetData[$x]["B"]);
                        $data['start_date'] = date('Y-m-d', strtotime($sheetData[$x]["C"]));
                        $data['end_date'] = date('Y-m-d', strtotime($sheetData[$x]["D"]));
                        $data['project_status'] = trim($sheetData[$x]["E"]);
                        $data['project_cost'] = trim($sheetData[$x]["F"]);
                        $data['demo_url'] = trim($sheetData[$x]["G"]);
                        $data['description'] = trim($sheetData[$x]["H"]);
                        $data['estimate_hours'] = '0:00';
                        $data['project_settings'] = json_encode(array('show_team_members', 'show_milestones', 'show_project_tasks', 'show_project_attachments', 'show_timesheets', 'show_project_bugs', 'show_project_history', 'show_project_calendar', 'show_project_comments', 'show_gantt_chart', 'show_project_hours', 'comment_on_project_tasks', 'show_project_tasks_attachments', 'show_tasks_hours', 'show_finance_overview'));
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
                        }
                        $this->items_model->_table_name = 'tbl_project';
                        $this->items_model->_primary_key = 'project_id';
                        $id = $this->items_model->save($data);
                    }
                    //save data into table.
                    $action = 'activity_save_project';
                    $msg = lang('save_project');

                    // save into activities
                    $activity = array(
                        'user' => $this->session->userdata('user_id'),
                        'module' => 'projects',
                        'module_field_id' => $id,
                        'activity' => $action,
                        'icon' => 'fa-folder-open-o',
                        'link' => 'admin/projects/project_details/' . $id,
                        'value1' => $data['project_name']
                    );
                    $this->items_model->_table_name = 'tbl_activities';
                    $this->items_model->_primary_key = 'activities_id';
                    $this->items_model->save($activity);

                    $type = "success";
                    $message = $msg;
                } else {
                    $type = 'error';
                    $message = "Sorry your uploaded file type not allowed ! please upload XLS/CSV File ";
                }
            } else {
                $type = 'error';
                $message = "You did not Select File! please upload XLS/CSV File ";
            }
            set_message($type, $message);
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/projects');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            set_message('error', lang('there_in_no_value'));
            redirect('admin/projects');
        }

    }

    public function saved_project($id = NULL)
    {
        $created = can_action('57', 'created');
        $edited = can_action('57', 'edited');
        if (!empty($created) || !empty($edited) && !empty($id)) {
            $this->items_model->_table_name = 'tbl_project';
            $this->items_model->_primary_key = 'project_id';

            $data = $this->items_model->array_from_post(array('project_no', 'project_name', 'client_id', 'progress', 'calculate_progress', 'start_date', 'end_date', 'billing_type', 'project_cost', 'hourly_rate', 'project_status', 'demo_url', 'description'));

            if (empty($data['project_cost'])) {
                $data['project_cost'] = '0';
            }
            if (empty($data['hourly_rate'])) {
                $data['hourly_rate'] = '0';
            }
            if ($data['project_status'] == 'completed') {
                $data['progress'] = 100;
            }
            if ($data['progress'] == 100) {
                $data['project_status'] = 'completed';
            }

            $estimate_hours = $this->input->post('estimate_hours', true);
            $check_flot = explode('.', $estimate_hours);
            if (!empty($check_flot[0])) {
                if (!empty($check_flot[1])) {
                    $data['estimate_hours'] = $check_flot[0] . ':' . $check_flot[1];
                } else {
                    $data['estimate_hours'] = $check_flot[0] . ':00';
                }
            } else {
                $data['estimate_hours'] = '0:00';
            }

            $project_permissions = $this->db->get('tbl_project_settings')->result();

            foreach ($project_permissions as $key => $v_permissions) {
                $psdata[] = $this->input->post($v_permissions->settings_id, true);
            }
            if (!empty($psdata)) {
                $data['project_settings'] = json_encode($psdata);
            } else {
                $data['project_settings'] = null;
            }
            $permission = $this->input->post('permission', true);
            if (!empty($permission)) {
                if ($permission == 'everyone') {
                    $assigned = 'all';
                    $assigned_to['assigned_to'] = $this->items_model->allowed_user_id('57');
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
                    redirect('admin/projects');
                } else {
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }

            if (!empty($id)) {
                $can_edit = $this->invoice_model->can_action('tbl_project', 'edit', array('project_id' => $id));
                if (!empty($can_edit)) {
                    $return_id = $this->items_model->save($data, $id);
                } else {
                    set_message('error', lang('there_in_no_value'));
                    redirect('admin/projects');
                }
            } else {
                $return_id = $this->items_model->save($data);
            }

            if ($assigned == 'all') {
                $assigned_to['assigned_to'] = $this->items_model->allowed_user_id('57');
            }
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
                'module' => 'projects',
                'module_field_id' => $id,
                'activity' => $action,
                'icon' => 'fa-folder-open-o',
                'link' => 'admin/projects/project_details/' . $id,
                'value1' => $data['project_name']
            );
            $this->items_model->_table_name = 'tbl_activities';
            $this->items_model->_primary_key = 'activities_id';
            $this->items_model->save($activity);

            $this->items_model->set_progress($id);
            // messages for user
            $type = "success";
            if ($this->input->post('progress') == '100') {
                $this->send_project_notify_client($id, TRUE);
            }
            $message = $msg;
            set_message($type, $message);
            redirect('admin/projects/project_details/' . $id);
        } else {
            redirect('admin/projects');
        }
    }

    public
    function clone_project($project_id)
    {
        $edited = can_action('57', 'edited');
        $can_edit = $this->invoice_model->can_action('tbl_project', 'edit', array('project_id' => $project_id));
        if (!empty($can_edit) && !empty($edited)) {
            $data['project_info'] = $this->invoice_model->check_by(array('project_id' => $project_id), 'tbl_project');
            $data['milestone_info'] = $this->invoice_model->check_by(array('project_id' => $project_id), 'tbl_milestones');
            $data['task_info'] = $this->invoice_model->check_by(array('project_id' => $project_id), 'tbl_task');
            // get all client
            $this->invoice_model->_table_name = 'tbl_client';
            $this->invoice_model->_order_by = 'client_id';
            $data['all_client'] = $this->invoice_model->get();

            $data['modal_subview'] = $this->load->view('admin/projects/clone_project', $data, FALSE);
            $this->load->view('admin/_layout_modal', $data);
        } else {
            set_message('error', lang('there_in_no_value'));
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/projects');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public function cloned_project($id)
    {
        $created = can_action('57', 'created');
        $edited = can_action('57', 'edited');
        if (!empty($created) || !empty($edited) && !empty($id)) {
            $this->items_model->_table_name = "tbl_project"; //table name
            $this->items_model->_order_by = "project_id";
            $project_info = $this->items_model->get_by(array('project_id' => $id), TRUE);

            $projects = '';
            if (empty(config_item('projects_number_format'))) {
                $projects .= config_item('projects_prefix');
            }
            $projects .= $this->items_model->generate_projects_number();
            $new_project = array(
                'project_no' => $projects,
                'project_name' => $project_info->project_name,
                'client_id' => $this->input->post('client_id', true),
                'progress' => $project_info->progress,
                'calculate_progress' => $project_info->calculate_progress,
                'start_date' => $this->input->post('start_date', true),
                'end_date' => $this->input->post('end_date', true),
                'billing_type' => $project_info->billing_type,
                'project_cost' => $project_info->project_cost,
                'hourly_rate' => $project_info->hourly_rate,
                'project_status' => $project_info->project_status,
                'estimate_hours' => $project_info->estimate_hours,
                'demo_url' => $project_info->demo_url,
                'description' => $project_info->description,
                'permission' => $project_info->permission,
                'project_settings' => $project_info->project_settings,
            );

            $this->items_model->_table_name = "tbl_project"; //table name
            $this->items_model->_primary_key = "project_id";
            $new_project_id = $this->items_model->save($new_project);
            $milestones = $this->input->post('milestones', true);
            if (!empty($milestones)) {
                //get milestones info by project id
                $this->items_model->_table_name = "tbl_milestones"; //table name
                $this->items_model->_order_by = "project_id";
                $milestones_info = $this->items_model->get_by(array('project_id' => $id), FALSE);

                if (!empty($milestones_info)) {
                    foreach ($milestones_info as $v_milestone) {
                        $milestone = array(
                            'milestone_name' => $v_milestone->milestone_name,
                            'description' => $v_milestone->description,
                            'project_id' => $new_project_id,
                            'user_id' => $v_milestone->user_id,
                            'start_date' => $v_milestone->start_date,
                            'end_date' => $v_milestone->end_date
                        );
                        $this->items_model->_table_name = "tbl_milestones"; //table name
                        $this->items_model->_primary_key = "milestones_id";
                        $this->items_model->save($milestone);
                    }
                }
            }

            $tasks = $this->input->post('tasks', true);
            if (!empty($tasks)) {
                //get tasks info by project id
                $this->items_model->_table_name = "tbl_task"; //table name
                $this->items_model->_order_by = "project_id";
                $takse_info = $this->items_model->get_by(array('project_id' => $id), FALSE);
                if (!empty($takse_info)) {
                    foreach ($takse_info as $v_task) {
                        $task = array(
                            'task_name' => $v_task->task_name,
                            'project_id' => $new_project_id,
                            'milestones_id' => $v_task->milestones_id,
                            'permission' => $v_task->permission,
                            'task_description' => $v_task->task_description,
                            'task_start_date' => $v_task->task_start_date,
                            'due_date' => $v_task->due_date,
                            'task_created_date' => $v_task->task_created_date,
                            'task_status' => $v_task->task_status,
                            'task_progress' => $v_task->task_progress,
                            'task_hour' => $v_task->task_hour,
                            'tasks_notes' => $v_task->tasks_notes,
                            'timer_status' => $v_task->timer_status,
                            'client_visible' => $v_task->client_visible,
                            'timer_started_by' => $v_task->timer_started_by,
                            'start_time' => $v_task->start_time,
                            'logged_time' => $v_task->logged_time,
                            'created_by' => $v_task->created_by
                        );
                        $this->items_model->_table_name = "tbl_task"; //table name
                        $this->items_model->_primary_key = "task_id";
                        $this->items_model->save($task);
                    }
                }
            }
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'projects',
                'module_field_id' => $id,
                'activity' => lang('activity_copied_project'),
                'icon' => 'fa-folder-open-o',
                'link' => 'admin/projects/project_details/' . $id,
                'value1' => $project_info->project_name,
            );
            // Update into tbl_project
            $this->items_model->_table_name = "tbl_activities"; //table name
            $this->items_model->_primary_key = "activities_id";
            $this->items_model->save($activities);

            // messages for user
            $type = "success";
            $message = lang('copied_project');
            set_message($type, $message);
        }
        redirect('admin/projects');
    }

    public function update_settings($id)
    {
        $edited = can_action('57', 'edited');
        $can_edit = $this->invoice_model->can_action('tbl_project', 'edit', array('project_id' => $id));
        if (!empty($can_edit) && !empty($edited) && !empty($id)) {
            $project_info = $this->items_model->check_by(array('project_id' => $id), 'tbl_project');
            $project_permissions = $this->db->get('tbl_project_settings')->result();
            foreach ($project_permissions as $key => $v_permissions) {
                $psdata[] = $this->input->post($v_permissions->settings_id, true);
            }
            $data['project_settings'] = json_encode($psdata);

            $this->items_model->_table_name = 'tbl_project';
            $this->items_model->_primary_key = 'project_id';
            $this->items_model->save($data, $id);

            $action = 'activity_update_project';
            $msg = lang('update_project');

            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'projects',
                'module_field_id' => $id,
                'activity' => $action,
                'icon' => 'fa-folder-open-o',
                'link' => 'admin/projects/project_details/' . $id,
                'value1' => $project_info->project_name
            );
            $this->items_model->_table_name = 'tbl_activities';
            $this->items_model->_primary_key = 'activities_id';
            $this->items_model->save($activity);

            // messages for user
            $type = "success";
            $message = $msg;
            set_message($type, $message);
            redirect('admin/projects/project_details/' . $id);
        } else {
            set_message('error', lang('there_in_no_value'));
            redirect('admin/projects');
        }
    }

    public function send_project_notify_assign_user($project_id, $users)
    {

        $project_info = $this->items_model->check_by(array('project_id' => $project_id), 'tbl_project');
        $email_template = email_templates(array('email_group' => 'assigned_project'), $project_info->client_id);
        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $project_name = str_replace("{PROJECT_NAME}", $project_info->project_name, $message);

        $assigned_by = str_replace("{ASSIGNED_BY}", ucfirst($this->session->userdata('name')), $project_name);
        $Link = str_replace("{PROJECT_URL}", base_url() . 'admin/projects/project_details/' . $project_id, $assigned_by);
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
            $description = 'not_completed';
        } else {
            $email_template = email_templates(array('email_group' => 'client_notification'), $project_info->client_id);
            $description = 'not_new_project_created';

        }
        $client_info = $this->items_model->check_by(array('client_id' => $project_info->client_id), 'tbl_client');
        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $clientName = str_replace("{CLIENT_NAME}", $client_info->name, $message);
        $project_name = str_replace("{PROJECT_NAME}", $project_info->project_name, $clientName);

        $Link = str_replace("{PROJECT_LINK}", base_url() . 'admin/projects/project_details/' . $project_id, $project_name);
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

    public function invoice($id)
    {
        $data['project_info'] = $this->items_model->check_by(array('project_id' => $id), 'tbl_project');

        $sbtn = $this->input->post('submit', true);

        if (!empty($sbtn)) {
            if (config_item('increment_invoice_number') == 'FALSE') {
                $this->load->helper('string');
                $reference_no = config_item('invoice_prefix') . ' ' . random_string('nozero', 6);
            } else {
                $reference_no = $this->items_model->generate_invoice_number();
            }

            $this->items_model->_table_name = "tbl_project"; //table name
            $this->items_model->_order_by = "project_id";
            $project_info = $this->items_model->get_by(array('project_id' => $id), TRUE);

            $currency = $this->items_model->client_currency_symbol($project_info->client_id);
            if (!empty($currency->code)) {
                $curr = $currency->code;
            } else {
                $curr = config_item('default_currency');
            }
            // save into invoice table
            $new_invoice = array(
                'reference_no' => $reference_no,
                'client_id' => $project_info->client_id,
                'currency' => $curr,
                'due_date' => $project_info->end_date,
            );
            $this->items_model->_table_name = "tbl_invoices"; //table name
            $this->items_model->_primary_key = "invoices_id";
            $new_invoice_id = $this->items_model->save($new_invoice);

            $items = array(
                'invoices_id' => $new_invoice_id,
                'item_name' => $project_info->project_name,
                'item_desc' => $project_info->description,
                'unit_cost' => $project_info->project_cost,
                'quantity' => 1,
                'total_cost' => $project_info->project_cost,
            );
            $this->items_model->_table_name = "tbl_items"; //table name
            $this->items_model->_primary_key = "items_id";
            $this->items_model->save($items);

            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'invoice',
                'module_field_id' => $new_invoice_id,
                'activity' => lang('activity_new_invoice_form_project'),
                'icon' => 'fa-shopping-cart',
                'link' => 'admin/invoice/manage_invoice/invoice_details/' . $new_invoice_id,
                'value1' => $reference_no,
            );
            // Update into tbl_project
            $this->items_model->_table_name = "tbl_activities"; //table name
            $this->items_model->_primary_key = "activities_id";
            $this->items_model->save($activities);

            // messages for user
            $type = "success";
            $message = lang('invoice_created');
            set_message($type, $message);
            redirect('admin/invoice/manage_invoice/invoice_details/' . $new_invoice_id);
        } else {
            // get all assign_user
            $data['modal_subview'] = $this->load->view('admin/projects/project_invoice', $data, FALSE);
            $this->load->view('admin/_layout_modal', $data);
        }
    }

    public function preview_invoice($id)
    {
        $data['project_info'] = $this->items_model->check_by(array('project_id' => $id), 'tbl_project');
        if (!empty($data['project_info'])) {

            $data['title'] = lang('preview_invoice');
            $data['items_name'] = $this->input->post('items_name', true);
            if ($data['items_name'] != 'single_line') {
                $data['tasks'] = $this->input->post('tasks', true);
            }
            $data['expense'] = $this->input->post('expense', true);
            // get all client
            $data['all_client'] = $this->db->get('tbl_client')->result();
            $data['subview'] = $this->load->view('admin/projects/preview_invoice', $data, TRUE);
            $this->load->view('admin/_layout_main', $data); //page load
        } else {
            set_message('error', lang('there_in_no_value'));
            redirect('admin/projects');
        }
    }

    function save_invoice($project_id = null)
    {
        $created = can_action('13', 'created');
        $edited = can_action('13', 'edited');
        if (!empty($created) || !empty($edited) && !empty($project_id)) {
            $save_as_draft = $this->input->post('save_as_draft', true);
            $update = $this->input->post('update', true);
            if (!empty($save_as_draft) || !empty($update)) {
                $data = $this->invoice_model->array_from_post(array('reference_no', 'client_id', 'project_id', 'discount_type', 'discount_percent', 'user_id', 'adjustment', 'discount_total', 'show_quantity_as'));

                $data['allow_paypal'] = ($this->input->post('allow_paypal') == 'Yes') ? 'Yes' : 'No';
                $data['allow_stripe'] = ($this->input->post('allow_stripe') == 'Yes') ? 'Yes' : 'No';
                $data['allow_2checkout'] = ($this->input->post('allow_2checkout') == 'Yes') ? 'Yes' : 'No';
                $data['allow_authorize'] = ($this->input->post('allow_authorize') == 'Yes') ? 'Yes' : 'No';
                $data['allow_ccavenue'] = ($this->input->post('allow_ccavenue') == 'Yes') ? 'Yes' : 'No';
                $data['allow_braintree'] = ($this->input->post('allow_braintree') == 'Yes') ? 'Yes' : 'No';
                $data['allow_mollie'] = ($this->input->post('allow_mollie') == 'Yes') ? 'Yes' : 'No';
                $data['allow_payumoney'] = ($this->input->post('allow_payumoney') == 'Yes') ? 'Yes' : 'No';
                $data['allow_tapPayment'] = ($this->input->post('allow_tapPayment') == 'Yes') ? 'Yes' : 'No';
                $data['allow_razorpay'] = ($this->input->post('allow_razorpay') == 'Yes') ? 'Yes' : 'No';
                $data['client_visible'] = ($this->input->post('client_visible') == 'Yes') ? 'Yes' : 'No';
                $data['invoice_date'] = date('Y-m-d', strtotime($this->input->post('invoice_date', TRUE)));
                if (empty($data['invoice_date'])) {
                    $data['invoice_date'] = date('Y-m-d');
                }
                $data['invoice_year'] = date('Y', strtotime($this->input->post('invoice_date', TRUE)));
                $data['invoice_month'] = date('Y-m', strtotime($this->input->post('invoice_date', TRUE)));
                $data['due_date'] = date('Y-m-d', strtotime($this->input->post('due_date', TRUE)));
                $data['notes'] = $this->input->post('notes', TRUE);
                $tax['tax_name'] = $this->input->post('total_tax_name', TRUE);
                $tax['total_tax'] = $this->input->post('total_tax', TRUE);
                $data['total_tax'] = json_encode($tax);
                $i_tax = 0;
                if (!empty($tax['total_tax'])) {
                    foreach ($tax['total_tax'] as $v_tax) {
                        $i_tax += $v_tax;
                    }
                }
                $data['tax'] = $i_tax;
                $save_as_draft = $this->input->post('save_as_draft', TRUE);
                if (!empty($save_as_draft)) {
                    $data['status'] = 'draft';
                }

                $currency = $this->invoice_model->client_currency_symbol($data['client_id']);
                if (!empty($currency->code)) {
                    $curren = $currency->code;
                } else {
                    $curren = config_item('default_currency');
                }
                $data['currency'] = $curren;

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
                        redirect('admin/projects');
                    } else {
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                }
                // get all client
                $this->items_model->_table_name = 'tbl_invoices';
                $this->items_model->_primary_key = 'invoices_id';
                $invoice_id = $this->items_model->save($data);
                $project_info = $this->items_model->check_by(array('project_id' => $data['project_id']), 'tbl_project');
                if (!empty($project_info->client_id)) {
                    $client_info = $this->items_model->check_by(array('client_id' => $project_info->client_id), 'tbl_client');
                    if (!empty($client_info->primary_contact)) {
                        $notifyUser = array($client_info->primary_contact);
                    }
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
                                'icon' => 'shopping-cart',
                                'description' => 'not_invoice_created',
                                'link' => 'client/invoice/manage_invoice/invoice_details/' . $invoice_id,
                                'value' => $data['reference_no'],
                            ));
                        }
                    }
                    show_notification($notifyUser);
                }
                save_custom_field(9, $invoice_id);

                $recuring_frequency = $this->input->post('recuring_frequency', TRUE);
                if (!empty($recuring_frequency) && $recuring_frequency != 'none') {
                    $recur_data = $this->items_model->array_from_post(array('recur_start_date', 'recur_end_date'));
                    $recur_data['recuring_frequency'] = $recuring_frequency;
                    $this->get_recuring_frequency($invoice_id, $recur_data); // set recurring
                }
                $qty_calculation = config_item('qty_calculation_from_items');
                // save items
                $invoices_to_merge = $this->input->post('invoices_to_merge', TRUE);
                $cancel_merged_invoices = $this->input->post('cancel_merged_invoices', TRUE);
                if (!empty($invoices_to_merge)) {
                    foreach ($invoices_to_merge as $inv_id) {
                        if (empty($cancel_merged_invoices)) {
                            if (!empty($qty_calculation) && $qty_calculation == 'Yes') {
                                $all_items_info = $this->db->where('invoices_id', $inv_id)->get('tbl_items')->result();
                                if (!empty($all_items_info)) {
                                    foreach ($all_items_info as $v_items) {
                                        $this->return_items($v_items->items_id);
                                    }
                                }
                            }
                            $this->db->where('invoices_id', $inv_id);
                            $this->db->delete('tbl_invoices');

                            $this->db->where('invoices_id', $inv_id);
                            $this->db->delete('tbl_items');

                        } else {
                            $mdata = array('status' => 'Cancelled');
                            $this->invoice_model->_table_name = 'tbl_invoices';
                            $this->invoice_model->_primary_key = 'invoices_id';
                            $this->invoice_model->save($mdata, $inv_id);
                        }
                    }
                }

                $removed_items = $this->input->post('removed_items', TRUE);
                if (!empty($removed_items)) {
                    foreach ($removed_items as $r_id) {
                        if ($r_id != 'undefined') {
                            if (!empty($qty_calculation) && $qty_calculation == 'Yes') {
                                $this->return_items($r_id);
                            }

                            $this->db->where('items_id', $r_id);
                            $this->db->delete('tbl_items');
                        }
                    }
                }

                $itemsid = $this->input->post('items_id', TRUE);
                $items_data = $this->input->post('items', true);

                if (!empty($items_data)) {
                    $index = 0;
                    foreach ($items_data as $items) {
                        $items['invoices_id'] = $invoice_id;
                        $tax = 0;
                        if (!empty($items['taxname'])) {
                            foreach ($items['taxname'] as $tax_name) {
                                $tax_rate = explode("|", $tax_name);
                                $tax += $tax_rate[1];

                            }
                            $items['item_tax_name'] = $items['taxname'];
                            unset($items['taxname']);
                            $items['item_tax_name'] = json_encode($items['item_tax_name']);
                        }
                        if (empty($items['saved_items_id'])) {
                            $items['saved_items_id'] = 0;
                        }
                        if (!empty($qty_calculation) && $qty_calculation == 'Yes') {
                            if (!empty($items['saved_items_id']) && $items['saved_items_id'] != 'undefined') {
                                $this->invoice_model->reduce_items($items['saved_items_id'], $items['quantity']);
                            }
                        }
                        $price = $items['quantity'] * $items['unit_cost'];
                        $items['item_tax_total'] = ($price / 100 * $tax);
                        $items['total_cost'] = $price;
                        // get all client
                        $this->invoice_model->_table_name = 'tbl_items';
                        $this->invoice_model->_primary_key = 'items_id';
                        $this->invoice_model->save($items);
                        if (!empty($items['items_id'])) {
                            $items_id = $items['items_id'];
                            if (!empty($qty_calculation) && $qty_calculation == 'Yes') {
                                $this->check_existing_qty($items_id, $items['quantity']);
                            }
                        }
                        $index++;
                    }
                }
                if (!empty($data['user_id'])) {
                    $notifiedUsers = array($data['user_id']);
                    foreach ($notifiedUsers as $users) {
                        if ($users != $this->session->userdata('user_id')) {
                            $r = true;
                            add_notification(array(
                                'to_user_id' => $users,
                                'description' => 'project_to_invoice_generated',
                                'icon' => 'shopping-cart',
                                'link' => 'admin/invoice/manage_invoice/invoice_details/' . $invoice_id,
                                'value' => lang('project') . ': ' . $project_info->project_name . ' ' . lang('invoice') . ': ' . $data['reference_no'],
                            ));
                        }
                    }
                    if (!empty($r)) {
                        show_notification($notifiedUsers);
                    }
                }

                $task_id = $this->input->post('task_id', true);
                if (!empty($task_id)) {
                    foreach ($task_id as $task) {
                        $tdata['task_progress'] = 100;
                        $tdata['task_status'] = 'completed';
                        update('tbl_task', array('task_id' => $task), $tdata);
                    }
                }
                // send notification to client

                if (!empty($project_info->client_id)) {
                    $client_info = $this->items_model->check_by(array('client_id' => $project_info->client_id), 'tbl_client');
                    if (!empty($client_info->primary_contact)) {
                        $notifyUser = array($client_info->primary_contact);
                    }
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
                                'icon' => 'shopping-cart',
                                'description' => 'not_invoice_created',
                                'link' => 'client/invoice/manage_invoice/invoice_details/' . $invoice_id,
                                'value' => $data['reference_no'],
                            ));
                        }
                    }
                    show_notification($notifyUser);
                }

                $activity = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'invoice',
                    'module_field_id' => $invoice_id,
                    'activity' => 'activity_project_invoice_generated',
                    'icon' => 'fa-shopping-cart',
                    'link' => 'admin/invoice/manage_invoice/invoice_details/' . $invoice_id,
                    'value1' => lang('project') . ': ' . $project_info->project_name . lang('invoice') . ' ' . ': ' . $data['reference_no'],
                );
                $this->items_model->_table_name = 'tbl_activities';
                $this->items_model->_primary_key = 'activities_id';
                $this->items_model->save($activity);

                $transactions_id = $this->input->post('transactions_id', true);
                if (!empty($transactions_id)) {
                    $tr_data['invoices_id'] = $invoice_id;
                    $this->invoice_model->_table_name = "tbl_transactions"; //table name
                    $this->invoice_model->_primary_key = "transactions_id";
                    $this->invoice_model->save($tr_data, $transactions_id);
                }
            }
            $type = "success";
            $message = lang('project_invoice_generated');
            set_message($type, $message);
            redirect('admin/invoice/manage_invoice/invoice_details/' . $invoice_id);
        } else {
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/projects');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }


    function return_items($items_id)
    {
        $items_info = $this->db->where('items_id', $items_id)->get('tbl_items')->row();
        if (!empty($items_info->saved_items_id)) {
            $this->invoice_model->return_items($items_info->saved_items_id, $items_info->quantity);
        }
        return true;

    }

    function check_existing_qty($items_id, $qty)
    {
        $items_info = $this->db->where('items_id', $items_id)->get('tbl_items')->row();
        if ($items_info->quantity != $qty) {
            if ($qty > $items_info->quantity) {
                $reduce_qty = $qty - $items_info->quantity;
                if (!empty($items_info->saved_items_id)) {
                    $this->invoice_model->reduce_items($items_info->saved_items_id, $reduce_qty);
                }
            }
            if ($qty < $items_info->quantity) {
                $return_qty = $items_info->quantity - $qty;
                if (!empty($items_info->saved_items_id)) {
                    $this->invoice_model->return_items($items_info->saved_items_id, $return_qty);
                }
            }
        }
        return true;

    }

    public function add_item($id = null)
    {
        $data = $this->items_model->array_from_post(array('invoices_id', 'item_order'));
        $quantity = $this->input->post('quantity', TRUE);
        $array_data = $this->items_model->array_from_post(array('item_name', 'item_desc', 'item_tax_rate', 'unit_cost'));
        if (!empty($quantity)) {
            foreach ($quantity as $key => $value) {
                $data['quantity'] = $value;
                $data['item_name'] = $array_data['item_name'][$key];
                $data['item_desc'] = $array_data['item_desc'][$key];
                $data['unit_cost'] = $array_data['unit_cost'][$key];
                $data['item_tax_rate'] = $array_data['item_tax_rate'][$key];
                $sub_total = $data['unit_cost'] * $data['quantity'];

                $data['item_tax_total'] = ($data['item_tax_rate'] / 100) * $sub_total;
                $data['total_cost'] = $sub_total + $data['item_tax_total'];

                // get all client
                $this->items_model->_table_name = 'tbl_items';
                $this->items_model->_primary_key = 'items_id';
                if (!empty($id)) {
                    $items_id = $id;
                    $this->items_model->save($data, $id);
                    $action = lang('activity_invoice_items_updated');
                    $msg = lang('invoice_item_updated');
                } else {
                    $items_id = $this->items_model->save($data);
                    $action = lang('activity_invoice_items_added');
                    $msg = lang('invoice_item_added');
                }
                $activity = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'invoice',
                    'module_field_id' => $items_id,
                    'activity' => $action,
                    'icon' => 'fa-circle-o',
                    'value1' => $data['item_name']
                );
                $this->items_model->_table_name = 'tbl_activities';
                $this->items_model->_primary_key = 'activities_id';
                $this->items_model->save($activity);
            }
        }
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/invoice/manage_invoice/invoice_details/' . $data['invoices_id']);

    }

    function get_recuring_frequency($invoices_id, $recur_data)
    {
        $recur_days = $this->get_calculate_recurring_days($recur_data['recuring_frequency']);
        $due_date = $this->items_model->get_table_field('tbl_invoices', array('invoices_id' => $invoices_id), 'due_date');

        $next_date = date("Y-m-d", strtotime($due_date . "+ " . $recur_days . " days"));

        if ($recur_data['recur_end_date'] == '') {
            $recur_end_date = '0000-00-00';
        } else {
            $recur_end_date = date('Y-m-d', strtotime($recur_data['recur_end_date']));
        }
        $update_invoice = array(
            'recurring' => 'Yes',
            'recuring_frequency' => $recur_days,
            'recur_frequency' => $recur_data['recuring_frequency'],
            'recur_start_date' => date('Y-m-d', strtotime($recur_data['recur_start_date'])),
            'recur_end_date' => $recur_end_date,
            'recur_next_date' => $next_date
        );
        $this->items_model->_table_name = 'tbl_invoices';
        $this->items_model->_primary_key = 'invoices_id';
        $this->items_model->save($update_invoice, $invoices_id);
        return TRUE;
    }

    function get_calculate_recurring_days($recuring_frequency)
    {
        switch ($recuring_frequency) {
            case '7D':
                return 7;
                break;
            case '1M':
                return 31;
                break;
            case '3M':
                return 90;
                break;
            case '6M':
                return 182;
                break;
            case '1Y':
                return 365;
                break;
        }
    }

    public function project_details($id, $active = NULL, $op_id = NULL)
    {

        $data['title'] = lang('project_details');
        //get all task information
        $data['project_details'] = $this->items_model->check_by(array('project_id' => $id), 'tbl_project');
        if (empty($data['project_details'])) {
            set_message('error', lang('there_in_no_value'));
            redirect('admin/projects');
        }
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
            $data['estimate'] = 1;
        } elseif ($active == 3) {
            $data['active'] = 3;
            $data['miles_active'] = 1;
            $data['task_active'] = 1;
            $data['bugs_active'] = 1;
            $data['time_active'] = 1;
            $data['estimate'] = 1;
        } elseif ($active == 4) {
            $data['active'] = 4;
            $data['miles_active'] = 1;
            $data['task_active'] = 1;
            $data['bugs_active'] = 1;
            $data['time_active'] = 1;
            $data['estimate'] = 1;
        } elseif ($active == 5) {
            $data['active'] = 5;
            $data['miles_active'] = 1;
            $data['task_active'] = 1;
            $data['bugs_active'] = 1;
            $data['time_active'] = 1;
            $data['estimate'] = 1;
        } elseif ($active == 'milestone') {
            $data['active'] = 5;
            $data['miles_active'] = 2;
            $data['task_active'] = 1;
            $data['bugs_active'] = 1;
            $data['time_active'] = 1;
            $data['estimate'] = 1;
            $data['milestones_info'] = $this->items_model->check_by(array('milestones_id' => $op_id), 'tbl_milestones');
        } elseif ($active == 6) {
            $data['active'] = 6;
            $data['miles_active'] = 1;
            $data['task_active'] = 1;
            $data['bugs_active'] = 1;
            $data['time_active'] = 1;
            $data['estimate'] = 1;
        } elseif ($active == 7) {
            $data['active'] = 7;
            $data['miles_active'] = 1;
            $data['task_active'] = 1;
            $data['bugs_active'] = 1;
            $data['estimate'] = 1;
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
            $data['estimate'] = 1;
        } elseif ($active == 10) {
            $data['active'] = 10;
            $data['miles_active'] = 1;
            $data['task_active'] = 1;
            $data['bugs_active'] = 1;
            $data['time_active'] = 1;
            $data['estimate'] = 1;
        } elseif ($active == 13) {
            $data['active'] = 13;
            $data['miles_active'] = 1;
            $data['task_active'] = 1;
            $data['bugs_active'] = 1;
            $data['time_active'] = 1;
            $data['estimate'] = 1;
        } elseif ($active == 15) {
            $data['active'] = 15;
            $data['miles_active'] = 1;
            $data['task_active'] = 1;
            $data['bugs_active'] = 1;
            $data['time_active'] = 1;
            $data['estimate'] = 1;
        } else {
            $data['active'] = 1;
            $data['miles_active'] = 1;
            $data['task_active'] = 1;
            $data['bugs_active'] = 1;
            $data['time_active'] = 1;
            $data['estimate'] = 1;
        }
        $data['subview'] = $this->load->view('admin/projects/project_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function update_users($id)
    {
        $edited = can_action('57', 'edited');
        $can_edit = $this->invoice_model->can_action('tbl_project', 'edit', array('project_id' => $id));
        if (!empty($can_edit) && !empty($edited)) {
            // get all assign_user
            $data['assign_user'] = $this->items_model->allowed_user('57');
            $data['project_info'] = $this->items_model->check_by(array('project_id' => $id), 'tbl_project');
            $data['modal_subview'] = $this->load->view('admin/projects/_modal_users', $data, FALSE);
            $this->load->view('admin/_layout_modal', $data);
        } else {
            set_message('error', lang('there_in_no_value'));
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/projects');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public function update_member($id)
    {
        $edited = can_action('57', 'edited');
        $can_edit = $this->invoice_model->can_action('tbl_project', 'edit', array('project_id' => $id));
        if (!empty($can_edit) && !empty($edited)) {
            $project_info = $this->items_model->check_by(array('project_id' => $id), 'tbl_project');

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
                    redirect('admin/projects');
                } else {
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }
            if ($assigned == 'all') {
                $assigned_to['assigned_to'] = $this->items_model->allowed_user_id('57');
            }
//save data into table.
            $this->items_model->_table_name = "tbl_project"; // table name
            $this->items_model->_primary_key = "project_id"; // $id
            $this->items_model->save($data, $id);

            $msg = lang('update_project');
            $activity = 'activity_update_project';
            if (!empty($assigned_to['assigned_to'])) {
                $this->send_project_notify_assign_user($id, $assigned_to['assigned_to']);
            }

// save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'projects',
                'module_field_id' => $id,
                'activity' => $activity,
                'icon' => 'fa-folder-open-o',
                'link' => 'admin/projects/project_details/' . $id,
                'value1' => $project_info->project_name,
            );
// Update into tbl_project
            $this->items_model->_table_name = "tbl_activities"; //table name
            $this->items_model->_primary_key = "activities_id";
            $this->items_model->save($activities);

            $type = "success";
            $message = $msg;
            set_message($type, $message);

        } else {
            set_message('error', lang('there_in_no_value'));
        }
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('admin/projects');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }

    }

    public function change_status($project_id, $status)
    {
        $edited = can_action('57', 'edited');
        $can_edit = $this->invoice_model->can_action('tbl_project', 'edit', array('project_id' => $id));
        if (!empty($can_edit) && !empty($edited)) {
            $project_info = $this->items_model->check_by(array('project_id' => $project_id), 'tbl_project');
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
                            'description' => 'not_changed_status',
                            'link' => 'admin/projects/project_details/' . $project_id,
                            'value' => lang('status') . ' : ' . lang($project_info->project_status) . ' to ' . lang($status),
                        ));
                    }
                }
            }
            show_notification($notifiedUsers);


            $client_info = $this->items_model->check_by(array('client_id' => $project_info->client_id), 'tbl_client');
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
                            'description' => 'not_changed_status',
                            'link' => 'client/projects/project_details/' . $project_id,
                            'value' => lang('status') . ' : ' . lang($project_info->project_status) . ' to ' . lang($status),
                        ));
                    }
                }
                show_notification($notifyUser);
            }

            $data['project_status'] = $status;
            if ($data['project_status'] == 'completed') {
                $data['progress'] = 100;
                $this->tasks_timer('off', $project_id, true);
            }
            if (!empty($data['progress']) && $data['progress'] == 100) {
                $data['project_status'] = 'completed';
            }
            $this->items_model->_table_name = 'tbl_project';
            $this->items_model->_primary_key = 'project_id';
            $this->items_model->save($data, $project_id);
            // messages for user
            $type = "success";
            $message = lang('change_status');
            set_message($type, $message);
        }
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('admin/projects');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function save_comments()
    {
        $data['project_id'] = $this->input->post('project_id');
        $data['comment'] = $this->input->post('description');

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
            $response_data = $this->load->view("admin/projects/comments_list", $view_data, true);
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
            $this->notify_comments_project($comment_id, true);
            $response_data = "";
            $view_data['comment_details'] = $this->db->where(array('task_comment_id' => $comment_id))->order_by('comment_datetime', 'DESC')->get('tbl_task_comment')->result();
            $response_data = $this->load->view("admin/projects/comments_list", $view_data, true);
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
            $user = $this->items_model->check_by(array('user_id' => $comments_info->user_id), 'tbl_users');
            if ($user->role_id == 2) {
                $url = 'client/';
            } else {
                $url = 'admin/';
            }
            $project_info = $this->items_model->check_by(array('project_id' => $data['project_id']), 'tbl_project');
            $notifiedUsers = array($comments_info->user_id);
            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => 'not_comment_reply',
                            'link' => $url . 'projects/project_details/' . $project_info->project_id . '/3',
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
                'activity' => 'activity_new_comment_reply',
                'icon' => 'fa-folder-open-o',
                'link' => $url . 'projects/project_details/' . $data['project_id'] . '/3',
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
            $response_data = $this->load->view("admin/projects/comments_reply", $view_data, true);
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


    function notify_comments_project($comment_id, $attachment = null)
    {

        $comment_info = $this->items_model->check_by(array('task_comment_id' => $comment_id), 'tbl_task_comment');
        $project_info = $this->items_model->check_by(array('project_id' => $comment_info->project_id), 'tbl_project');
        $email_template = email_templates(array('email_group' => 'project_comments'), $project_info->client_id);
        $user = $this->items_model->check_by(array('user_id' => $comment_info->user_id), 'tbl_users');
        if ($user->role_id == 2) {
            $url = 'client/';
        } else {
            $url = 'admin/';
        }

        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $projectName = str_replace("{PROJECT_NAME}", $project_info->project_name, $message);
        $assigned_by = str_replace("{POSTED_BY}", ucfirst($this->session->userdata('name')), $projectName);
        $Link = str_replace("{COMMENT_URL}", base_url() . $url . 'projects/project_details/' . $project_info->project_id . '/3', $assigned_by);
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

    public function new_attachment($id)
    {
        $data['dropzone'] = true;
        $data['project_info'] = $this->items_model->check_by(array('project_id' => $id), 'tbl_project');
        $data['modal_subview'] = $this->load->view('admin/projects/new_attachment', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function attachment_details($type, $id)
    {
        $data['type'] = $type;
        $data['attachment_info'] = $this->items_model->check_by(array('task_attachment_id' => $id), 'tbl_task_attachment');
        $data['modal_subview'] = $this->load->view('admin/projects/attachment_details', $data, FALSE);
        $this->load->view('admin/_layout_modal_extra_lg', $data);
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
            'module' => 'projects',
            'module_field_id' => $data['project_id'],
            'activity' => 'activity_new_project_attachment',
            'icon' => 'fa-folder-open-o',
            'link' => 'admin/projects/project_details/' . $data['project_id'] . '/4',
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
        redirect('admin/projects/project_details/' . $data['project_id'] . '/' . '4');
    }

    function notify_attchemnt_project($task_attachment_id)
    {
        $comment_info = $this->items_model->check_by(array('task_attachment_id' => $task_attachment_id), 'tbl_task_attachment');
        $project_info = $this->items_model->check_by(array('project_id' => $comment_info->project_id), 'tbl_project');
        $email_template = email_templates(array('email_group' => 'project_attachment'), $project_info->client_id);

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

    public function save_milestones($milestones_id = NULL)
    {
        $data = $this->items_model->array_from_post(array('project_id', 'milestone_name', 'description', 'start_date', 'end_date', 'user_id', 'client_visible'));
        // Update into tbl_project
        $this->items_model->_table_name = "tbl_milestones"; //table name
        $this->items_model->_primary_key = "milestones_id";
        if (!empty($milestones_id)) {
            $id = $milestones_id;
            $this->items_model->save($data, $milestones_id);
            $action = ('activity_updated_milestones');
            $msg = lang('update_milestone');
        } else {
            $id = $this->items_model->save($data);
            $action = 'activity_added_new_milestones';
            $msg = lang('create_milestone');
        }
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'projects',
            'module_field_id' => $id,
            'activity' => $action,
            'icon' => 'fa-folder-open-o',
            'link' => 'admin/projects/project_details/' . $id . '/5',
            'value1' => $data['milestone_name'],
        );
        // Update into tbl_project
        $this->items_model->_table_name = "tbl_activities"; //table name
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);
        $this->send_project_notify_milestone($id);
        // messages for user
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/projects/project_details/' . $data['project_id'] . '/' . '5');
    }

    public function send_project_notify_milestone($milestones_id)
    {

        $milestone_info = $this->items_model->check_by(array('milestones_id' => $milestones_id), 'tbl_milestones');
        $project_info = $this->items_model->check_by(array('project_id' => $milestone_info->project_id), 'tbl_project');
        $email_template = email_templates(array('email_group' => 'responsible_milestone'), $milestone_info->user_id, true);
        $user_info = $this->items_model->check_by(array('user_id' => $milestone_info->user_id), 'tbl_users');
        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $milestone = str_replace("{MILESTONE_NAME}", $milestone_info->milestone_name, $message);
        $assigned_by = str_replace("{ASSIGNED_BY}", ucfirst($this->session->userdata('name')), $milestone);
        $project_name = str_replace("{PROJECT_NAME}", $project_info->project_name, $assigned_by);

        $Link = str_replace("{PROJECT_URL}", base_url() . 'admin/projects/project_details/' . $milestone_info->project_id . '/' . $data['active'] = 5, $project_name);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';
        if (!empty($user_info)) {
            $params['recipient'] = $user_info->email;
            $this->items_model->send_email($params);
            $project_info = $this->items_model->check_by(array('project_id' => $milestone_info->project_id), 'tbl_project');

            if ($user_info->user_id != $this->session->userdata('user_id')) {
                add_notification(array(
                    'to_user_id' => $user_info->user_id,
                    'from_user_id' => true,
                    'description' => 'not_responsible_milestone',
                    'link' => 'admin/projects/project_details/' . $project_info->project_id . '/4',
                    'value' => lang('project') . ' ' . $project_info->project_name,
                ));
            }
            show_notification(array($user_info->user_id));
        }
    }

    public function delete_milestones($project_id, $milestones_id)
    {

        $this->items_model->_table_name = "tbl_milestones"; //table name
        $this->items_model->_order_by = "milestones_id";
        $milestones_info = $this->items_model->get_by(array('milestones_id' => $milestones_id), TRUE);
        if (empty($milestones_info)) {
            $type = "error";
            $message = "No Record Found";
            echo json_encode(array("status" => $type, 'message' => $message));
            exit();
        }
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'projects',
            'module_field_id' => $project_id,
            'activity' => lang('activity_delete_milestones'),
            'icon' => 'fa-folder-open-o',
            'link' => 'admin/projects/project_details/' . $project_id . '/5',
            'value1' => $milestones_info->milestone_name,
        );
        // Update into tbl_project
        $this->items_model->_table_name = "tbl_activities"; //table name
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);

        //save data into table.
        $this->items_model->_table_name = "tbl_milestones"; // table name
        $this->items_model->delete_multiple(array('milestones_id' => $milestones_id));

        // delete into tbl_milestones
        $this->items_model->_table_name = "tbl_milestones"; //table name
        $this->items_model->_primary_key = "milestones_id";
        $this->items_model->delete($milestones_id);
        // Update into tbl_tasks

        $this->items_model->_table_name = "tbl_task"; //table name
        $this->items_model->delete_multiple(array('milestones_id' => $milestones_id));

        echo json_encode(array("status" => 'success', 'message' => lang('delete_milestone')));
        exit();
    }

    public function change_milestones($milestone_id)
    {
        $task_id = $this->input->post('task_id', true);
        foreach ($task_id as $key => $id) {
            $data['milestones_id'] = $milestone_id;
            $data['milestones_order'] = $key + 1;
            //save data into table.
            $this->items_model->_table_name = "tbl_task"; // table name
            $this->items_model->_primary_key = "task_id"; // $id
            $id = $this->items_model->save($data, $id);

        }
        $m_info = $this->db->where('milestones_id', $milestone_id)->get('tbl_milestones')->row();
        if (!empty($m_info)) {
            $m_catagory = $m_info->milestone_name;
        } else {
            $m_catagory = lang('uncategorized');
        }
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'tasks',
            'module_field_id' => $milestone_id,
            'activity' => 'activity_updated_milestones',
            'icon' => 'fa-tasks',
            'value1' => $m_catagory,
        );
        // Update into tbl_project
        $this->items_model->_table_name = "tbl_activities"; //table name
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);
        $type = "success";
        $message = lang('update_milestone');
        echo json_encode(array("status" => $type, "message" => $message));
    }

    public function bulk_delete()
    {
        $selected_id = $this->input->post('ids', true);
        if (!empty($selected_id)) {
            foreach ($selected_id as $id) {
                $result[] = $this->delete_project($id, true);
            }
            echo json_encode($result);
            exit();
        } else {
            $type = "error";
            $message = lang('you_need_select_to_delete');
            echo json_encode(array("status" => $type, 'message' => $message));
            exit();
        }
    }

    public function delete_project($id, $bulk = null)
    {
        $deleted = can_action('57', 'deleted');
        $can_delete = $this->items_model->can_action('tbl_project', 'delete', array('project_id' => $id));
        if (!empty($deleted) && !empty($can_delete)) {
            $project_info = $this->items_model->check_by(array('project_id' => $id), 'tbl_project');
            if (empty($project_info)) {
                $type = "error";
                $message = "No Record Found";
                if (!empty($bulk)) {
                    return (array("status" => $type, 'message' => $message));
                }
                echo json_encode(array("status" => $type, 'message' => $message));
                exit();
            }
            if (!empty($project_info)) {
                $activity = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'projects',
                    'module_field_id' => $id,
                    'activity' => 'activity_project_deleted',
                    'icon' => 'fa-folder-open-o',
                    'value1' => $project_info->project_name
                );
                $this->items_model->_table_name = 'tbl_activities';
                $this->items_model->_primary_key = 'activities_id';
                $this->items_model->save($activity);

                // deleted project comments with file
                $all_comments_info = $this->db->where(array('project_id' => $id))->get('tbl_task_comment')->result();
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
                $this->items_model->_table_name = "tbl_task_comment"; // table name
                $this->items_model->delete_multiple(array('project_id' => $id));

                // deleted project attachment with file
                $this->items_model->_table_name = "tbl_task_attachment"; //table name
                $this->items_model->_order_by = "project_id";
                $files_info = $this->items_model->get_by(array('project_id' => $id), FALSE);
                if (!empty($files_info)) {
                    foreach ($files_info as $v_files) {
                        $uploadFileinfo = $this->db->where('task_attachment_id', $v_files->task_attachment_id)->get('tbl_task_uploaded_files')->result();
                        if (!empty($uploadFileinfo)) {
                            foreach ($uploadFileinfo as $Fileinfo) {
                                remove_files($Fileinfo->file_name);
                            }
                        }
                        //save data into table.
                        $this->items_model->_table_name = "tbl_task_uploaded_files"; // table name
                        $this->items_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
                    }
                }
                $this->items_model->_table_name = "tbl_task_attachment"; // table name
                $this->items_model->delete_multiple(array('project_id' => $id));

                // deleted project milestone
                $this->items_model->_table_name = "tbl_milestones"; // table name
                $this->items_model->delete_multiple(array('project_id' => $id));

                // deleted project tasks and task comments , attachments,timer
                $project_tasks = $this->db->where('project_id', $id)->get('tbl_task')->result();
                if (!empty($project_tasks)) {
                    foreach ($project_tasks as $v_taks) {

                        $all_comments_info = $this->db->where(array('task_id' => $v_taks->task_id))->get('tbl_task_comment')->result();
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
                        $this->items_model->_table_name = "tbl_task_comment"; // table name
                        $this->items_model->delete_multiple(array('task_id' => $v_taks->task_id));

                        $this->items_model->_table_name = "tbl_task_attachment"; //table name
                        $this->items_model->_order_by = "task_id";
                        $files_info = $this->items_model->get_by(array('task_id' => $v_taks->task_id), FALSE);
                        if (!empty($files_info)) {
                            foreach ($files_info as $t_v_files) {
                                $uploadFileinfo = $this->db->where('task_attachment_id', $t_v_files->task_attachment_id)->get('tbl_task_uploaded_files')->result();
                                if (!empty($uploadFileinfo)) {
                                    foreach ($uploadFileinfo as $Fileinfo) {
                                        remove_files($Fileinfo->file_name);
                                    }
                                }
                                $this->items_model->_table_name = "tbl_task_uploaded_files"; //table name
                                $this->items_model->delete_multiple(array('task_attachment_id' => $t_v_files->task_attachment_id));
                            }
                        }
                        //delete into table.
                        $this->items_model->_table_name = "tbl_task_attachment"; // table name
                        $this->items_model->delete_multiple(array('task_id' => $v_taks->task_id));

                        //delete into table.
                        $this->items_model->_table_name = "tbl_tasks_timer"; // table name
                        $this->items_model->delete_multiple(array('task_id' => $v_taks->task_id));

                        $pin_info = $this->items_model->check_by(array('module_name' => 'tasks', 'module_id' => $v_taks->task_id), 'tbl_pinaction');
                        if (!empty($pin_info)) {
                            $this->items_model->_table_name = 'tbl_pinaction';
                            $this->items_model->delete_multiple(array('module_name' => 'tasks', 'module_id' => $v_taks->task_id));
                        }
                    }
                }

                $this->items_model->_table_name = "tbl_task"; // table name
                $this->items_model->delete_multiple(array('project_id' => $id));

                // deleted project bugs and bug comments , attachments,bug taks and everything
                $project_bugs = $this->db->where('project_id', $id)->get('tbl_bug')->result();
                if (!empty($project_bugs)) {
                    foreach ($project_bugs as $v_bugs) {

                        $all_comments_info = $this->db->where(array('bug_id' => $v_bugs->bug_id))->get('tbl_task_comment')->result();
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
                        $this->bugs_model->delete_multiple(array('bug_id' => $v_bugs->bug_id));


                        $this->bugs_model->_table_name = "tbl_task_attachment"; //table name
                        $this->bugs_model->_order_by = "bug_id";
                        $files_info = $this->bugs_model->get_by(array('bug_id' => $v_bugs->bug_id), FALSE);

                        foreach ($files_info as $b_v_files) {
                            $uploadFileinfo = $this->db->where('task_attachment_id', $b_v_files->task_attachment_id)->get('tbl_task_uploaded_files')->result();
                            if (!empty($uploadFileinfo)) {
                                foreach ($uploadFileinfo as $Fileinfo) {
                                    remove_files($Fileinfo->file_name);
                                }
                            }
                            $this->bugs_model->_table_name = "tbl_task_uploaded_files"; //table name
                            $this->bugs_model->delete_multiple(array('task_attachment_id' => $b_v_files->task_attachment_id));
                        }
                        //delete into table.
                        $this->bugs_model->_table_name = "tbl_task_attachment"; // table name
                        $this->bugs_model->delete_multiple(array('bug_id' => $v_bugs->bug_id));

                        $this->bugs_model->_table_name = 'tbl_pinaction';
                        $this->bugs_model->delete_multiple(array('module_name' => 'bugs', 'module_id' => $v_bugs->bug_id));

                        $bug_tasks = $this->db->where('bug_id', $v_bugs->bug_id)->get('tbl_task')->result();
                        if (!empty($bug_tasks)) {
                            foreach ($bug_tasks as $tasks_bugs) {

                                $all_comments_info = $this->db->where(array('task_id' => $tasks_bugs->task_id))->get('tbl_task_comment')->result();
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
                                $this->items_model->_table_name = "tbl_task_comment"; // table name
                                $this->items_model->delete_multiple(array('task_id' => $tasks_bugs->task_id));

                                $this->items_model->_table_name = "tbl_task_attachment"; //table name
                                $this->items_model->_order_by = "task_id";
                                $files_info = $this->items_model->get_by(array('task_id' => $tasks_bugs->task_id), FALSE);
                                if (!empty($files_info)) {
                                    foreach ($files_info as $t_v_files) {
                                        $uploadFileinfo = $this->db->where('task_attachment_id', $t_v_files->task_attachment_id)->get('tbl_task_uploaded_files')->result();
                                        if (!empty($uploadFileinfo)) {
                                            foreach ($uploadFileinfo as $Fileinfo) {
                                                remove_files($Fileinfo->file_name);
                                            }
                                        }
                                        $this->items_model->_table_name = "tbl_task_uploaded_files"; //table name
                                        $this->items_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
                                    }
                                }
                                //delete into table.
                                $this->items_model->_table_name = "tbl_task_attachment"; // table name
                                $this->items_model->delete_multiple(array('task_id' => $tasks_bugs->task_id));

                                $pin_info = $this->items_model->check_by(array('module_name' => 'tasks', 'module_id' => $tasks_bugs->task_id), 'tbl_pinaction');
                                if (!empty($pin_info)) {
                                    $this->items_model->_table_name = 'tbl_pinaction';
                                    $this->items_model->delete_multiple(array('module_name' => 'tasks', 'module_id' => $tasks_bugs->task_id));
                                }


                            }
                        }
                        //save data into table.
                        $this->items_model->_table_name = "tbl_task"; // table name
                        $this->items_model->delete_multiple(array('bug_id' => $v_bugs->bug_id));

                    }
                }

                //delete the bugs
                $this->items_model->_table_name = "tbl_bug"; // table name
                $this->items_model->delete_multiple(array('project_id' => $id));

                // delete all project tickets ans tikcets reply with attachment
                $project_tickets = $this->db->where('project_id', $id)->get('tbl_tickets')->result();
                // deleted project comments with file
                if (!empty($project_tickets)) {
                    foreach ($project_tickets as $v_tickets) {

                        $tickets_reply = $this->db->where('tickets_id', $v_tickets->tickets_id)->get('tbl_tickets_replies')->result();
                        if (!empty($tickets_reply)) {
                            foreach ($tickets_reply as $v_ti_reply) {
                                if (!empty($v_ti_reply->attachment)) {
                                    $attachment = json_decode($v_ti_reply->attachment);
                                    foreach ($attachment as $v_file) {
                                        remove_files($v_file->fileName);
                                    }
                                }
                            }
                            //delete data into table.
                            $this->items_model->_table_name = "tbl_tickets_replies"; // table name
                            $this->items_model->delete_multiple(array('tickets_id' => $v_tickets->tickets_id));
                        }

                        if (!empty($v_tickets->upload_file)) {
                            $attachment = json_decode($v_tickets->upload_file);
                            foreach ($attachment as $v_file) {
                                remove_files($v_file->fileName);
                            }
                        }
                    }
                    //delete data into table.
                    $this->items_model->_table_name = "tbl_tickets"; // table name
                    $this->items_model->delete_multiple(array('project_id' => $id));
                }

                // delete all invoice and invoice items
                $project_invoice = $this->db->where('project_id', $id)->get('tbl_invoices')->result();
                if (!empty($project_invoice)) {
                    foreach ($project_invoice as $vp_invoice) {
                        $this->items_model->_table_name = "tbl_items"; // table name
                        $this->items_model->delete_multiple(array('invoices_id' => $vp_invoice->invoices_id));
                    }
                    $this->items_model->_table_name = "tbl_invoices"; // table name
                    $this->items_model->delete_multiple(array('project_id' => $id));
                }

                // delete all estimates and estimates items
                $project_estimate = $this->db->where('project_id', $id)->get('tbl_estimates')->result();
                if (!empty($project_estimate)) {
                    foreach ($project_estimate as $vp_estimate) {
                        $this->items_model->_table_name = "tbl_estimate_items"; // table name
                        $this->items_model->delete_multiple(array('estimates_id' => $vp_estimate->estimates_id));
                    }
                    $this->items_model->_table_name = "tbl_estimates"; // table name
                    $this->items_model->delete_multiple(array('project_id' => $id));
                }
                // delete all estimates and estimates items
                $project_expense = $this->db->where('project_id', $id)->get('tbl_transactions')->result();
                if (!empty($project_expense)) {
                    foreach ($project_expense as $vp_expense) {
                        $account_info = $this->items_model->check_by(array('account_id' => $vp_expense->account_id), 'tbl_accounts');

                        $ac_data['balance'] = $account_info->balance + $vp_expense->amount;
                        $this->items_model->_table_name = "tbl_accounts"; //table name
                        $this->items_model->_primary_key = "account_id";
                        $this->items_model->save($ac_data, $account_info->account_id);

                        $this->items_model->_table_name = "tbl_transactions"; //table name
                        $this->items_model->_primary_key = "transactions_id";
                        $this->items_model->delete($id);
                    }
                }

                //delete the timer from tbl_tasks_timer
                $this->items_model->_table_name = "tbl_tasks_timer"; // table name
                $this->items_model->delete_multiple(array('project_id' => $id));

                $this->items_model->_table_name = 'tbl_pinaction';
                $this->items_model->delete_multiple(array('module_name' => 'project', 'module_id' => $id));

                $this->items_model->_table_name = 'tbl_project';
                $this->items_model->_primary_key = 'project_id';
                $this->items_model->delete($id);

                $type = 'success';
                $message = lang('project_deleted');
            } else {
                $type = 'error';
                $message = lang('error_occurred');
            }
            if (!empty($bulk)) {
                return (array("status" => $type, 'message' => $message));
            }
            echo json_encode(array("status" => $type, 'message' => $message));
        }
        exit();
    }

    public
    function save_project_notes($id)
    {

        $data = $this->items_model->array_from_post(array('notes'));

//save data into table.
        $this->items_model->_table_name = 'tbl_project';
        $this->items_model->_primary_key = 'project_id';
        $id = $this->items_model->save($data, $id);
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'projects',
            'module_field_id' => $id,
            'activity' => 'activity_update_notes',
            'icon' => 'fa-folder-open-o',
            'link' => 'admin/projects/project_details/' . $id . '/8',
            'value1' => $data['notes'],
        );
        // Update into tbl_project
        $this->items_model->_table_name = "tbl_activities"; //table name
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);

        $type = "success";
        $message = lang('update_task');
        set_message($type, $message);
        redirect('admin/projects/project_details/' . $id . '/' . '8');
    }

    public
    function update_project_timer($id = NULL, $action = NULL)
    {
        if (!empty($action)) {
            $t_data['project_id'] = $this->db->where(array('tasks_timer_id' => $id))->get('tbl_tasks_timer')->row()->project_id;
            $activity = 'activity_delete_tasks_timesheet';
            $msg = lang('delete_timesheet');
        } else {
            $activity = ('activity_update_task_timesheet');
            $msg = lang('timer_update');
        }
        if ($action != 'delete_task_timmer') {
            $t_data = $this->items_model->array_from_post(array('project_id', 'start_date', 'start_time', 'end_date', 'end_time'));

            if (empty($t_data['start_date'])) {
                $t_data['start_date'] = date('Y-m-d');
            }
            if (empty($t_data['end_date'])) {
                $t_data['end_date'] = date('Y-m-d');
            }
            if (empty($t_data['start_time'])) {
                $t_data['start_time'] = date('H:i');
            }
            if (empty($t_data['end_time'])) {
                $t_data['end_time'] = date('H:i');
            }
            $data['start_time'] = strtotime($t_data['start_date'] . ' ' . $t_data['start_time']);
            $data['end_time'] = strtotime($t_data['end_date'] . ' ' . $t_data['end_time']);

            $data['reason'] = $this->input->post('reason', TRUE);
            $data['edited_by'] = $this->session->userdata('user_id');

            $data['project_id'] = $t_data['project_id'];
            $data['user_id'] = $this->session->userdata('user_id');

            $this->items_model->_table_name = "tbl_tasks_timer"; //table name
            $this->items_model->_primary_key = "tasks_timer_id";
            if (!empty($id)) {
                $id = $this->items_model->save($data, $id);
            } else {
                $id = $this->items_model->save($data);
            }
            $task_start = $this->items_model->check_by(array('project_id' => $data['project_id']), 'tbl_project');
            $estimate_hours = $task_start->estimate_hours;

            $percentage = $this->items_model->get_estime_time($estimate_hours);
            $logged_hour = $this->items_model->calculate_project('project_hours', $task_start->project_id);
            if ($percentage != 0) {
                $progress = round(($logged_hour / $percentage) * 100);
                if ($progress > 100) {
                    $progress = 100;
                }
                $p_data = array(
                    'progress' => $progress,
                );
                $this->items_model->_table_name = "tbl_project"; //table name
                $this->items_model->_primary_key = "project_id";
                $this->items_model->save($p_data, $data['project_id']);
            }
        } else {
            $this->items_model->set_progress($t_data['project_id']);

            $project_info = $this->items_model->check_by(array('project_id' => $t_data['project_id']), 'tbl_project');

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
                            'description' => 'not_update_timer',
                            'link' => 'admin/projects/project_details/' . $t_data['project_id'] . '/7',
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
                'module_field_id' => $id,
                'activity' => $activity,
                'icon' => 'fa-folder-open-o',
                'link' => 'admin/projects/project_details/' . $t_data['project_id'] . '/7',
                'value1' => $project_info->project_name,
            );
            $this->items_model->_table_name = "tbl_activities"; //table name
            $this->items_model->_primary_key = "activities_id";
            $this->items_model->save($activities);

            $this->items_model->_table_name = "tbl_tasks_timer"; //table name
            $this->items_model->_primary_key = "tasks_timer_id";
            $this->items_model->delete($id);
        }
        if ($action == 'delete_task_timmer') {
            echo json_encode(array("status" => 'success', 'message' => $msg));
            exit();
        } else {
            $type = "success";
            $message = $msg;
            set_message($type, $message);
            redirect('admin/projects/project_details/' . $t_data['project_id'] . '/7');
        }
    }

    public
    function tasks_timer($status, $project_id, $inline = null)
    {
        $task_start = $this->items_model->check_by(array('project_id' => $project_id), 'tbl_project');
        $project_info = $this->items_model->check_by(array('project_id' => $project_id), 'tbl_project');
        $notifiedUsers = array();
        if (!empty($project_info->permission) && $project_info->permission != 'all') {
            $permissionUsers = json_decode($project_info->permission);
            foreach ($permissionUsers as $user => $v_permission) {
                array_push($notifiedUsers, $user);
            }
        } else {
            $notifiedUsers = $this->items_model->allowed_user_id('57');
        }
        if ($status == 'off') {
            // check this user start time or this user is admin
            // if true then off time
            // else do not off time
            $check_user = $this->timer_started_by($project_id);

            if ($check_user == TRUE) {

                $task_logged_time = $this->items_model->task_spent_time_by_id($project_id, true);

                $time_logged = (time() - $task_start->start_time) + $task_logged_time; //time already logged

                $data = array(
                    'timer_status' => $status,
                    'logged_time' => $time_logged,
                    'start_time' => ''
                );
// Update into tbl_task
                $this->items_model->_table_name = "tbl_project"; //table name
                $this->items_model->_primary_key = "project_id";
                $this->items_model->save($data, $project_id);
                // save into tbl_task_timer
                $t_data = array(
                    'project_id' => $project_id,
                    'user_id' => $this->session->userdata('user_id'),
                    'timer_status' => $status,
                    'end_time' => time()
                );
                $tasks_timer_id = timer_status('projects', $project_id, 'on', true);
                // insert into tbl_task_timer
                $this->items_model->_table_name = "tbl_tasks_timer"; //table name
                $this->items_model->_primary_key = "tasks_timer_id";
                $this->items_model->save($t_data, $tasks_timer_id);

                // save into activities
                $activities = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'projects',
                    'module_field_id' => $project_id,
                    'activity' => ('activity_tasks_timer_off'),
                    'icon' => 'fa-folder-open-o',
                    'link' => 'admin/projects/project_details/' . $project_id . '/7',
                    'value1' => $task_start->project_name,
                );
// Update into tbl_project
                $this->items_model->_table_name = "tbl_activities"; //table name
                $this->items_model->_primary_key = "activities_id";
                $this->items_model->save($activities);

                $task_start = $this->items_model->check_by(array('project_id' => $project_id), 'tbl_project');
                $estimate_hours = $task_start->estimate_hours;

                $percentage = $this->items_model->get_estime_time($estimate_hours);
                $logged_hour = $this->items_model->calculate_project('project_hours', $task_start->project_id);
                $progress = 0;
                if ($percentage != 0) {
                    $progress = round(($logged_hour / $percentage) * 100);
                    if ($progress > 100) {
                        $progress = 100;
                    }
                    $p_data = array(
                        'progress' => $progress,
                    );
                    $this->items_model->_table_name = "tbl_project"; //table name
                    $this->items_model->_primary_key = "project_id";
                    $this->items_model->save($p_data, $project_id);
                }

                if (!empty($notifiedUsers)) {
                    foreach ($notifiedUsers as $users) {
                        if ($users != $this->session->userdata('user_id')) {
                            add_notification(array(
                                'to_user_id' => $users,
                                'from_user_id' => true,
                                'description' => 'not_timer_stop',
                                'link' => 'admin/projects/project_details/' . $project_info->project_id . '/7',
                                'value' => lang('project') . ' ' . $project_info->project_name . ' ' . lang('progress') . ' ' . $progress,
                            ));
                        }
                    }
                    show_notification($notifiedUsers);
                }
                $this->items_model->set_progress($t_data['project_id']);
            }
        } else {
            $data = array(
                'timer_status' => $status,
                'timer_started_by' => $this->session->userdata('user_id'),
                'start_time' => time()
            );

// save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'projects',
                'module_field_id' => $project_id,
                'activity' => 'activity_tasks_timer_on',
                'icon' => 'fa-folder-open-o',
                'link' => 'admin/projects/project_details/' . $project_id . '/7',
                'value1' => $task_start->project_name,
            );
// Update into tbl_project
            $this->items_model->_table_name = "tbl_activities"; //table name
            $this->items_model->_primary_key = "activities_id";
            $this->items_model->save($activities);

// Update into tbl_task
            $this->items_model->_table_name = "tbl_project"; //table name
            $this->items_model->_primary_key = "project_id";
            $this->items_model->save($data, $project_id);

            // save into tbl_task_timer
            $t_data = array(
                'project_id' => $project_id,
                'timer_status' => $status,
                'user_id' => $this->session->userdata('user_id'),
                'start_time' => time()
            );
            // insert into tbl_task_timer
            $this->items_model->_table_name = "tbl_tasks_timer"; //table name
            $this->items_model->_primary_key = "tasks_timer_id";
            $this->items_model->save($t_data);

            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => 'not_timer_start',
                            'link' => 'admin/projects/project_details/' . $project_info->project_id . '/7',
                            'value' => lang('project') . ' ' . $project_info->project_name,
                        ));
                    }
                }
                show_notification($notifiedUsers);
            }
        }
        // messages for user
        if (!empty($inline)) {
            return true;
        } else {
            $type = "success";
            $message = lang('task_timer_' . $status);
            set_message($type, $message);
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/projects/project_details/' . $project_info->project_id . '/7');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }

    }

    public
    function timer_started_by($task_id)
    {
        $user_id = $this->session->userdata('user_id');
//        $user_info = $this->items_model->check_by(array('user_id' => $user_id), 'tbl_users');
        $timer_started_info = $this->items_model->check_by(array('project_id' => $task_id), 'tbl_project');
        if ($timer_started_info->timer_started_by == $user_id) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public
    function download_files($uploaded_files_id, $comments = null)
    {

        $this->load->helper('download');
        if (!empty($comments)) {
            if ($uploaded_files_id) {
                $down_data = file_get_contents('uploads/' . $uploaded_files_id); // Read the file's contents
                force_download($uploaded_files_id, $down_data);
            } else {
                echo json_encode(array("status" => 'error', 'message' => lang('error_occurred')));
            }
        } else {
            $uploaded_files_info = $this->items_model->check_by(array('uploaded_files_id' => $uploaded_files_id), 'tbl_task_uploaded_files');
            if (!empty($uploaded_files_info->uploaded_path)) {
                $data = file_get_contents($uploaded_files_info->uploaded_path); // Read the file's contents
                if (!empty($data)) {
                    force_download($uploaded_files_info->file_name, $data);
                } else {
                    echo json_encode(array("status" => 'error', 'message' => lang('error_occurred')));
                }

            } else {
                echo json_encode(array("status" => 'error', 'message' => lang('error_occurred')));

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
            redirect('admin/projects/project_details/' . $attachment_info->project_id . '/4');
        }
    }

    public
    function add_todo_list($module, $id)
    {
        $where = array('user_id' => $this->session->userdata('user_id'), 'module_id' => $id, 'module_name' => $module);
        $already_pinned = $this->items_model->check_by($where, 'tbl_pinaction');
        if (empty($already_pinned)) {
            $this->items_model->_table_name = "tbl_pinaction"; //table name
            $this->items_model->_primary_key = "pinaction_id";
            $this->items_model->save($where);
        }
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('admin/dashboard');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public
    function remove_todo($id)
    {
        // Update into tbl_pinaction
        $this->items_model->_table_name = "tbl_pinaction"; //table name
        $this->items_model->_primary_key = "pinaction_id";
        $this->items_model->delete($id);
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('admin/dashboard');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public
    function export_project($id)
    {
        $data['title'] = lang('export_report');
        $data['project_details'] = $this->items_model->check_by(array('project_id' => $id), 'tbl_project');
        $viewfile = $this->load->view('admin/projects/export_project', $data, TRUE);
//        echo "<pre>";
//        print_r($viewfile);
//        exit();
//        $data['subview'] = $viewfile;
        $this->load->helper('dompdf');
        pdf_create($viewfile, slug_it($data['project_details']->project_name . '-' . lang('details')), 1, 1);
//        $this->load->view('admin/_layout_main', $data); //page load
    }
}
