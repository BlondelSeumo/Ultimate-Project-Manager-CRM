<?php

class Performance extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('performance_model');
    }

    public function performance_indicator($id = NULL)
    {
        $data['title'] = lang('performance_indicator');
        // get all department info and designation info
        $this->performance_model->_table_name = "tbl_departments"; //table name
        $this->performance_model->_order_by = "departments_id";
        $data['all_dept_info'] = $this->performance_model->get();
        // get all department info and designation info
        foreach ($data['all_dept_info'] as $v_dept_info) {
            $data['all_department_info'][] = $this->performance_model->get_add_department_by_id($v_dept_info->departments_id);
        }


        //get all performance indicator information
        $data['all_indicator_info'] = $this->performance_model->get_all_indicator_info();

        if ($id) { // retrive data from db by id
            $data['active'] = 2;
            //get all performance indicator information
            $data['indicator_info_by_id'] = $this->performance_model->get_all_indicator_info($id);

        } else {
            $data['active'] = 1;
        }

        $data['subview'] = $this->load->view('admin/performance/indicator', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function save_performance_indicator($id = NULL)
    {

        $edited = can_action('86', 'edited');
        $created = can_action('86', 'created');
        if (!empty($created) || !empty($edited) && !empty($id)) {
            $data = $this->performance_model->array_from_post(array(
                'designations_id',
                'customer_experiece_management',
                'marketing',
                'management',
                'administration',
                'presentation_skill',
                'quality_of_work',
                'efficiency',
                'integrity',
                'professionalism',
                'team_work',
                'critical_thinking',
                'conflict_management',
                'attendance',
                'ability_to_meed_deadline',
            ));

            $designation = $this->db->where('designations_id', $data['designations_id'])->get('tbl_designations')->row()->designations;

            if ($id) {
                $activity = lang('activity_performance_indicator_updated');
                $msg = lang('indicator_update');
            } else {
                $check_indicator = $this->performance_model->check_update('tbl_performance_indicator', $where = array('designations_id' => $data['designations_id']));

                if (!empty($check_indicator)) {
                    $type = "error";
                    $message = lang('performance_indicator_exists');
                    set_message($type, $message);
                    redirect('admin/performance/performance_indicator');
                } else {
                    $activity = lang('activity_performance_indicator_saved');
                    $msg = lang('indicator_saved');
                }
            }

            $this->performance_model->_table_name = "tbl_performance_indicator"; // table name
            $this->performance_model->_primary_key = "performance_indicator_id"; // $id
            $this->performance_model->save($data, $id);

            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'performance',
                'module_field_id' => $id,
                'activity' => $activity,
                'value1' => $designation
            );
            $this->performance_model->_table_name = 'tbl_activities';
            $this->performance_model->_primary_key = 'activities_id';
            $this->performance_model->save($activity);

            // messages for user
            $type = "success";
            $message = $msg;
            set_message($type, $message);
        }
        redirect('admin/performance/performance_indicator');
    }

    public function indicator_details($id)
    {

        $data['performance_indicator_details'] = $this->db->where('designations_id', $id)->get('tbl_performance_indicator')->row();

        $data['modal_subview'] = $this->load->view('admin/performance/_modal_indicator_details', $data, FALSE);
        $this->load->view('admin/_layout_modal_lg', $data);
    }

    public function give_performance_appraisal($id = NULL)
    {
        $data['title'] = lang('give_performance_appraisal');

        // get all_employee
        $data['all_employee'] = $this->performance_model->get_all_employee();

        //get designation id from input
        $data['user_id'] = $this->input->post('user_id',true);
        $data['appraisal_month'] = $this->input->post('appraisal_month');

        if ($data['user_id']) {
            $data['indicator_flag'] = 1;
            $user_info = $this->db->where('user_id', $data['user_id'])->get('tbl_account_details')->row();
            //get all indicator values
            $data['performance_indicator_details'] = $this->db->where('designations_id', $user_info->designations_id)->get('tbl_performance_indicator')->row();

            $where = array('user_id' => $data['user_id'], 'appraisal_month' => $data['appraisal_month']);
            $data['get_appraisal_info'] = $this->performance_model->check_by($where, 'tbl_performance_apprisal');
            //to give user notification that already once appraisal is given.
            if (!empty($data['get_appraisal_info'])) {
                $data['appraisal_once_given'] = 1;
            }
        }

        if ($id) {
            //get all indicator values for user          
            $data['get_appraisal_info'] = $this->performance_model->get_appraisal_value_by_id($id);

            // to block unwanted id pass
            if (!empty($data['get_appraisal_info']->designations_id)) {
                $data['indicator_flag'] = 1;
                //get all indicator values
                $data['performance_indicator_details'] = $this->db->where('designations_id', $data['get_appraisal_info']->designations_id)->get('tbl_performance_indicator')->row();
            }
        }
        $data['subview'] = $this->load->view('admin/performance/give_appraisal', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function save_performance_appraisal($id = NULL)
    {
        $edited = can_action('88', 'edited');
        $created = can_action('88', 'created');
        if (!empty($created) || !empty($edited) && !empty($id)) {
            //get data from post.
            $appraisal_data = $this->performance_model->array_from_post(array(
                'user_id',
                'appraisal_month',
                'customer_experiece_management',
                'marketing',
                'management',
                'administration',
                'presentation_skill',
                'quality_of_work',
                'efficiency',
                'integrity',
                'professionalism',
                'team_work',
                'critical_thinking',
                'conflict_management',
                'attendance',
                'ability_to_meed_deadline',
                'general_remarks',
            ));

            if ($id) {
                $activity = lang('activity_appraisal_update');
                $msg = lang('appraisal_update');
            } else {

                $activity = lang('activity_appraisal_saved');
                $msg = lang('appraisal_saved');
            }

            //Save Appraisal Data
            $this->performance_model->_table_name = "tbl_performance_apprisal"; // table name
            $this->performance_model->_primary_key = "performance_appraisal_id"; // $id
            $this->performance_model->save($appraisal_data, $id);

            $employee = $this->db->where('user_id', $appraisal_data['user_id'])->get('tbl_account_details')->row();
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'performance',
                'module_field_id' => $id,
                'activity' => $activity,
                'value1' => $employee->fullname,
                'value2' => lang('for') . date('M Y', strtotime($appraisal_data['appraisal_month']))
            );

            $this->performance_model->_table_name = 'tbl_activities';
            $this->performance_model->_primary_key = 'activities_id';
            $this->performance_model->save($activity);
            // messages for user
            $type = "success";
            $message = $msg;
            set_message($type, $message);
        }
        redirect('admin/performance/performance_report');
    }

    public function performance_report()
    {
        $data['title'] = lang('performance_report');
        $data['current_month'] = date('m');

        if ($this->input->post('year', TRUE)) { // if input year 
            $data['year'] = $this->input->post('year', TRUE);
        } else { // else current year
            $data['year'] = date('Y'); // get current year
        }
        // get all expense list by year and month
        $data['all_performance_info'] = $this->get_performance_info($data['year']);
        $data['subview'] = $this->load->view('admin/performance/report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function get_performance_info($year, $month = NULL)
    {// this function is to create get monthy recap report
        if (!empty($month)) {
            if ($month >= 1 && $month <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                $month = $year . "-" . '0' . $month;
            } else {
                $month = $year . "-" . $month;
            }
            $get_performance_list = $this->performance_model->get_performance_info_by_month($month); // get all report by start date and in date 
        } else {
            for ($i = 1; $i <= 12; $i++) { // query for months
                if ($i >= 1 && $i <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                    $month = $year . "-" . '0' . $i;
                } else {
                    $month = $year . "-" . $i;
                }
                $get_performance_list[$i] = $this->performance_model->get_performance_info_by_month($month); // get all report by start date and in date 
            }
        }
        return $get_performance_list; // return the result
    }

    public function appraisal_details($id)
    {
        //get all indicator values for user          
        $data['get_appraisal_info'] = $this->performance_model->get_appraisal_value_by_id($id);

        //get all indicator values
        $this->performance_model->_table_name = "tbl_performance_indicator"; // table name
        $this->performance_model->_order_by = "performance_indicator_id"; // $id
        $data['performance_indicator_details'] = $this->performance_model->get_by(array('designations_id' => $data['get_appraisal_info']->designations_id), TRUE);

        $data['modal_subview'] = $this->load->view('admin/performance/_modal_appraisal_details', $data, FALSE);
        $this->load->view('admin/_layout_modal_lg', $data);
    }

    public function appraisal_details_pdf($id)
    {
        //get all indicator values for user          
        $data['get_appraisal_info'] = $this->performance_model->get_appraisal_value_by_id($id);

        //get all indicator values
        $this->performance_model->_table_name = "tbl_performance_indicator"; // table name
        $this->performance_model->_order_by = "performance_indicator_id"; // $id
        $data['performance_indicator_details'] = $this->performance_model->get_by(array('designations_id' => $data['get_appraisal_info']->designations_id), TRUE);
        // get all employee salary info  by id        
        $viewfile = $this->load->view('admin/performance/appraisal_detail_pdf', $data, TRUE);

        $this->load->helper('dompdf');
        pdf_create($viewfile, slug_it(lang('performance_appraisal_of') . $data['get_appraisal_info']->fullname));
    }

}
