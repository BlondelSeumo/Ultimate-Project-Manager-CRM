<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of admistrator
 *
 * @author pc mart ltd
 */
class Tickets extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('tickets_model');
    }

    public function index($action = NULL, $id = NULL)
    {
        // get permission user by menu id
        $data['permission_user'] = $this->tickets_model->all_permission_user('6');

        $data['title'] = "Tickets Details"; //Page title      
        if (!empty($id)) {
            if (is_numeric($id)) {
                $can_edit = $this->tickets_model->can_action('tbl_tickets', 'edit', array('tickets_id' => $id));
                $edited = can_action(6, 'edited');
                if (!empty($can_edit) && !empty($edited)) {
                    $data['tickets_info'] = $this->tickets_model->check_by(array('tickets_id' => $id), 'tbl_tickets');
                }
            }

        }
        $data['dropzone'] = true;
        if ($action == 'edit_tickets' || $action == 'project_tickets') {
            $project_id = $this->uri->segment(6);
            if (!empty($project_id)) {
                $project_info = get_row('tbl_project', array('project_id' => $project_id));
                if ($project_info->permission == 'all') {
                    $data['permission_user'] = $this->tickets_model->allowed_user('57');
                } else {
                    $data['permission_user'] = $this->tickets_model->permitted_allowed_user($project_info->permission);
                }
            }
            $data['active'] = 2;
        } else {
            $data['active'] = 1;
        }
        $data['page'] = lang('tickets');
        $data['sub_active'] = lang('all_tickets');
        if ($action == 'tickets_details') {
            $data['tickets_info'] = $this->tickets_model->check_by(array('tickets_id' => $id), 'tbl_tickets');
            $subview = 'tickets_details';
        } elseif ($action == 'download_file') {
            $this->load->helper('download');
            $file = $this->uri->segment(6);
            if ($id) {
                $down_data = file_get_contents('uploads/' . $file); // Read the file's contents
                force_download($file, $down_data);
            } else {
                $type = "error";
                $message = 'Operation Fieled !';
                set_message($type, $message);
                if (empty($_SERVER['HTTP_REFERER'])) {
                    redirect('admin/tickets');
                } else {
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }
        } elseif ($action == 'changed_ticket_status') {
            $date = date('Y-m-d H:i:s');
            $status = $this->uri->segment(6);
            if (!empty($status)) {
                if ($status == 'closed' && config_item('notify_ticket_reopened') == 'TRUE') {
                    $this->notify_ticket_reopened($id);
                }
                $this->tickets_model->set_action(array('tickets_id' => $id), array('status' => $status), 'tbl_tickets');

            }
            $this->tickets_model->set_action(array('tickets_id' => $id), array('last_reply' => $date), 'tbl_tickets');

            $rdata['body'] = $this->input->post('body', TRUE);

            $rdata['tickets_id'] = $id;
            $rdata['replierid'] = $this->session->userdata('user_id');


            $this->tickets_model->_table_name = 'tbl_tickets_replies';
            $this->tickets_model->_primary_key = 'tickets_replies_id';
            $this->tickets_model->save($rdata);

            $user_info = $this->db->where(array('user_id' => $rdata['replierid']))->get('tbl_users')->row();

            if ($user_info->role_id == '2') {
                $this->get_notify_ticket_reply('admin', $rdata); // Send email to admins
            } else {
                $this->get_notify_ticket_reply('client', $rdata); // Send email to client
            }
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'tickets',
                'module_field_id' => $id,
                'activity' => 'activity_reply_tickets',
                'icon' => 'fa-ticket',
                'link' => 'admin/tickets/index/tickets_details/' . $id,
                'value1' => $rdata['body'],
            );
            // Update into tbl_project
            $this->tickets_model->_table_name = "tbl_activities"; //table name
            $this->tickets_model->_primary_key = "activities_id";
            $this->tickets_model->save($activities);
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/tickets');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }

        } else {
            $subview = 'tickets';
        }

        $data['all_tickets_info'] = $this->tickets_model->get_permission('tbl_tickets');

        $data['subview'] = $this->load->view('admin/tickets/' . $subview, $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function ticketsList($filterBy = null, $search_by = null)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_tickets';
            $this->datatables->join_table = array('tbl_departments');
            $this->datatables->join_where = array('tbl_departments.departments_id=tbl_tickets.departments_id');
            $this->datatables->column_order = array('ticket_code', 'subject', 'reporter', 'priority', 'tbl_departments.deptname', 'status');
            $this->datatables->column_search = array('ticket_code', 'subject', 'reporter', 'priority', 'tbl_departments.deptname', 'status');
            $this->datatables->order = array('tickets_id' => 'desc');
            $where = null;
            if (empty($filterBy) && !empty(admin())) {
                $where = array('status !=' => 'closed');
            }
            if (!empty($search_by)) {
                if ($search_by == 'by_reported') {
                    $where = array('reporter' => $filterBy);
                }
                if ($search_by == 'by_project') {
                    $where = array('project_id' => $filterBy);
                }
                if ($search_by == 'by_department') {
                    $where = array('tbl_tickets.departments_id' => $filterBy);
                }
            } else {
                if ($filterBy == 'assigned_to_me') {
                    $user_id = $this->session->userdata('user_id');
                    $where = $user_id;
                } else if ($filterBy == 'everyone') {
                    $where = array('permission' => 'all');
                } elseif (!empty($filterBy)) {
                    $where = array('status' => $filterBy);
                }
            }
            // get all invoice
            $fetch_data = $this->datatables->get_tickets($filterBy, $search_by);

            $data = array();

            $edited = can_action('6', 'edited');
            $deleted = can_action('6', 'deleted');
            foreach ($fetch_data as $_key => $v_tickets_info) {
                if (!empty($v_tickets_info)) {
                    $action = null;
                    $can_edit = $this->tickets_model->can_action('tbl_tickets', 'edit', array('tickets_id' => $v_tickets_info->tickets_id));
                    $can_delete = $this->tickets_model->can_action('tbl_tickets', 'delete', array('tickets_id' => $v_tickets_info->tickets_id));
                    if ($v_tickets_info->status == 'open') {
                        $s_label = 'danger';
                    } elseif ($v_tickets_info->status == 'closed') {
                        $s_label = 'success';
                    } else {
                        $s_label = 'default';
                    }
                    $dept_info = $this->db->where(array('departments_id' => $v_tickets_info->departments_id))->get('tbl_departments')->row();
                    if (!empty($dept_info)) {
                        $dept_name = $dept_info->deptname;
                    } else {
                        $dept_name = '-';
                    }

                    $sub_array = array();
                    if (!empty($deleted) || !empty($can_delete)) {
                        $sub_array[] = '<div class="checkbox c-checkbox" ><label class="needsclick"> <input value="' . $v_tickets_info->tickets_id . '" type="checkbox"><span class="fa fa-check"></span></label></div>';
                    }
                    $ticket_code = null;
                    $ticket_code .= '<a class="text-info" href="' . base_url() . 'admin/tickets/index/tickets_details/' . $v_tickets_info->tickets_id . '">' . $v_tickets_info->ticket_code . '</a>';
                    $sub_array[] = $ticket_code;

                    $ticket_subject = null;
                    $ticket_subject .= '<a class="text-info" href="' . base_url() . 'admin/tickets/index/tickets_details/' . $v_tickets_info->tickets_id . '">' . $v_tickets_info->subject . '</a>';
                    $sub_array[] = $ticket_subject;
                    $sub_array[] = strftime(config_item('date_format'), strtotime($v_tickets_info->created));
                    if ($this->session->userdata('user_type') == '1') {
                        $reporter = '<a href="#" data-toggle="tooltip" data-placement="top" title="' . fullname($v_tickets_info->reporter) . '" class="pull-left recect_task ">
    <img style="width: 30px;margin-left: 18px;height: 29px;border: 1px solid #aaa;"
         src="' . base_url() . staffImage($v_tickets_info->reporter) . '"
         class="img-circle"></a>';
                        $sub_array[] = $reporter;
                    }
                    $sub_array[] = $dept_name;
                    $sub_array[] = '<span class="label label-' . $s_label . ' "> ' . lang($v_tickets_info->status) . '</span> ';

                    $custom_form_table = custom_form_table(7, $v_tickets_info->tickets_id);
                    if (!empty($custom_form_table)) {
                        foreach ($custom_form_table as $c_label => $v_fields) {
                            $sub_array[] = $v_fields;
                        }
                    }
                    if (!empty($can_edit) && !empty($edited)) {
                        $action .= btn_edit('admin/tickets/index/edit_tickets/' . $v_tickets_info->tickets_id) . ' ';
                    }
                    if (!empty($can_delete) && !empty($deleted)) {
                        $action .= ajax_anchor(base_url("admin/tickets/delete/delete_tickets/$v_tickets_info->tickets_id"), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                    }
                    $action .= btn_view('admin/tickets/index/tickets_details/' . $v_tickets_info->tickets_id) . ' ';

                    $change_status = null;
                    if (!empty($can_edit) && !empty($edited)) {
                        $ch_url = base_url() . 'admin/tickets/change_status/';
                        $change_status .= ' <div class="btn-group" >
        <button class="btn btn-xs btn-default dropdown-toggle" data-toggle = "dropdown" >
                    ' . lang('change') . '<span class="caret" ></span></button>
        <ul class="dropdown-menu animated zoomIn" >';
                        $status_info = get_result('tbl_status');
                        if (!empty($status_info)) {
                            foreach ($status_info as $v_status) {
                                $change_status .= '<li ><a data-toggle="modal" data-target="#myModal" href = "' . $ch_url . $v_tickets_info->tickets_id . '/' . $v_status->status . '" > ' . lang($v_status->status) . ' </a ></li > ';
                            }
                        }
                        $change_status .= '</ul ></div > ';
                        $action .= $change_status;
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

    public
    function create_tickets($id = NULL)
    {

        $data = $this->tickets_model->array_from_post(array('ticket_code', 'subject', 'reporter', 'priority', 'departments_id', 'body'));
        $data['project_id'] = $this->input->post('project_id', true);
        if (empty($id)) {
            $status = $this->input->post('status', true);
            if (!empty($status)) {
                $data['status'] = $status;
                if ($status == 'index') {
                    $data['status'] = 'open';
                }
            } else {
                $data['status'] = 'open';
            }
        }

        $created = can_action(6, 'created');
        $edited = can_action(6, 'edited');

        if (!empty($created) || !empty($edited) && !empty($id)) {

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
                $data['upload_file'] = null;
            }

            $permission = $this->input->post('permission', true);
            if (!empty($permission)) {
                if ($permission == 'everyone') {
                    $assigned = 'all';
                } else {
                    $assigned_to = $this->tickets_model->array_from_post(array('assigned_to'));
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
                    redirect('admin/tickets');
                } else {
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }

            $this->tickets_model->_table_name = 'tbl_tickets';
            $this->tickets_model->_primary_key = 'tickets_id';
            if (!empty($id)) {
                $this->tickets_model->save($data, $id);
            } else {
                $id = $this->tickets_model->save($data, $id);
            }
            save_custom_field(7, $id);
            // send email to reporter
            $this->send_tickets_info_by_email($data);
            // Send email to Client
            $this->send_tickets_info_by_email($data, TRUE);

            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'tickets',
                'module_field_id' => $id,
                'activity' => 'activity_create_tickets',
                'icon' => 'fa-ticket',
                'link' => 'admin/tickets/index/tickets_details/' . $id,
                'value1' => $data['ticket_code'],
            );
            // Update into tbl_project
            $this->tickets_model->_table_name = "tbl_activities"; //table name
            $this->tickets_model->_primary_key = "activities_id";
            $this->tickets_model->save($activities);

            // messages for user
            $type = "success";
            $message = lang('ticket_created');
            set_message($type, $message);
            if (!empty($data['project_id']) && is_numeric($data['project_id'])) {
                redirect('admin/projects/project_details/' . $data['project_id']);
            } else {
                redirect('admin/tickets/index/tickets_details/' . $id);
            }
        } else {
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/tickets');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public function save_tickets_reply($id)
    {
        $date = date('Y-m-d H:i:s');
        $status = $this->uri->segment(6);
        if (!empty($status)) {
            if ($status == 'closed' && config_item('notify_ticket_reopened') == 'TRUE') {
                $this->notify_ticket_reopened($id);
            }
            $this->tickets_model->set_action(array('tickets_id' => $id), array('status' => $status), 'tbl_tickets');
        }
        $this->tickets_model->set_action(array('tickets_id' => $id), array('last_reply' => $date), 'tbl_tickets');

        $rdata['body'] = $this->input->post('body', TRUE);
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
        if (!empty($upload_file)) {
            $rdata['attachment'] = json_encode($upload_file);
        }
        $rdata['tickets_id'] = $id;
        $rdata['replierid'] = $this->session->userdata('user_id');


        $this->tickets_model->_table_name = 'tbl_tickets_replies';
        $this->tickets_model->_primary_key = 'tickets_replies_id';
        $tickets_replies_id = $this->tickets_model->save($rdata);
        if (!empty($tickets_replies_id)) {
            //check this tickets already answer or not
            $ticket_info = $this->db->where(array('tickets_id' => $id, 'status' => 'answered'))->get('tbl_tickets')->row();
            if (empty($ticket_info)) {
                $this->tickets_model->set_action(array('tickets_id' => $id), array('status' => 'answered'), 'tbl_tickets');
            }

            $user_info = $this->db->where(array('user_id' => $rdata['replierid']))->get('tbl_users')->row();

            if ($user_info->role_id == '2') {
                $this->get_notify_ticket_reply('admin', $rdata); // Send email to admins
            } else {
                $this->get_notify_ticket_reply('client', $rdata); // Send email to client
            }
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'tickets',
                'module_field_id' => $id,
                'activity' => 'activity_reply_tickets',
                'icon' => 'fa-ticket',
                'link' => 'admin/tickets/index/tickets_details/' . $id,
                'value1' => $rdata['body'],
            );
            // Update into tbl_project
            $this->tickets_model->_table_name = "tbl_activities"; //table name
            $this->tickets_model->_primary_key = "activities_id";
            $this->tickets_model->save($activities);
            $response_data = "";
            $view_data['ticket_replies'] = $this->db->where(array('tickets_replies_id' => $tickets_replies_id))->order_by('time', 'DESC')->get('tbl_tickets_replies')->result();
            $response_data = $this->load->view("admin/tickets/tickets_reply", $view_data, true);
            echo json_encode(array("status" => 'success', "data" => $response_data, 'message' => lang('tickets_reply_saved')));
            exit();
        } else {
            echo json_encode(array("status" => 'error', 'message' => lang('error_occurred')));
            exit();
        }

    }

    function notify_ticket_reopened($id)
    {
        $ticket_info = $this->db->where(array('tickets_id' => $id))->get('tbl_tickets')->row();
        $email_template = email_templates(array('email_group' => 'ticket_reopened_email'), $ticket_info->reporter, true);

        $designation_info = $this->db->where('departments_id', $ticket_info->departments_id)->get('tbl_designations')->result();
        if (!empty($designation_info)) {
            foreach ($designation_info as $v_designation) {
                $user_info[] = $this->db->where('designations_id', $v_designation->designations_id)->get('tbl_account_details')->row();
            }
        }

        $message = $email_template->template_body;
        $subject = $email_template->subject;

        $RECIPIENT = str_replace("{RECIPIENT}", fullname($ticket_info->reporter), $message);
        $SUBJECT = str_replace("{SUBJECT}", $ticket_info->ticket_code, $RECIPIENT);
        $USER = str_replace("{USER}", fullname(my_id()), $SUBJECT);
        $TicketLink = str_replace("{TICKET_LINK}", base_url() . 'admin/tickets/index/tickets_details/' . $ticket_info->tickets_id, $USER);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $TicketLink);
        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $subject = str_replace("[TICKET_CODE]", '[' . $ticket_info->ticket_code . ']', $subject);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';
        $notifyUser = array();
        if (!empty($user_info)) {
            foreach ($user_info as $v_user) {
                if (!empty($v_user)) {
                    $login_info = $this->tickets_model->check_by(array('user_id' => $v_user->user_id), 'tbl_users');
                    $params['recipient'] = $login_info->email;
                    $this->tickets_model->send_email($params);
                    $notifyUser = array_push($notifyUser, $v_user->user_id);
                    if ($v_user->user_id != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $v_user->user_id,
                            'icon' => 'ticket',
                            'description' => 'not_ticket_assign_to_you',
                            'link' => 'admin/tickets/index/tickets_details/' . $ticket_info->tickets_id,
                            'value' => $ticket_info->ticket_code,
                        ));
                    }
                }
            }
        }
        if (!empty($notifyUser)) {
            show_notification($notifyUser);
        }
        return true;
    }

    public function save_comments_reply($tickets_replies_id)
    {
        $rdata['tickets_id'] = $this->input->post('tickets_id', TRUE);
        $rdata['body'] = $this->input->post('reply_comments', TRUE);
        $rdata['ticket_reply_id'] = $tickets_replies_id;

        $rdata['replierid'] = $this->session->userdata('user_id');

        $this->tickets_model->_table_name = 'tbl_tickets_replies';
        $this->tickets_model->_primary_key = 'tickets_replies_id';
        $tickets_replies_id = $this->tickets_model->save($rdata);
        if (!empty($tickets_replies_id)) {

            $user_info = $this->db->where(array('user_id' => $rdata['replierid']))->get('tbl_users')->row();

            if ($user_info->role_id == '2') {
                $this->get_notify_ticket_reply('admin', $rdata); // Send email to admins
            } else {
                $this->get_notify_ticket_reply('client', $rdata); // Send email to client
            }
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'tickets',
                'module_field_id' => $rdata['tickets_id'],
                'activity' => 'activity_reply_tickets',
                'icon' => 'fa-ticket',
                'link' => 'admin/tickets/index/tickets_details/' . $rdata['tickets_id'],
                'value1' => $rdata['body'],
            );
            // Update into tbl_project
            $this->tickets_model->_table_name = "tbl_activities"; //table name
            $this->tickets_model->_primary_key = "activities_id";
            $this->tickets_model->save($activities);

            $response_data = "";
            $view_data['comment_reply_details'] = $this->db->where(array('tickets_replies_id' => $tickets_replies_id))->order_by('time', 'ASC')->get('tbl_tickets_replies')->result();
            $response_data = $this->load->view("admin/tickets/comments_reply", $view_data, true);
            echo json_encode(array("status" => 'success', "data" => $response_data, 'message' => lang('tickets_reply_saved')));
            exit();

        } else {
            echo json_encode(array("status" => 'error', 'message' => lang('error_occurred')));
            exit();
        }
    }

    function send_tickets_info_by_email($postdata, $client = NULL)
    {
        if (!empty($postdata['reporter'])) {
            $postdata['reporter'] = $postdata['reporter'];
        } else {
            $postdata['reporter'] = $this->session->userdata('user_id');
        }

        $user_login_info = $this->tickets_model->check_by(array('user_id' => $postdata['reporter']), 'tbl_users');
        $ticket_info = $this->tickets_model->check_by(array('ticket_code' => $postdata['ticket_code']), 'tbl_tickets');

        if (!empty($client)) {
            if (!empty($user_login_info->email)) {

                $email_template = email_templates(array('email_group' => 'ticket_client_email'), $postdata['reporter'], true);
                $message = $email_template->template_body;
                $subject = $email_template->subject;

                $client_email = str_replace("{CLIENT_EMAIL}", $user_login_info->email, $message);
                $ticket_code = str_replace("{TICKET_CODE}", $postdata['ticket_code'], $client_email);
                $TicketLink = str_replace("{TICKET_LINK}", base_url() . 'client/tickets/index/tickets_details/' . $ticket_info->tickets_id, $ticket_code);
                $message = str_replace("{SITE_NAME}", config_item('company_name'), $TicketLink);
                $data['message'] = $message;

                $message = $this->load->view('email_template', $data, TRUE);

                $subject = str_replace("[TICKET_CODE]", '[' . $postdata['ticket_code'] . ']', $subject);

                $params['recipient'] = $user_login_info->email;
                $params['subject'] = $subject;
                $params['message'] = $message;
                $params['resourceed_file'] = '';
                $this->tickets_model->send_email($params);

                // send notification to client
                $notifyUser = array($user_login_info->user_id);
                if (!empty($notifyUser)) {
                    foreach ($notifyUser as $v_user) {
                        if ($v_user != $this->session->userdata('user_id')) {
                            add_notification(array(
                                'to_user_id' => $v_user,
                                'icon' => 'ticket',
                                'description' => 'not_ticket_email_to_client',
                                'link' => 'client/tickets/index/tickets_details/' . $ticket_info->tickets_id,
                                'value' => $postdata['ticket_code'],
                            ));
                        }
                    }
                    show_notification($notifyUser);
                }
            }
        } else {
            $email_template = email_templates(array('email_group' => 'ticket_staff_email'), $user_login_info->user_id, true);

            $designation_info = $this->db->where('departments_id', $postdata['departments_id'])->get('tbl_designations')->result();
            if (!empty($designation_info)) {
                foreach ($designation_info as $v_designation) {
                    $user_info[] = $this->db->where('designations_id', $v_designation->designations_id)->get('tbl_account_details')->row();
                }
            }

            $message = $email_template->template_body;
            $subject = $email_template->subject;

            $TicketCode = str_replace("{TICKET_CODE}", $postdata['ticket_code'], $message);
            $ReporterEmail = str_replace("{REPORTER_EMAIL}", !empty($user_login_info->email) ? $user_login_info->email : $this->session->userdata('email'), $TicketCode);
            $TicketLink = str_replace("{TICKET_LINK}", base_url() . 'admin/tickets/index/tickets_details/' . $ticket_info->tickets_id, $ReporterEmail);
            $message = str_replace("{SITE_NAME}", config_item('company_name'), $TicketLink);
            $data['message'] = $message;
            $message = $this->load->view('email_template', $data, TRUE);

            $subject = str_replace("[TICKET_CODE]", '[' . $postdata['ticket_code'] . ']', $subject);

            $params['subject'] = $subject;
            $params['message'] = $message;
            $params['resourceed_file'] = '';
            $notifyUser = array();
            if (!empty($user_info)) {
                foreach ($user_info as $v_user) {
                    if (!empty($v_user)) {
                        $login_info = $this->tickets_model->check_by(array('user_id' => $v_user->user_id), 'tbl_users');
                        $params['recipient'] = $login_info->email;
                        $this->tickets_model->send_email($params);
                        if (!empty($v_user->user_id)) {
                            $notifyUser = array_push($notifyUser, $v_user->user_id);
                            if ($v_user->user_id != $this->session->userdata('user_id')) {
                                add_notification(array(
                                    'to_user_id' => $v_user->user_id,
                                    'icon' => 'ticket',
                                    'description' => 'not_ticket_assign_to_you',
                                    'link' => 'admin/tickets/index/tickets_details/' . $ticket_info->tickets_id,
                                    'value' => $postdata['ticket_code'],
                                ));
                            }
                        }
                    }
                }
            }
            if (!empty($notifyUser)) {
                show_notification($notifyUser);
            }
        }
    }

    function get_notify_ticket_reply($users, $postdata)
    {
        $email_template = email_templates(array('email_group' => 'ticket_reply_email'));
        $tickets_info = $this->tickets_model->check_by(array('tickets_id' => $postdata['tickets_id']), 'tbl_tickets');

        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $status = $tickets_info->status;

        $TicketCode = str_replace("{TICKET_CODE}", $tickets_info->ticket_code, $message);
        $TicketStatus = str_replace("{TICKET_STATUS}", ucfirst($status), $TicketCode);
        $TicketLink = str_replace("{TICKET_LINK}", base_url() . 'client/tickets/index/tickets_details/' . $tickets_info->tickets_id, $TicketStatus);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $TicketLink);

        $subject = str_replace("[TICKET_CODE]", '[' . $tickets_info->ticket_code . ']', $subject);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';

        switch ($users) {
            case 'admin':
                $designation_info = $this->db->where('departments_id', $tickets_info->departments_id)->get('tbl_designations')->result();
                if (!empty($designation_info)) {
                    foreach ($designation_info as $v_designation) {
                        $user_info[] = $this->db->where('designations_id', $v_designation->designations_id)->get('tbl_account_details')->row();
                    }
                }
                $notifyUser = array();
                if (!empty($user_info)) {
                    foreach ($user_info as $v_user) {
                        $login_info = $this->tickets_model->check_by(array('user_id' => $v_user->user_id), 'tbl_users');
                        $params['recipient'] = $login_info->email;
                        $this->tickets_model->send_email($params);

                        $notifyUser = array_push($notifyUser, $v_user->user_id);
                        if ($v_user->user_id != $this->session->userdata('user_id')) {
                            add_notification(array(
                                'to_user_id' => $v_user->user_id,
                                'icon' => 'ticket',
                                'description' => 'not_ticket_reply',
                                'link' => 'admin/tickets/index/tickets_details/' . $tickets_info->tickets_id,
                                'value' => $tickets_info->ticket_code,
                            ));
                        }
                    }
                }

                if (!empty($notifyUser)) {
                    show_notification($notifyUser);
                }
            default:
                $login_info = $this->tickets_model->check_by(array('user_id' => $tickets_info->reporter), 'tbl_users');
                if (!empty($login_info)) {
                    $params['recipient'] = $login_info->email;
                    if ($login_info->role_id == 2) {
                        $url = 'client';
                    } else {
                        $url = 'admin';
                    }
                    $this->tickets_model->send_email($params);
                    if ($login_info->user_id != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $login_info->user_id,
                            'icon' => 'ticket',
                            'description' => 'not_ticket_reply',
                            'link' => $url . '/tickets/index/tickets_details/' . $tickets_info->tickets_id,
                            'value' => $tickets_info->ticket_code,
                        ));
                    }
                    $notifyUser = array($login_info->user_id);
                    show_notification($notifyUser);
                }
        }
    }

    public
    function change_status($id, $status)
    {

        $can_edit = $this->tickets_model->can_action('tbl_tickets', 'edit', array('tickets_id' => $id));

        $edited = can_action(6, 'edited');
        if (!empty($can_edit) && !empty($edited)) {
            $data['id'] = $id;
            $data['status'] = $status;
            $data['modal_subview'] = $this->load->view('admin/tickets/_modal_change_status', $data, FALSE);
            $this->load->view('admin/_layout_modal', $data);
        } else {
            set_message('error', lang('there_in_no_value'));
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/tickets');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public function bulk_delete()
    {
        $selected_id = $this->input->post('ids', true);
        if (!empty($selected_id)) {
            foreach ($selected_id as $id) {
                $result[] = $this->delete('delete_tickets', $id, true);
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

    public
    function delete($action, $id, $replay_id = NULL)
    {
        if ($action == 'delete_ticket_replay') {
            $comments_info = $this->tickets_model->check_by(array('tickets_replies_id' => $replay_id), 'tbl_tickets_replies');
            if (!empty($comments_info->attachment)) {
                $attachment = json_decode($comments_info->attachment);
                foreach ($attachment as $v_file) {
                    remove_files($v_file->fileName);
                }
            }
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'tickets',
                'module_field_id' => $id,
                'activity' => 'activity_comment_deleted',
                'icon' => 'fa-ticket',
                'link' => 'admin/tickets/index/tickets_details/' . $id,
                'value1' => $comments_info->body,
            );
            // Update into tbl_project
            $this->tickets_model->_table_name = "tbl_activities"; //table name
            $this->tickets_model->_primary_key = "activities_id";
            $this->tickets_model->save($activities);

            $this->tickets_model->_table_name = 'tbl_tickets_replies';
            $this->tickets_model->delete_multiple(array('ticket_reply_id' => $replay_id));

            $this->tickets_model->_table_name = 'tbl_tickets_replies';
            $this->tickets_model->_primary_key = 'tickets_replies_id';
            $this->tickets_model->delete($replay_id);

            echo json_encode(array("status" => 'success', 'message' => lang('ticket_reply_deleted')));
            exit();
        }
        if ($action == 'delete_tickets') {

            $tik_info = $this->tickets_model->check_by(array('tickets_id' => $id), 'tbl_tickets');
            $deleted = can_action(6, 'deleted');
            if (!empty($deleted)) {

                $all_replies_info = $this->db->where(array('tickets_id' => $id))->get('tbl_tickets_replies')->result();

                if (!empty($all_replies_info)) {
                    foreach ($all_replies_info as $v_replies) {
                        $attachment = json_decode($v_replies->attachment);
                        foreach ($attachment as $v_file) {
                            remove_files($v_file->fileName);
                        }
                    }
                }

                $comments_info = $this->tickets_model->check_by(array('tickets_id' => $id), 'tbl_tickets');
                if (!empty($comments_info->upload_file)) {
                    $attachment = json_decode($comments_info->upload_file);
                    foreach ($attachment as $v_file) {
                        remove_files($v_file->fileName);
                    }
                }
                // save into activities
                $activities = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'tickets',
                    'module_field_id' => $id,
                    'activity' => 'activity_tickets_deleted',
                    'icon' => 'fa-ticket',
//                    'link' => 'admin/tickets/index/tickets_details/' . $comments_info->tickets_id,
                    'value1' => (!empty($tik_info->ticket_code) ? $tik_info->ticket_code : ''),
                );
                // Update into tbl_project
                $this->tickets_model->_table_name = "tbl_activities"; //table name
                $this->tickets_model->_primary_key = "activities_id";
                $this->tickets_model->save($activities);

                $this->tickets_model->_table_name = 'tbl_tickets_replies';
                $this->tickets_model->delete_multiple(array('tickets_id' => $id));

                $this->tickets_model->_table_name = 'tbl_pinaction';
                $this->tickets_model->delete_multiple(array('module_name' => 'tickets', 'module_id' => $id));


                $this->tickets_model->_table_name = 'tbl_tickets';
                $this->tickets_model->_primary_key = 'tickets_id';
                $this->tickets_model->delete($id);
                $type = 'success';
                $message = lang('ticket_deleted');
            } else {
                $type = 'error';
                $message = lang('error_occurred');
            }
            if (!empty($replay_id)) {
                return (array("status" => $type, 'message' => $message));
            }
            echo json_encode(array("status" => $type, 'message' => $message));
            exit();
        }
    }

}
