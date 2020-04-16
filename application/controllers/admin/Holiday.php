<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Holiday extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('global_model');

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

    public function index($flag = NULL, $id = NULL)
    {
        $data['title'] = lang('holiday');

        $this->global_model->_table_name = "tbl_holiday"; //table name
        $this->global_model->_order_by = "holiday_id";
        // get holiday list by id
        if (!empty($id)) {
            $data['holiday_list'] = $this->global_model->get_by(array('holiday_id' => $id,), TRUE);
            if (empty($data['holiday_list'])) {
                $type = "error";
                $message = lang('no_record_found');
                set_message($type, $message);
                redirect('admin/settings/holiday_list');
            }
        }// click add holiday theb show
        if (!empty($flag)) {
            $data['active_add_holiday'] = $flag;
        }
        // active check with current month
        $data['current_month'] = date('m');
        if ($this->input->post('year', TRUE)) { // if input year
            $data['year'] = $this->input->post('year', TRUE);
        } else { // else current year
            $data['year'] = date('Y'); // get current year
        }
        // get all holiday list by year and month
        $data['all_holiday_list'] = $this->get_holiday_list($data['year']);  // get current year
        // retrive all data from db
        $data['subview'] = $this->load->view('admin/holiday/holiday_list', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function add_holiday($id = null)
    {
        // active check with current month
        $data['current_month'] = date('m');
        if ($this->input->post('year', TRUE)) { // if input year
            $data['year'] = $this->input->post('year', TRUE);
        } else { // else current year
            $data['year'] = date('Y'); // get current year
        }
        $edited = can_action('71', 'edited');
        if (!empty($id) && !empty($edited)) {
            $data['holiday_list'] = $this->db->where('holiday_id', $id)->get('tbl_holiday')->row();
            if (empty($data['holiday_list'])) {
                $type = "error";
                $message = "No Record Found";
                set_message($type, $message);
                redirect('admin/holiday');
            }
        }
        $data['modal_subview'] = $this->load->view('admin/holiday/_modal_add_holiday', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function get_holiday_list($year)
    {// this function is to create get monthy recap report
        for ($i = 1; $i <= 12; $i++) { // query for months
            if ($i >= 1 && $i <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                $start_date = $year . "-" . '0' . $i . '-' . '01';
                $end_date = $year . "-" . '0' . $i . '-' . '31';
            } else {
                $start_date = $year . "-" . $i . '-' . '01';
                $end_date = $year . "-" . $i . '-' . '31';
            }
            $get_holiday_list[$i] = $this->global_model->get_holiday_list_by_date($start_date, $end_date); // get all report by start date and in date
        }
        return $get_holiday_list; // return the result
    }

    public function save_holiday($id = NULL)
    {
        $created = can_action('71', 'created');
        $edited = can_action('71', 'edited');
        if (!empty($created) || !empty($edited) && !empty($id)) {
            $this->global_model->_table_name = "tbl_holiday"; //table name
            $this->global_model->_primary_key = "holiday_id";    //id
            // input data
            $data = $this->global_model->array_from_post(array('event_name', 'description', 'start_date', 'end_date', 'location', 'color')); //input post

            // dublicacy check into database
            if (!empty($id)) {
                $holiday_id = array('holiday_id !=' => $id);
            } else {
                $holiday_id = null;
            }
            $where = array('event_name' => $data['event_name'], 'start_date' => $data['start_date']); // where
            // check holiday by where
            // if not empty show alert message else save data
            $check_holiday = $this->global_model->check_update('tbl_holiday', $where, $holiday_id);

            if (!empty($check_holiday)) {
                $type = "error";
                $message = lang('this_information_exist');
                set_message($type, $message);
            } else {
                if (!empty($id)) {
                    $activities = lang('active_event_updated');
                } else {
                    $activities = lang('active_event_saved');
                }
                $id = $this->global_model->save($data, $id);
                // messages for user
                $type = "success";
                $message = lang('holiday_information_saved');
                set_message($type, $message);
            }

            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'holiday',
                'module_field_id' => $id,
                'activity' => $activities,
                'icon' => 'fa-ticket',
                'value1' => $data['event_name'],
            );
            // Update into tbl_project
            $this->global_model->_table_name = "tbl_activities"; //table name
            $this->global_model->_primary_key = "activities_id";
            $this->global_model->save($activities);
        } else {
            $type = "error";
            $message = "No Record Found";
            set_message($type, $message);
        }
        redirect('admin/holiday'); //redirect page
    }

    public function delete_holiday($id)
    { // delete holiday list by id
        $deleted = can_action('71', 'deleted');
        if (!empty($deleted)) {
            $check_holiday = $this->global_model->check_by(array('holiday_id' => $id), 'tbl_holiday');
            $this->global_model->_table_name = "tbl_holiday"; //table name
            $this->global_model->_primary_key = "holiday_id";    //id
            $this->global_model->delete($id);
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'holiday',
                'module_field_id' => $id,
                'activity' => 'activity_delete_holiday',
                'icon' => 'fa-ticket',
                'value1' => $check_holiday->event_name,
            );
// Update into tbl_project
            $this->global_model->_table_name = "tbl_activities"; //table name
            $this->global_model->_primary_key = "activities_id";
            $this->global_model->save($activities);

            $type = "success";
            $message = lang('holoday_information_delete');
            set_message($type, $message);
        } else {
            $type = "error";
            $message = "No Record Found";
            set_message($type, $message);
        }
        redirect('admin/holiday'); //redirect page
    }
}
