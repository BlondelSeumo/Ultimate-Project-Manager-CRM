<?php

class Job_Circular extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('job_circular_model');
    }

    public function jobs_posted()
    {
        $data['title'] = lang('job_posted_list');
        //get all training information
        $data['job_post_info'] = $this->job_circular_model->get_permission('tbl_job_circular');
        $data['subview'] = $this->load->view('admin/job_circular/jobs_posted', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function jobs_postedList()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_job_circular';
            $this->datatables->join_table = array('tbl_designations');
            $this->datatables->join_where = array('tbl_designations.designations_id=tbl_job_circular.designations_id');
            $this->datatables->column_order = array('job_title', 'tbl_designations.designations', 'employment_type', 'vacancy_no', 'experience', 'age', 'salary_range', 'posted_date', 'description', 'last_date', 'status');
            $this->datatables->column_search = array('job_title', 'tbl_designations.designations', 'employment_type', 'vacancy_no', 'experience', 'age', 'salary_range', 'posted_date', 'description', 'last_date', 'status');
            $this->datatables->order = array('job_circular_id' => 'desc');

            $fetch_data = $this->datatables->get_datatable_permission();

            $edited = can_action('103', 'edited');
            $deleted = can_action('103', 'deleted');
            $data = array();
            foreach ($fetch_data as $_key => $v_job_post) {
                if (!empty($v_job_post->designations_id)) {
                    $design_info = $this->db->where('designations_id', $v_job_post->designations_id)->get('tbl_designations')->row();
                    if (!empty($design_info)) {
                        $designation = $design_info->designations;
                    } else {
                        $designation = '-';
                    }
                } else {
                    $designation = '-';
                }
                $can_edit = $this->job_circular_model->can_action('tbl_job_circular', 'edit', array('job_circular_id' => $v_job_post->job_circular_id));
                $can_delete = $this->job_circular_model->can_action('tbl_job_circular', 'delete', array('job_circular_id' => $v_job_post->job_circular_id));


                $action = null;
                $sub_array = array();
                $title = null;
                $title .= '<a data-toggle="modal" data-target="#myModal_lg" class="text-info" href="' . base_url() . 'admin/job_circular/view_circular_details/' . $v_job_post->job_circular_id . '">' . $v_job_post->job_title . '</a>';
                $sub_array[] = $title;

                $sub_array[] = $designation;
                $sub_array[] = $v_job_post->vacancy_no;
                $sub_array[] = display_date($v_job_post->last_date);

                $custom_form_table = custom_form_table(14, $v_job_post->job_circular_id);

                if (!empty($custom_form_table)) {
                    foreach ($custom_form_table as $c_label => $v_fields) {
                        $sub_array[] = $v_fields;
                    }
                }
                if ($v_job_post->status == 'unpublished') {
                    $status = '<span class="label label-danger">' . lang('unpublished') . '</span>';
                } else {
                    $status = '<span class="label label-success">' . lang('published') . '</span>';
                }
                $sub_array[] = $status;

                $action .= '<a href="' . base_url() . 'admin/job_circular/jobs_applications/' . $v_job_post->job_circular_id . '" title="' . lang('all_application_for') . '" class="btn btn-purple btn-xs" data-toggle="tooltip"><span
                            class="fa fa-list-alt"></span></a>' . ' ';
                if (!empty($can_edit) && !empty($edited)) {
                    if ($v_job_post->status == 'unpublished') {
                        $action .= btn_publish('admin/job_circular/change_status/published/' . $v_job_post->job_circular_id) . ' ';
                    } else {
                        $action .= btn_unpublish('admin/job_circular/change_status/unpublished/' . $v_job_post->job_circular_id) . ' ';
                    }
                    $action .= '<span data-toggle="tooltip" data-placement="top" title="' . lang('edit') . '"><a href="' . base_url() . 'admin/job_circular/new_jobs_posted/' . $v_job_post->job_circular_id . '"
                       class="btn btn-primary btn-xs" data-toggle="modal" data-placement="top" data-target="#myModal_lg"><i class="fa fa-pencil-square-o"></i></a></span>' . ' ';
                }
                if (!empty($can_delete) && !empty($deleted)) {
                    $action .= ajax_anchor(base_url("admin/job_circular/delete_jobs_posted/" . $v_job_post->job_circular_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                }
                $action .= '<a target="_blank" href="' . base_url() . 'frontend/circular_details/' . $v_job_post->job_circular_id . '"
                       title="' . lang('view_circular_details') . ' & ' . lang('apply_now') . '" class="btn btn-primary btn-xs" data-placement="top" data-toggle="tooltip"><i class="fa fa-paper-plane"></i></a>' . ' ';

                $sub_array[] = $action;
                $data[] = $sub_array;

            }

            render_table($data);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function new_jobs_posted($id = null)
    {
        $data['title'] = lang('new') . ' ' . lang('jobs_posted');
        if (!empty($id)) {
            $can_edit = $this->job_circular_model->can_action('tbl_job_circular', 'edit', array('job_circular_id' => $id));
            if (!empty($can_edit)) {
                $data['job_posted'] = $this->db->where('job_circular_id', $id)->get('tbl_job_circular')->row();

                if (empty($data['job_posted'])) {
                    // messages for user
                    $type = "error";
                    $message = "Not Found!";
                    set_message($type, $message);
                    redirect('admin/job_circular/jobs_posted');
                }
            }
        }
        // get all department info and designation info
        $data['all_dept_info'] = $this->db->get('tbl_departments')->result();
        // get all department info and designation info
        foreach ($data['all_dept_info'] as $v_dept_info) {
            $data['all_department_info'][] = $this->job_circular_model->get_add_department_by_id($v_dept_info->departments_id);
        }

        $data['assign_user'] = $this->job_circular_model->allowed_user('103');

        $data['subview'] = $this->load->view('admin/job_circular/new_jobs_posted', $data, FALSE);
        $this->load->view('admin/_layout_modal_lg', $data); //page load
    }

    public function save_job_posted($id = NULL)
    {
        $created = can_action('103', 'created');
        $edited = can_action('103', 'edited');
        if (!empty($created) || !empty($edited) && !empty($id)) {
            $data = $this->job_circular_model->array_from_post(array('job_title', 'designations_id', 'employment_type', 'vacancy_no', 'experience', 'age', 'salary_range', 'posted_date', 'description', 'last_date', 'status'));
            $designation_info = $this->db->where('designations_id', $data['designations_id'])->get('tbl_designations')->row();
            $permission = $this->input->post('permission', true);
            // update root category
            $where = array('designations_id' => $data['designations_id']);
            // duplicate value check in DB
            if (!empty($id)) { // if id exist in db update data
                $job_circular_id = array('job_circular_id !=' => $id);
            } else { // if id is not exist then set id as null
                $job_circular_id = null;
            }
            if (!empty($permission)) {
                if ($permission == 'everyone') {
                    $assigned = 'all';
                } else {
                    $assigned_to = $this->job_circular_model->array_from_post(array('assigned_to'));
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
                    redirect('admin/job_circular/jobs_posted');
                } else {
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }

            $this->job_circular_model->_table_name = "tbl_job_circular"; // table name
            $this->job_circular_model->_primary_key = "job_circular_id"; // $id
            $return_id = $this->job_circular_model->save($data, $id);

            if (!empty($id)) {
                $activity = 'activity_update_job_posted';
                $msg = lang('job_posted_information_update');
            } else {
                $activity = 'activity_added_job_posted';
                $msg = lang('job_posted_information_saved');
                $id = $return_id;
            }
            save_custom_field(14, $id);

            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'job_circular',
                'module_field_id' => $id,
                'activity' => $activity,
                'icon' => 'fa-ticket',
                'value1' => $data['job_title'] . '[' . $designation_info->designations . ']',
            );

            // Update into tbl_project
            $this->job_circular_model->_table_name = "tbl_activities"; //table name
            $this->job_circular_model->_primary_key = "activities_id";
            $this->job_circular_model->save($activities);
            // messages for user
            $type = "success";
            $message = $msg;
            set_message($type, $message);
        }
        redirect('admin/job_circular/jobs_posted');
    }

    public function delete_jobs_posted($id)
    {
        $deleted = can_action('103', 'deleted');
        if (!empty($deleted)) {
            // check into tbl_job_allocations
            // if id exist delete this
            $check_existing_data = $this->job_circular_model->check_by(array('job_circular_id' => $id), 'tbl_job_appliactions');
            $job_posted_info = $this->job_circular_model->check_by(array('job_circular_id' => $id), 'tbl_job_circular');

            if (empty($check_existing_data)) {
                // save into activities
                $activities = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'job_circular',
                    'module_field_id' => $id,
                    'activity' => 'activity_delete_job_posted',
                    'icon' => 'fa-ticket',
                    'value1' => $job_posted_info->job_title,
                );
                // Update into tbl_project
                $this->job_circular_model->_table_name = "tbl_activities"; //table name
                $this->job_circular_model->_primary_key = "activities_id";
                $this->job_circular_model->save($activities);

                // delete into tbl_job_circular
                $this->job_circular_model->_table_name = "tbl_job_circular"; // table name
                $this->job_circular_model->_primary_key = "job_circular_id"; // $id
                $this->job_circular_model->delete($id);
                // messages for user
                $type = "success";
                $message = lang('job_posted_information_delete');

            } else {
                $type = "error";
                $message = lang('job_posted_information_used');
            }
            set_message($type, $message);
        }
        redirect('admin/job_circular/jobs_posted');
    }

    public
    function change_status($status, $id)
    {
        $edited = can_action('103', 'edited');
        if (!empty($edited)) {
            // if flag == 1 that means it is published to un pubslished
            // else unpublished to pubslished
            $this->job_circular_model->set_action(array('job_circular_id' => $id), array('status' => $status), 'tbl_job_circular');

            $type = "success";
            $message = lang('job_posted_status_change') . ' ' . $status . ' !';
            set_message($type, $message);
        }
        redirect('admin/job_circular/jobs_posted');
    }

    public
    function view_circular_details($id)
    {
        $data['title'] = lang('view_circular_details');
        $data['job_posted'] = $this->db->where('job_circular_id', $id)->get('tbl_job_circular')->row();
        $data['subview'] = $this->load->view('admin/job_circular/circular_details', $data, FALSE);
        $this->load->view('admin/_layout_modal_lg', $data); //page load
    }

    public
    function jobs_posted_pdf($id)
    {
        $data['job_posted'] = $this->db->where('job_circular_id', $id)->get('tbl_job_circular')->row();

        $this->load->helper('dompdf');
        $view_file = $this->load->view('admin/job_circular/jobs_posted_pdf', $data, true);
        pdf_create($view_file, slug_it(lang('jobs_posted') . '- ' . $data['job_posted']->job_title));
    }

    public
    function jobs_applications($id = null)
    {
        $data['title'] = lang('all_jobs_application');
        // get salary template details
        $data['job_appliactions_id'] = $id;
        $data['subview'] = $this->load->view('admin/job_circular/jobs_applications', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function jobs_applicationsList($id = null)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_job_appliactions';
            $this->datatables->join_table = array('tbl_job_circular');
            $this->datatables->join_where = array('tbl_job_circular.job_circular_id=tbl_job_appliactions.job_circular_id');
            $this->datatables->column_order = array('tbl_job_circular.job_title', 'name', 'email', 'mobile', 'application_status', 'apply_date', 'interview_date');
            $this->datatables->column_search = array('tbl_job_circular.job_title', 'name', 'email', 'mobile', 'application_status', 'apply_date', 'interview_date');
            $this->datatables->order = array('tbl_job_appliactions.job_appliactions_id' => 'desc');
            $where = null;
            if (!empty($id)) {
                $where = array('tbl_job_appliactions.job_circular_id' => $id);
            }
            $fetch_data = make_datatables($where);
            $data = array();
            foreach ($fetch_data as $_key => $v_job_application) {

                $action = null;
                $sub_array = array();
                $title = null;
                $title .= '<a data-toggle="modal" data-target="#myModal_lg" class="text-info" href="' . base_url() . 'admin/job_circular/view_circular_details/' . $v_job_application->job_circular_id . '">' . $v_job_application->job_title . '</a>';
                $sub_array[] = $title;

                $sub_array[] = $v_job_application->name;
                $sub_array[] = $v_job_application->email;
                $sub_array[] = $v_job_application->mobile;
                $sub_array[] = display_date($v_job_application->apply_date);

                if ($v_job_application->application_status == 0) {
                    $status = '<span class="label label-warning">' . lang('unread') . '</span>';
                } elseif ($v_job_application->application_status == 1) {
                    $status = '<span class="label label-success">' . lang('approved') . '</span>';
                } elseif ($v_job_application->application_status == 2) {
                    $status = '<span class="label label-primary">' . lang('primary_selected') . '</span>';
                } elseif ($v_job_application->application_status == 3) {
                    $status = '<span class="label label-purple">' . lang('call_for_interview') . '</span>';
                } else {
                    $status = '<span class="label label-danger">' . lang('rejected') . '</span>';
                }
                $sub_array[] = $status;

                $action .= '<a href="' . base_url() . 'admin/job_circular/download_resume/' . $v_job_application->job_appliactions_id . '" title="' . lang('download') . ' ' . lang('resume') . '" class="btn btn-purple btn-xs" data-toggle="tooltip"><span
                            class="fa fa-download"></span></a>' . ' ';

                $action .= '<a href="' . base_url() . 'admin/job_circular/change_application_status/' . $v_job_application->job_appliactions_id . '" title="' . lang('change_status') . '" class="btn btn-success btn-xs" data-toggle="modal" data-target="#myModal"><span
                            class="fa fa-pencil-square-o"></span> ' . lang('status') . '</a>' . ' ';

                $action .= '<a href="' . base_url() . 'admin/job_circular/jobs_application_details/' . $v_job_application->job_appliactions_id . '" title="' . lang('view') . '" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal"><span
                            class="fa fa-list-alt"></span></a>' . ' ';

                $action .= ajax_anchor(base_url("admin/job_circular/delete_jobs_application/" . $v_job_application->job_appliactions_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';

                $sub_array[] = $action;
                $data[] = $sub_array;

            }

            render_table($data, $where);
        } else {
            redirect('admin/dashboard');
        }
    }

    public
    function change_application_status($id)
    {
        $flag = $this->input->post('flag', true);
        if (empty($flag)) {
            $data['title'] = lang('change_status');
            // get salary template deatails
            $data['job_application_info'] = $this->db->where('job_appliactions_id', $id)->get('tbl_job_appliactions')->row();

            $data['subview'] = $this->load->view('admin/job_circular/application_status', $data, FALSE);
            $this->load->view('admin/_layout_modal', $data);
        } else {
            // if flag == 1 that means it is published to un pubslished
            // else unpublished to pubslished
            $status = $this->input->post('status', true);
            $where = array('application_status' => $status);
            if ($status == 3) {
                $send_email = $this->input->post('send_email', true);
                $interview_date = $this->input->post('interview_date', true);
                if (!empty($send_email)) {
                    $where = array('application_status' => $status, 'send_email' => $send_email, 'interview_date' => $interview_date);
                    $this->call_for_interview($id, $interview_date);
                }
            }
            $this->job_circular_model->set_action(array('job_appliactions_id' => $id), $where, 'tbl_job_appliactions');
            // messages for user
            $type = "success";
            $message = lang('job_posted_status_change');
            set_message($type, $message);
            redirect('admin/job_circular/jobs_applications');
        }


    }

    function call_for_interview($id, $interview_date)
    {
        $job_application_info = $this->db->where('job_appliactions_id', $id)->get('tbl_job_appliactions')->row();
        if (!empty($job_application_info)) {
            $job_circular_info = $this->db->where('job_circular_id', $job_application_info->job_circular_id)->get('tbl_job_circular')->row();
            $designation = '-';
            if (!empty($job_circular_info->designations_id)) {
                $design_info = $this->db->where('designations_id', $job_circular_info->designations_id)->get('tbl_designations')->row();
                if (!empty($design_info)) {
                    $designation = $design_info->designations;
                }
            }

            $email_template = email_templates(array('email_group' => 'call_for_interview'));

            $message = $email_template->template_body;
            $subject = $email_template->subject;
            $title = str_replace("{NAME}", $job_application_info->name, $message);
            $job_title = str_replace("{JOB_TITLE}", $job_circular_info->job_title, $title);
            $designation = str_replace("{DESIGNATION}", $designation, $job_title);
            $date = str_replace("{DATE}", strftime(config_item('date_format'), strtotime($interview_date)), $designation);
            $Link = str_replace("{LINK}", base_url() . 'frontend/circular_details/' . $job_application_info->job_circular_id, $date);
            $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);
            $data['message'] = $message;
            $message = $this->load->view('email_template', $data, TRUE);

            $params['subject'] = $subject;
            $params['message'] = $message;
            $params['resourceed_file'] = '';

            $params['recipient'] = $job_application_info->email;
            $this->job_circular_model->send_email($params);
        }
        return true;
    }

    public
    function jobs_application_details($id)
    {
        $data['title'] = lang('jobs_application_details');
        // get salary template deatails
        $data['job_application_info'] = $this->job_circular_model->get_job_application_info($id);
        $data['subview'] = $this->load->view('admin/job_circular/jobs_applications_details', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public
    function view_jobs_application($id)
    {
        $data['title'] = lang('jobs_application_details');
        // get salary template deatails
        $data['job_application_info'] = $this->job_circular_model->get_job_application_info($id);
        $data['subview'] = $this->load->view('admin/job_circular/jobs_applications_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public
    function delete_jobs_application($id)
    {
        $jobs_application = $this->job_circular_model->get_job_application_info($id);
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'job_circular',
            'module_field_id' => $id,
            'activity' => 'activity_delete_job_application',
            'icon' => 'fa-ticket',
            'value1' => $jobs_application->name,
        );
        // Update into tbl_project
        $this->job_circular_model->_table_name = "tbl_activities"; //table name
        $this->job_circular_model->_primary_key = "activities_id";
        $this->job_circular_model->save($activities);

        $this->job_circular_model->_table_name = "tbl_job_appliactions"; // table name
        $this->job_circular_model->_primary_key = "job_appliactions_id"; // $id
        $this->job_circular_model->delete($id);

        // messages for user
        $type = "success";
        $message = lang('deleted_job_application');
        set_message($type, $message);
        redirect('admin/job_circular/jobs_applications');
    }

    public
    function download_resume($id)
    {
        $job_application_info = $this->job_circular_model->get_job_application_info($id);
        $path = file_get_contents($job_application_info->resume); // Read the file's contents
        $resume = explode('/', $job_application_info->resume);
        $this->load->helper('download');
        force_download($job_application_info->name . ' - ' . $resume[1], $path);
    }

}
