<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of employee
 *
 * @author Ashraf
 */
class Award extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('award_model');
    }

    public function index($id = NULL)
    {
        $data['title'] = lang('employee_award');
        // retrive all data from department table
        $data['all_employee'] = $this->award_model->get_all_employee();
        /// edit and update get employee award info
        if (!empty($id) && is_numeric($id)) {
            $data['active'] = 2;
            $data['award_info'] = $this->award_model->get_employee_award_by_id($id);
        } elseif (!empty($id) && !is_numeric($id)) {
            $data['switch'] = 1;
            // get all_employee_award_info
            $data['all_employee_award_info'] = $this->award_model->get_employee_award_by_id();
        } else {
            $data['active'] = 2;
        }
        // active check with current month
        $data['current_month'] = date('m');
        if ($this->input->post('year', TRUE)) { // if input year
            $data['year'] = $this->input->post('year', TRUE);
        } else { // else current year
            $data['year'] = date('Y'); // get current year
        }
        // get all expense list by year and month
        $data['all_employee_award'] = $this->get_employee_award($data['year']);

        $data['subview'] = $this->load->view('admin/award/award_list', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function get_employee_award($year, $month = NULL)
    {// this function is to create get monthy recap report
        if (!empty($month)) {
            $employee_award = get_result('tbl_employee_award', array('award_date' => $month)); // get all report by start date and in date
        } else {
            for ($i = 1; $i <= 12; $i++) { // query for months
                if ($i >= 1 && $i <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                    $month = $year . "-" . '0' . $i;
                } else {
                    $month = $year . "-" . $i;
                }
                $employee_award[$i] = get_result('tbl_employee_award', array('award_date' => $month)); // get all report by start date and in date
            }
        }
        return $employee_award; // return the result
    }

    public function awardList($id = null, $user = null)
    {
//        if ($this->input->is_ajax_request()) {
        $this->load->model('datatables');
        $this->datatables->table = 'tbl_employee_award';
//        $this->datatables->join_table = 'tbl_account_details';
//        $this->datatables->join_where = array('tbl_account_details.user_id=tbl_employee_award.user_id');
        $this->datatables->column_order = array('award_name', 'gift_item', 'award_amount', 'award_date', 'given_date');
        $this->datatables->column_search = array('award_name', 'gift_item', 'award_amount', 'award_date', 'given_date');
        $this->datatables->order = array('tbl_employee_award.employee_award_id' => 'desc');

        $where = array();
        if (!empty($id) && empty($user)) {
            $where = array('tbl_employee_award.employee_award_id' => $id);
        } elseif (!empty($id) && !empty($user)) {
            $where = array('tbl_employee_award.user_id' => $id);
        }

        $fetch_data = make_datatables($where);

        $edited = can_action('99', 'edited');
        $deleted = can_action('99', 'deleted');
        $data = array();
        foreach ($fetch_data as $_key => $v_award_info) {

            $action = null;
            $sub_array = array();
            if (!empty($v_award_info)) {
                $staff_details = get_staff_details($v_award_info->user_id);
                $sub_array[] = $staff_details->employment_id;
                $sub_array[] = $staff_details->fullname;
                $sub_array[] = $v_award_info->award_name;
                $sub_array[] = $v_award_info->gift_item;
                $sub_array[] = display_money($v_award_info->award_amount, default_currency());
                $sub_array[] = date('M Y', strtotime($v_award_info->award_date));
                $sub_array[] = display_date($v_award_info->given_date);

                if (!empty($edited)) {
                    $action .= '<a href="' . base_url() . 'admin/award/give_award/' . $v_award_info->employee_award_id . '"
                               class="btn btn-primary btn-xs" title="' . lang('edit') . '" data-toggle="modal"
                               data-target="#myModal"><span class="fa fa-pencil-square-o"></span></a>  ';
                }
                if (!empty($deleted)) {
                    $action .= ajax_anchor(base_url('admin/award/delete_employee_award/' . $v_award_info->employee_award_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                }
                $sub_array[] = $action;
                $data[] = $sub_array;
            }
        }

        render_table($data);
//        } else {
//            redirect('admin/dashboard');
//        }
    }

    public function give_award($id = NULL)
    {
        $data['title'] = lang('give_award');
        // retrive all data from department table
        $data['all_employee'] = $this->award_model->get_all_employee();
        /// edit and update get employee award info
        if (!empty($id)) {
            $data['award_info'] = $this->award_model->get_employee_award_by_id($id);
        }
        // get all_employee_award_info
        $data['all_employee_award_info'] = $this->award_model->get_employee_award_by_id();

        $data['subview'] = $this->load->view('admin/award/give_award', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function save_employee_award($id = NULL)
    {
        $created = can_action('99', 'created');
        $edited = can_action('99', 'edited');
        if (!empty($created) || !empty($edited) && !empty($id)) {
            $data = $this->award_model->array_from_post(array('award_name', 'user_id', 'gift_item', 'award_amount', 'award_date', 'given_date'));

            $this->award_model->_table_name = "tbl_employee_award"; // table name
            $this->award_model->_primary_key = "employee_award_id"; // $id
            $this->award_model->save($data, $id);

            if (!empty($id)) {
                $activity = 'activity_update_a_award';
                $msg = lang('award_information_saved');
                $description = 'not_award_received';
            } else {
                $activity = 'activity_added_a_award';
                $msg = lang('award_information_update');
                $description = 'not_award_update';
            }
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'award',
                'module_field_id' => $id,
                'activity' => $activity,
                'icon' => 'fa-trophy',
                'value1' => $data['award_name'],
                'value2' => date('Y M', strtotime($data['award_date'])),
            );

            // Update into tbl_project
            $this->award_model->_table_name = "tbl_activities"; //table name
            $this->award_model->_primary_key = "activities_id";
            $this->award_model->save($activities);
            $profile_info = $this->award_model->check_by(array('user_id' => $data['user_id']), 'tbl_account_details');
            $user_info = $this->award_model->check_by(array('user_id' => $data['user_id']), 'tbl_users');
            if (empty($id)) {
                $award_email = config_item('award_email');
                if (!empty($award_email) && $award_email == 1) {
                    $email_template = email_templates(array('email_group' => 'award_email'), $data['user_id'], true);
                    $message = $email_template->template_body;
                    $subject = $email_template->subject;
                    $username = str_replace("{NAME}", $profile_info->fullname, $message);
                    $award_name = str_replace("{AWARD_NAME}", $data['award_name'], $username);
                    $award_date = str_replace("{MONTH}", date('M Y', strtotime($data['award_date'])), $award_name);
                    $message = str_replace("{SITE_NAME}", config_item('company_name'), $award_date);
                    $data['message'] = $message;
                    $message = $this->load->view('email_template', $data, TRUE);

                    $params['subject'] = $subject;
                    $params['message'] = $message;
                    $params['resourceed_file'] = '';
                    $params['recipient'] = $user_info->email;
                    $this->award_model->send_email($params);

                }
                $notifyUser = array($user_info->user_id);
                if (!empty($notifyUser)) {
                    foreach ($notifyUser as $v_user) {
                        add_notification(array(
                            'to_user_id' => $v_user,
                            'description' => $description,
                            'icon' => 'trophy',
                            'link' => 'admin/award',
                            'value' => date('M Y', strtotime($data['award_date'])),
                        ));
                    }
                }
                if (!empty($notifyUser)) {
                    show_notification($notifyUser);
                }

            }
            // messages for user
            $type = "success";
            $message = $msg;
            set_message($type, $message);
        }
        redirect('admin/award'); //redirect page
    }

    public function delete_employee_award($id = NULL)
    {
        $deleted = can_action('99', 'deleted');
        if (!empty($deleted)) {
            $award_info = $this->db->where('employee_award_id', $id)->get('tbl_employee_award')->row();
            if (empty($award_info)) {
                $type = "error";
                $message = "No Record Found";
                set_message($type, $message);
                redirect('admin/award');
            }
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'award',
                'module_field_id' => $id,
                'activity' => 'activity_delete_award',
                'icon' => 'fa-trophy',
                'value1' => $award_info->award_name,
                'value2' => date('Y M', strtotime($award_info->award_date)),
            );

            // Update into tbl_project
            $this->award_model->_table_name = "tbl_activities"; //table name
            $this->award_model->_primary_key = "activities_id";
            $this->award_model->save($activities);


            $this->award_model->_table_name = "tbl_employee_award"; // table name
            $this->award_model->_primary_key = "employee_award_id"; // $id
            $this->award_model->delete($id); // delete

            // messages for user
            $type = "success";
            $message = lang('award_information_delete');
            set_message($type, $message);
        }
        redirect('admin/award'); //redirect page
    }

    public function employee_award_pdf($year, $month)
    {
        if ($month >= 1 && $month <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
            $month = $year . "-" . '0' . $month;
        } else {
            $month = $year . "-" . $month;
        }
        $data['employee_award_info'] = $this->get_employee_award($year, $month);
        if (empty($data['employee_award_info'])) {
            $type = "error";
            $message = "No Record Found";
            set_message($type, $message);
            redirect('admin/award');
        }
        $this->load->helper('dompdf');
        $view_file = $this->load->view('admin/award/employee_award_pdf', $data, true);
        pdf_create($view_file, slug_it(lang('employee_award')));
    }

}
