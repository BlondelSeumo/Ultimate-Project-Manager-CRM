<?php

/**
 * Description of training
 *
 * @author Ashraf
 */
class Training extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('training_model');
    }

    public function index($id = NULL)
    {
        $data['title'] = lang('all') . ' ' . lang('training');
        //get all training information
        $data['all_training_info'] = $this->training_model->get_permission('tbl_training');
        $data['subview'] = $this->load->view('admin/training/trainings', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function new_training($id = null)
    {
        $data['title'] = lang('new') . ' ' . lang('training');
        if (!empty($id)) {
            $can_edit = $this->training_model->can_action('tbl_training', 'edit', array('training_id' => $id));
            $edited = can_action('101', 'edited');
            if (!empty($can_edit) && !empty($edited)) {
                $data['training_info'] = $this->training_model->get_all_training_info($id);
            }
        }
        $data['assign_user'] = $this->training_model->allowed_user('101');
        // get all employee info
        $data['all_employee'] = $this->training_model->get_all_employee();
        $data['subview'] = $this->load->view('admin/training/new_training', $data, FALSE);
        $this->load->view('admin/_layout_modal_lg', $data); //page load
    }

    public function trainingList()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_training';
            $this->datatables->join_table = array('	tbl_account_details');
            $this->datatables->join_where = array('tbl_account_details.user_id=tbl_training.user_id');
            $this->datatables->column_order = array('tbl_account_details.fullname', 'training_name', 'vendor_name', 'start_date', 'finish_date', 'training_cost', 'status', 'performance');
            $this->datatables->column_search = array('tbl_account_details.fullname', 'training_name', 'vendor_name', 'start_date', 'finish_date', 'training_cost', 'status', 'performance');
            $this->datatables->order = array('training_id' => 'desc');
            $fetch_data = $this->datatables->get_datatable_permission();

            $edited = can_action('101', 'edited');
            $deleted = can_action('101', 'deleted');
            $data = array();
            foreach ($fetch_data as $_key => $v_training) {
                $profile_info = $this->db->where('user_id', $v_training->user_id)->get('tbl_account_details')->row();
                $can_edit = $this->training_model->can_action('tbl_training', 'edit', array('training_id' => $v_training->training_id));
                $can_delete = $this->training_model->can_action('tbl_training', 'delete', array('training_id' => $v_training->training_id));
                if (!empty($profile_info->fullname)) {
                    $name = $profile_info->fullname . (!empty($profile_info->employment_id) ? ' (' . $profile_info->employment_id . ')' : ' ');
                } else {
                    $name = '-';
                }

                $action = null;
                $sub_array = array();
                $sub_array[] = $name;
                $sub_array[] = $v_training->training_name;
                $sub_array[] = $v_training->vendor_name;
                $sub_array[] = display_date($v_training->start_date);
                $sub_array[] = display_date($v_training->finish_date);
                $custom_form_table = custom_form_table(15, $v_training->training_id);
                if (!empty($custom_form_table)) {
                    foreach ($custom_form_table as $c_label => $v_fields) {
                        $sub_array[] = $v_fields;
                    }
                }
                if ($v_training->status == '0') {
                    $status = '<span class="label label-warning">' . lang('pending') . ' </span>';
                } elseif ($v_training->status == '1') {
                    $status = '<span class="label label-info">' . lang('started') . '</span>';
                } elseif ($v_training->status == '2') {
                    $status = '<span class="label label-success"> ' . lang('completed') . ' </span>';
                } else {
                    $status = '<span class="label label-danger"> ' . lang('terminated ') . '</span>';
                }
                $sub_array[] = $status;

                if (!empty($can_edit) && !empty($edited)) {
                    $action .= '<a href="' . base_url() . 'admin/training/new_training/' . $v_training->training_id . '"
                               class="btn btn-primary btn-xs" title="' . lang('edit') . '" data-toggle="modal"
                               data-target="#myModal_large"><span class="fa fa-pencil-square-o"></span></a>  ';
                }
                if (!empty($can_delete) && !empty($deleted)) {
                    $action .= ajax_anchor(base_url('admin/training/delete_training/' . $v_training->training_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                }
                $action .= btn_view_modal('admin/training/training_details/' . $v_training->training_id) . ' ';

                $sub_array[] = $action;
                $data[] = $sub_array;
            }

            render_table($data);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function save_training($id = NULL)
    {
        $created = can_action('101', 'created');
        $edited = can_action('101', 'edited');
        if (!empty($created) || !empty($edited) && !empty($id)) {
            $data = $this->training_model->array_from_post(array(
                'user_id',
                'training_name',
                'vendor_name',
                'start_date',
                'finish_date',
                'training_cost',
                'status',
                'performance',
                'remarks'));

            $upload_file = array();
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
                            $up_data = array(
                                "fileName" => $new_file_name,
                                "path" => "uploads/" . $new_file_name,
                                "fullPath" => getcwd() . "/uploads/" . $new_file_name,
                                "ext" => '.' . end($file_ext),
                                "size" => round($size, 2),
                                "is_image" => $is_image,
                            );
                            array_push($upload_file, $up_data);
                        }
                    }
                }
            }

            $fileName = $this->input->post('fileName', true);
            $path = $this->input->post('path', true);
            $fullPath = $this->input->post('fullPath', true);
            $size = $this->input->post('size', true);
            $is_image = $this->input->post('is_image', true);

            if (!empty($fileName)) {
                foreach ($fileName as $key => $name) {
                    $old['fileName'] = $name;
                    $old['path'] = $path[$key];
                    $old['fullPath'] = $fullPath[$key];
                    $old['size'] = $size[$key];
                    $old['is_image'] = $is_image[$key];

                    array_push($upload_file, $old);
                }
            }
            if (!empty($upload_file)) {
                $data['upload_file'] = json_encode($upload_file);
            } else {
                $data['upload_file'] = '';
            }

            if (empty($id)) {
                $data['assigned_by'] = $this->session->userdata('user_id');
            }
            $permission = $this->input->post('permission', true);
            if (!empty($permission)) {
                if ($permission == 'everyone') {
                    $assigned = 'all';
                } else {
                    $assigned_to = $this->training_model->array_from_post(array('assigned_to'));
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
                    redirect('admin/training');
                } else {
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }

            //save data into table.
            $this->training_model->_table_name = "tbl_training"; // table name
            $this->training_model->_primary_key = "training_id"; // $id
            $return_id = $this->training_model->save($data, $id);

            save_custom_field(15, $return_id);

            if (!empty($id)) {
                $activity = 'activity_update_training';
                $msg = lang('training_information_update');
            } else {
                $activity = 'activity_added_training';
                $msg = lang('training_information_saved');

                $user_info = $this->training_model->check_by(array('user_id' => $data['user_id']), 'tbl_users');

                $training_email = config_item('training_email');
                if (!empty($training_email) && $training_email == 1) {
                    $email_template = email_templates(array('email_group' => 'new_training_email'), $data['user_id'], true);
                    $message = $email_template->template_body;
                    $subject = $email_template->subject;
                    $title = str_replace("{TRAINING_NAME}", $data['training_name'], $message);
                    $assigned_by = str_replace("{ASSIGNED_BY}", $this->session->userdata('name'), $title);
                    $Link = str_replace("{LINK}", base_url() . 'admin/training/view_training_details/' . $return_id, $assigned_by);
                    $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);
                    $data['message'] = $message;
                    $message = $this->load->view('email_template', $data, TRUE);

                    $params['subject'] = $subject;
                    $params['message'] = $message;
                    $params['resourceed_file'] = '';

                    $params['recipient'] = $user_info->email;
                    $this->training_model->send_email($params);
                }
                $notifyUser = array($user_info->user_id);
                if (!empty($notifyUser)) {
                    foreach ($notifyUser as $v_user) {
                        add_notification(array(
                            'to_user_id' => $v_user,
                            'description' => 'not_new_training',
                            'icon' => 'suitcase',
                            'link' => 'admin/training/view_training_details/' . $return_id,
                            'value' => lang('by') . ' ' . $data['training_name'],
                        ));
                    }
                }
                if (!empty($notifyUser)) {
                    show_notification($notifyUser);
                }
            }
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'training',
                'module_field_id' => $return_id,
                'activity' => $activity,
                'icon' => 'fa-suitcase',
                'link' => 'admin/training/view_training_details/' . $return_id,
                'value1' => $data['training_name'],
            );

            // Update into tbl_project
            $this->training_model->_table_name = "tbl_activities"; //table name
            $this->training_model->_primary_key = "activities_id";
            $this->training_model->save($activities);


            $type = "success";
            $message = $msg;
            set_message($type, $message);
        }
        redirect('admin/training');
    }

    public function view_training_details($id = NULL)
    {
        $data['title'] = lang('training_details');
        //get all training information
        $data['training_info'] = $this->training_model->get_all_training_info($id);

        $data['subview'] = $this->load->view('admin/training/training_detail', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function training_details($id = NULL)
    {
        $data['title'] = lang('training_details');
        //get all training information
        $data['training_info'] = $this->training_model->get_all_training_info($id);

        $data['subview'] = $this->load->view('admin/training/training_detail', $data, FALSE);
        $this->load->view('admin/_layout_modal_lg', $data);
    }

    public function delete_training($id = NULL)
    {
        $deleted = can_action('101', 'deleted');
        $can_delete = $this->training_model->can_action('tbl_training', 'delete', array('training_id' => $id));
        if (!empty($can_delete) && !empty($deleted)) {

            $training_info = $training_info = $this->db->where('training_id', $id)->get('tbl_training')->row();
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'training',
                'module_field_id' => $id,
                'activity' => 'activity_delete_training',
                'icon' => 'fa-suitcase',
                'value1' => $training_info->training_name,
            );

            // Update into tbl_project
            $this->training_model->_table_name = "tbl_activities"; //table name
            $this->training_model->_primary_key = "activities_id";
            $this->training_model->save($activities);


            $this->training_model->_table_name = "tbl_training"; // table name
            $this->training_model->_primary_key = "training_id"; // $id
            $this->training_model->delete($id); // delete

            // messages for user
            $type = "success";
            $message = lang('training_information_delete');
        } else {
            $type = "error";
            $message = 'Operation Field!';
        }
        set_message($type, $message);
        redirect('admin/training');
    }

    public function training_pdf($id)
    {
        $data['training_info'] = $this->training_model->get_all_training_info($id);

        $this->load->helper('dompdf');
        $view_file = $this->load->view('admin/training/training_pdf', $data, true);
        pdf_create($view_file, slug_it(lang('training_details') . '- ' . $data['training_info']->fullname));
    }

    public function download_file($id)
    {
        $this->load->helper('download');
        $file = $this->uri->segment(5);

        if ($id) {
            $down_data = file_get_contents('uploads/' . $file); // Read the file's contents
            force_download($file, $down_data);
        } else {
            $type = "error";
            $message = 'Operation Fieled !';
            set_message($type, $message);
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/training');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

}
