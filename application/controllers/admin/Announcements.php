<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Announcements extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('announcements_model');

    }

    public function index($id = NULL)
    {

        $data['title'] = lang('all') . ' ' . lang('announcements');
        if ($id) {
            $data['announcements'] = $this->db->where('announcements_id', $id)->get('tbl_announcements')->row();
            if (empty($data['announcements'])) {
                $type = "error";
                $message = "No Record Found";
                set_message($type, $message);
                redirect('admin/announcements');
            }
        }
        $data['all_announcements'] = $this->db->get('tbl_announcements')->result();

        $data['subview'] = $this->load->view('admin/announcements/all_announcements', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function new_announcements($id = null)
    {
        $data['title'] = lang('new') . ' ' . lang('announcements'); //Page title

        if (!empty($id)) {
            $edited = can_action('100', 'edited');
            if (!empty($edited)) {
                $data['announcements'] = $this->db->where('announcements_id', $id)->get('tbl_announcements')->row();
            }
            if (empty($data['announcements'])) {
                $type = "error";
                $message = "No Record Found";
                set_message($type, $message);
                redirect('admin/announcements/create_announcements');
            }
        }
        $data['subview'] = $this->load->view('admin/announcements/new_announcements', $data, FALSE);
        $this->load->view('admin/_layout_modal_lg', $data); //page load
    }

    public function announcementsList()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_announcements';
            $this->datatables->join_table = array('	tbl_account_details');
            $this->datatables->join_where = array('tbl_account_details.user_id=tbl_announcements.user_id');
            $this->datatables->column_order = array('title', 'description', 'start_date', 'end_date', 'tbl_account_details.fullname',);
            $this->datatables->column_search = array('title', 'description', 'start_date', 'end_date', 'tbl_account_details.fullname',);
            $this->datatables->order = array('announcements_id' => 'desc');

            $fetch_data = make_datatables();

            $edited = can_action('100', 'edited');
            $deleted = can_action('100', 'deleted');
            $data = array();
            foreach ($fetch_data as $_key => $v_announcements) {
                $action = null;
                $sub_array = array();
                $sub_array[] = $v_announcements->title;
                $sub_array[] = fullname($v_announcements->user_id);
                $sub_array[] = display_date($v_announcements->start_date);
                $sub_array[] = display_date($v_announcements->end_date);
                $custom_form_table = custom_form_table(16, $v_announcements->announcements_id);
                if (!empty($custom_form_table)) {
                    foreach ($custom_form_table as $c_label => $v_fields) {
                        $sub_array[] = $v_fields;
                    }
                }
                if ($v_announcements->status == 'unpublished') {
                    $status = '<span class="label label-danger">' . lang('unpublished') . ' </span>';
                } else {
                    $status = '<span class="label label-success"> ' . lang('published') . ' </span>';
                }
                $sub_array[] = $status;

                $action .= btn_view_modal('admin/announcements/announcements_details/' . $v_announcements->announcements_id) . ' ';
                if (!empty($edited)) {
                    $action .= '<a href="' . base_url() . 'admin/announcements/new_announcements/' . $v_announcements->announcements_id . '"
                               class="btn btn-primary btn-xs" title="' . lang('edit') . '" data-toggle="modal"
                               data-target="#myModal_lg"><span class="fa fa-pencil-square-o"></span></a>  ';
                }
                if (!empty($deleted)) {
                    $action .= ajax_anchor(base_url('admin/announcements/delete_announcements/' . $v_announcements->announcements_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                }

                $sub_array[] = $action;
                $data[] = $sub_array;
            }

            render_table($data);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function save_announcements($id = NULL)
    {
        $created = can_action('100', 'created');
        $edited = can_action('100', 'edited');
        if (!empty($created) || !empty($edited) && !empty($id)) {
            $data = $this->announcements_model->array_from_post(array(
                'title',
                'description',
                'start_date',
                'end_date',
                'all_client',
                'status',
            ));

            if (empty($data['status'])) {
                $data['status'] = 'unpublished';
            }
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
                $data['attachment'] = json_encode($upload_file);
            } else {
                $data['attachment'] = null;
            }

            $data['user_id'] = $this->session->userdata('user_id');

            $this->announcements_model->_table_name = "tbl_announcements"; // table name
            $this->announcements_model->_primary_key = "announcements_id"; // $id
            $return_id = $this->announcements_model->save($data, $id);

            save_custom_field(16, $return_id);

            if (!empty($id)) {
                $activity = 'activity_update_announcements';
                $msg = lang('announcements_information_update');
            } else {
                $activity = 'activity_added_announcements';
                $msg = lang('announcements_information_saved');

                if ($data['all_client'] == 1) {
                    $all_users = $this->db->get('tbl_users')->result();
                } else {
                    $all_users = $this->db->where('role_id !=', '2')->get('tbl_users')->result();
                }
                $announcements_email = config_item('announcements_email');
                if (!empty($announcements_email) && $announcements_email == 1) {

                    $email_template = email_templates(array('email_group' => 'new_notice_published'));
                    $message = $email_template->template_body;
                    $subject = $email_template->subject;

                    $title = str_replace("{TITLE}", $data['title'], $message);
                    $Link = str_replace("{LINK}", base_url() . 'admin/announcements/view_announcements_details/' . $return_id, $title);
                    $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);
                    $data['message'] = $message;
                    $message = $this->load->view('email_template', $data, TRUE);

                    $params['subject'] = $subject;

                    $params['resourceed_file'] = '';

                    foreach ($all_users as $v_user) {
                        $profile = $this->db->where('user_id', $v_user->user_id)->get('tbl_account_details')->row();
                        $user = str_replace("{NAME}", $profile->fullname, $message);
                        $params['message'] = $user;
                        $params['recipient'] = $v_user->email;
                        $this->announcements_model->send_email($params);
                    }
                }
                $notifyUser = array();
                if (!empty($all_users)) {
                    foreach ($all_users as $v_user) {
                        if ($v_user->user_id != $this->session->userdata('user_id')) {
                            if ($v_user->role_id == 2) {
                                $url = 'client/';
                            } else {
                                $url = 'admin/';
                            }
                            array_push($notifyUser, $v_user->user_id);
                            add_notification(array(
                                'to_user_id' => $v_user->user_id,
                                'description' => 'not_new_notice',
                                'icon' => 'bullhorn',
                                'link' => $url . 'announcements/view_announcements_details/' . $return_id,
                                'value' => $data['title'],
                            ));
                        }
                    }
                }
                if (!empty($notifyUser)) {
                    show_notification($notifyUser);
                }
            }
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'announcements',
                'module_field_id' => $id,
                'activity' => $activity,
                'icon' => 'fa-ticket',
                'value1' => $data['title'],
            );

            // Update into tbl_project
            $this->announcements_model->_table_name = "tbl_activities"; //table name
            $this->announcements_model->_primary_key = "activities_id";
            $this->announcements_model->save($activities);

            // messages for user
            $type = "success";
            $message = $msg;
            set_message($type, $message);
        }
        redirect('admin/announcements');
    }

    public function download_files($id, $fileName)
    {
        $appl_info = $this->announcements_model->check_by(array('announcements_id' => $id), 'tbl_announcements');
        if (empty($appl_info)) {
            $type = "error";
            $message = "No Record Found";
            set_message($type, $message);
            redirect('admin/announcements');
        }
        $this->load->helper('download');
        if (!empty($appl_info->attachment)) {
            $down_data = file_get_contents('uploads/' . $fileName); // Read the file's contents
            force_download($fileName, $down_data);
        }
    }

    public function announcements_details($id)
    {
        $data['title'] = lang('announcements_details'); //Page title

        $this->announcements_model->_table_name = "tbl_announcements"; // table name
        $this->announcements_model->_order_by = "announcements_id"; // $id
        $data['announcements_details'] = $this->announcements_model->get_by(array('announcements_id' => $id), TRUE);
        if (empty($data['announcements_details'])) {
            $type = "error";
            $message = "No Record Found";
            set_message($type, $message);
            redirect('admin/announcements');
        }
        $this->announcements_model->_primary_key = 'announcements_id';
        $updata['view_status'] = '1';
        $this->announcements_model->save($updata, $id);
        $data['subview'] = $this->load->view('admin/announcements/announcements_details', $data, FALSE);
        $this->load->view('admin/_layout_modal_lg', $data); //page load
    }

    public function view_announcements_details($id)
    {
        $data['title'] = lang('announcements_details'); //Page title

        $this->announcements_model->_table_name = "tbl_announcements"; // table name
        $this->announcements_model->_order_by = "announcements_id"; // $id
        $data['announcements_details'] = $this->announcements_model->get_by(array('announcements_id' => $id), TRUE);
        if (empty($data['announcements_details'])) {
            $type = "error";
            $message = "No Record Found";
            set_message($type, $message);
            redirect('admin/announcements');
        }
        $this->announcements_model->_primary_key = 'announcements_id';
        $updata['view_status'] = '1';
        $this->announcements_model->save($updata, $id);
        $data['subview'] = $this->load->view('admin/announcements/announcements_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function delete_announcements($id = NULL)
    {
        $deleted = can_action('100', 'deleted');
        if (!empty($deleted)) {
            $announcements_info = $this->announcements_model->check_by(array('announcements_id' => $id), 'tbl_announcements');
            if (empty($announcements_info)) {
                $type = "error";
                $message = "No Record Found";
                set_message($type, $message);
                redirect('admin/announcements');
            }
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'announcements',
                'module_field_id' => $id,
                'activity' => 'activity_delete_announcements',
                'icon' => 'fa-ticket',
                'value1' => $announcements_info->title,
            );

            // Update into tbl_project
            $this->announcements_model->_table_name = "tbl_activities"; //table name
            $this->announcements_model->_primary_key = "activities_id";
            $this->announcements_model->save($activities);

            $this->announcements_model->_table_name = "tbl_announcements";
            $this->announcements_model->_primary_key = "announcements_id";
            $this->announcements_model->delete($id);;

            // messages for user
            $type = "success";
            $message = lang('announcements_information_delete');
            set_message($type, $message);
        }
        redirect('admin/announcements');
    }

}
