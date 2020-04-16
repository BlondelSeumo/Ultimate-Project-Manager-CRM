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
class Tickets extends Client_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tickets_model');
    }

    public function index($action = NULL, $id = NULL)
    {
        $data['title'] = "Tickets Details"; //Page title   
        $data['page'] = lang('tickets');
        $data['breadcrumbs'] = lang('tickets');
        $data['sub'] = 5;

        if (!empty($id)) {
            $data['tickets_info'] = $this->tickets_model->check_by(array('tickets_id' => $id), 'tbl_tickets');
            $user_id = $this->session->userdata('user_id');
            if ($user_id != $data['tickets_info']->reporter) {
                redirect('client/tickets');
            }
        }
        if ($action == 'edit_tickets' || $action == 'project_tickets') {
            $data['active'] = 2;
        } else {
            $data['active'] = 1;
        }
        $data['page'] = lang('tickets');
        $data['sub_active'] = lang('all_tickets');
        if ($action == 'tickets_details') {
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
                redirect($_SERVER['HTTP_REFERER']);
            }
        } elseif ($action == 'save_reply') {
            $status = $this->uri->segment(6);
            if (!empty($status)) {
                $this->tickets_model->set_action(array('tickets_id' => $id), array('status' => $status), 'tbl_tickets');
            }
            $rdata['body'] = $this->input->post('body', TRUE);
            if (!empty($_FILES['attachment']['name']['0'])) {
                $old_path_info = $this->input->post('upload_path');
                if (!empty($old_path_info)) {
                    foreach ($old_path_info as $old_path) {
                        unlink($old_path);
                    }
                }
                $mul_val = $this->tickets_model->multi_uploadAllType('attachment');
                $rdata['attachment'] = json_encode($mul_val);
            }

            $rdata['tickets_id'] = $id;
            $rdata['replierid'] = $this->session->userdata('user_id');

            $this->tickets_model->_table_name = 'tbl_tickets_replies';
            $this->tickets_model->_primary_key = 'tickets_replies_id';
            $this->tickets_model->save($rdata);

            //check this tickets already answer or not
            $ticket_info = $this->db->where(array('tickets_id' => $id, 'status' => 'answered'))->get('tbl_tickets')->row();
            if (!empty($ticket_info)) {
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
                'value1' => $rdata['body'],
            );
            // Update into tbl_project
            $this->tickets_model->_table_name = "tbl_activities"; //table name
            $this->tickets_model->_primary_key = "activities_id";
            $this->tickets_model->save($activities);
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $subview = 'tickets';
        }
        $this->tickets_model->_table_name = 'tbl_tickets';
        $this->tickets_model->_order_by = 'tickets_id';
        $data['all_tickets_info'] = $this->tickets_model->get_by(array('reporter' => $this->session->userdata('user_id')), FALSE);

        $user_id = $this->session->userdata('user_id');
        $user_info = $this->tickets_model->check_by(array('user_id' => $user_id), 'tbl_users');
        $data['role'] = $user_info->role_id;
        $data['dropzone'] = true;

        $data['subview'] = $this->load->view('client/tickets/' . $subview, $data, TRUE);
        $this->load->view('client/_layout_main', $data); //page load
    }

    public function ticketsList($filterBy = null)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_tickets';
            $this->datatables->join_table = array('tbl_departments');
            $this->datatables->join_where = array('tbl_departments.departments_id=tbl_tickets.departments_id');
            $this->datatables->column_order = array('ticket_code', 'subject', 'reporter', 'priority', 'tbl_departments.deptname', 'status');
            $this->datatables->column_search = array('ticket_code', 'subject', 'reporter', 'priority', 'tbl_departments.deptname', 'status');
            $this->datatables->order = array('tickets_id' => 'desc');

            $where = array('reporter' => $this->session->userdata('user_id'));
            if (!empty($filterBy)) {
                $where = array('status' => $filterBy, 'reporter' => $this->session->userdata('user_id'));
            }
            // get all invoice
            $fetch_data = make_datatables($where);
            $data = array();
            foreach ($fetch_data as $_key => $v_tickets_info) {
                $action = null;
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
                $ticket_code = null;
                $ticket_code .= '<a class="text-info" href="' . base_url() . 'client/tickets/index/tickets_details/' . $v_tickets_info->tickets_id . '">' . $v_tickets_info->ticket_code . '</a>';
                $sub_array[] = $ticket_code;

                $ticket_subject = null;
                $ticket_subject .= '<a class="text-info" href="' . base_url() . 'client/tickets/index/tickets_details/' . $v_tickets_info->tickets_id . '">' . $v_tickets_info->subject . '</a>';
                $sub_array[] = $ticket_subject;
                $sub_array[] = strftime(config_item('date_format'), strtotime($v_tickets_info->created));
                $sub_array[] = $dept_name;
                $sub_array[] = '<span class="label label-' . $s_label . ' "> ' . lang($v_tickets_info->status) . '</span> ';
                $custom_form_table = custom_form_table(7, $v_tickets_info->tickets_id);
                if (!empty($custom_form_table)) {
                    foreach ($custom_form_table as $c_label => $v_fields) {
                        $sub_array[] = $v_fields;
                    }
                }
                $action .= btn_view('client/tickets/index/tickets_details/' . $v_tickets_info->tickets_id) . ' ';
                $sub_array[] = $action;
                $data[] = $sub_array;
            }
            render_table($data, $where);
        } else {
            redirect('client/dashboard');
        }
    }

    public function create_tickets($id = NULL)
    {
        $data = $this->tickets_model->array_from_post(array('ticket_code', 'subject', 'priority', 'departments_id', 'body'));
        $data['status'] = 'open';
        $data['reporter'] = $this->session->userdata('user_id');
        $data['project_id'] = $this->input->post('project_id', true);

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
        $data['permission'] = 'all';

        $this->tickets_model->_table_name = 'tbl_tickets';
        $this->tickets_model->_primary_key = 'tickets_id';
        $id = $this->tickets_model->save($data, $id);

        save_custom_field(7, $id);

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
        redirect('client/tickets/index/tickets_details/' . $id);
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
        } else {
            $email_template = email_templates(array('email_group' => 'ticket_staff_email'), $postdata['reporter'], true);

            $designation_info = $this->db->where('departments_id', $postdata['departments_id'])->get('tbl_designations')->result();
            if (!empty($designation_info)) {
                foreach ($designation_info as $v_designation) {
                    $user_info[] = $this->db->where('designations_id', $v_designation->designations_id)->get('tbl_account_details')->row();
                }
            }

            $message = $email_template->template_body;
            $subject = $email_template->subject;

            $TicketCode = str_replace("{TICKET_CODE}", $postdata['ticket_code'], $message);
            $ReporterEmail = str_replace("{REPORTER_EMAIL}", $user_login_info->email, $TicketCode);
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

                        $notifyUser = array_push($notifyUser, $v_user->user_id);
                        if ($v_user != $this->session->userdata('user_id')) {
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
                        if ($v_user != $this->session->userdata('user_id')) {
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
                $params['recipient'] = $login_info->email;
                $this->tickets_model->send_email($params);
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

    public function change_status($id, $status)
    {
        $data['id'] = $id;
        $data['status'] = $status;
        $data['modal_subview'] = $this->load->view('client/tickets/_modal_change_status', $data, FALSE);
        $this->load->view('client/_layout_modal', $data);
    }

    public function update_status($id, $status)
    {

        $this->tickets_model->set_action(array('tickets_id' => $id), array('status' => $status, 'comment' => $this->input->post('comment', TRUE)), 'tbl_tickets');
        // messages for user
        $type = "success";
        $message = lang('ticket_status');
        set_message($type, $message);
        redirect('client/tickets');
    }

    public function answered()
    {
        $data['title'] = 'Answerd Ticket';
        $data['page'] = lang('tickets');
        $data['breadcrumbs'] = lang('tickets');
        $data['sub'] = 1;

        $data['active'] = 1;
        $this->tickets_model->_table_name = 'tbl_tickets';
        $this->tickets_model->_order_by = 'tickets_id';
        $data['all_tickets_info'] = $this->tickets_model->get_by(array('status' => 'answered', 'reporter' => $this->session->userdata('user_id')), FALSE);

        $user_id = $this->session->userdata('user_id');
        $user_info = $this->tickets_model->check_by(array('user_id' => $user_id), 'tbl_users');
        $data['role'] = $user_info->role_id;

        $data['subview'] = $this->load->view('client/tickets/tickets', $data, TRUE);
        $this->load->view('client/_layout_main', $data); //page load
    }

    public function closed()
    {
        $data['sub'] = 4;
        $data['title'] = 'Answerd Ticket';
        $data['page'] = lang('tickets');
        $data['breadcrumbs'] = lang('tickets');
        $data['active'] = 1;
        $this->tickets_model->_table_name = 'tbl_tickets';
        $this->tickets_model->_order_by = 'tickets_id';
        $data['all_tickets_info'] = $this->tickets_model->get_by(array('status' => 'closed', 'reporter' => $this->session->userdata('user_id')), FALSE);

        $user_id = $this->session->userdata('user_id');
        $user_info = $this->tickets_model->check_by(array('user_id' => $user_id), 'tbl_users');
        $data['role'] = $user_info->role_id;

        $data['subview'] = $this->load->view('client/tickets/tickets', $data, TRUE);
        $this->load->view('client/_layout_main', $data); //page load
    }

    public function open()
    {
        $data['sub'] = 2;
        $data['title'] = 'Answerd Ticket';
        $data['page'] = lang('tickets');
        $data['breadcrumbs'] = lang('tickets');
        $data['active'] = 1;
        $this->tickets_model->_table_name = 'tbl_tickets';
        $this->tickets_model->_order_by = 'tickets_id';
        $data['all_tickets_info'] = $this->tickets_model->get_by(array('status' => 'open', 'reporter' => $this->session->userdata('user_id')), FALSE);

        $user_id = $this->session->userdata('user_id');
        $user_info = $this->tickets_model->check_by(array('user_id' => $user_id), 'tbl_users');
        $data['role'] = $user_info->role_id;

        $data['subview'] = $this->load->view('client/tickets/tickets', $data, TRUE);
        $this->load->view('client/_layout_main', $data); //page load
    }

    public function in_progress()
    {
        $data['sub'] = 3;
        $data['title'] = 'Answerd Ticket';
        $data['page'] = lang('tickets');
        $data['breadcrumbs'] = lang('tickets');
        $data['active'] = 1;
        $this->tickets_model->_table_name = 'tbl_tickets';
        $this->tickets_model->_order_by = 'tickets_id';
        $data['all_tickets_info'] = $this->tickets_model->get_by(array('status' => 'in_progress', 'reporter' => $this->session->userdata('user_id')), FALSE);

        $user_id = $this->session->userdata('user_id');
        $user_info = $this->tickets_model->check_by(array('user_id' => $user_id), 'tbl_users');
        $data['role'] = $user_info->role_id;

        $data['subview'] = $this->load->view('client/tickets/tickets', $data, TRUE);
        $this->load->view('client/_layout_main', $data); //page load
    }

    public function delete($action, $id, $replay_id = NULL)
    {
        if ($action == 'delete_ticket_replay') {
            $this->tickets_model->_table_name = 'tbl_tickets_replies';
            $this->tickets_model->_primary_key = 'tickets_replies_id';
            $this->tickets_model->delete($replay_id);
            // messages for user            
            redirect('client/tickets/index/tickets_details/' . $id);
        }
    }

}
