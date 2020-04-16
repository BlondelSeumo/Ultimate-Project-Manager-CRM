<?php

class Payroll extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('payroll_model');
    }

    public function salary_template($id = null)
    {
        $data['title'] = lang('salary_template_details');

        if (!empty($id)) {

            $this->payroll_model->_table_name = "tbl_salary_template"; // table name
            $this->payroll_model->_order_by = "salary_template_id"; // $id
            $data['salary_template_info'] = $this->payroll_model->get_by(array('salary_template_id' => $id), TRUE);

            // get salary allowance info by  salary template id
            $this->payroll_model->_table_name = "tbl_salary_allowance"; // table name
            $this->payroll_model->_order_by = "salary_allowance_id"; // $id
            $data['salary_allowance_info'] = $this->payroll_model->get_by(array('salary_template_id' => $id), FALSE);

            // get salary deduction info by salary template id
            $this->payroll_model->_table_name = "tbl_salary_deduction"; // table name
            $this->payroll_model->_order_by = "salary_deduction_id"; // $id
            $data['salary_deduction_info'] = $this->payroll_model->get_by(array('salary_template_id' => $id), FALSE);

            $data['active'] = 2;
        } else {
            $data['active'] = 1;
        }
        $data['subview'] = $this->load->view('admin/payroll/salary_template', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function salary_templateList()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_salary_template';
            $this->datatables->column_order = array('salary_grade', 'basic_salary', 'overtime_salary');
            $this->datatables->column_search = array('salary_grade', 'basic_salary', 'overtime_salary');
            $this->datatables->order = array('salary_template_id' => 'desc');

            $fetch_data = make_datatables();

            $edited = can_action('94', 'edited');
            $deleted = can_action('94', 'deleted');

            $data = array();
            foreach ($fetch_data as $_key => $v_salary_info) {

                $action = null;
                $sub_array = array();
                $sub_array[] = $_key + 1;

                $title = null;
                $title .= '<a data-toggle="modal" data-target="#myModal_lg" class="text-info" href="' . base_url() . 'admin/payroll/salary_template_details/' . $v_salary_info->salary_template_id . '">' . $v_salary_info->salary_grade . '</a>';
                $sub_array[] = $title;

                $sub_array[] = display_money($v_salary_info->basic_salary, default_currency());

                if (!empty($v_salary_info->overtime_salary)) {
                    $overtime_salary = $v_salary_info->overtime_salary;
                } else {
                    $overtime_salary = 0;
                }
                $sub_array[] = display_money($overtime_salary, default_currency());

                $action .= btn_view_modal('admin/payroll/salary_template_details/' . $v_salary_info->salary_template_id) . ' ';
                if (!empty($edited)) {
                    $action .= btn_edit('admin/payroll/salary_template/' . $v_salary_info->salary_template_id) . ' ';
                }
                if (!empty($deleted)) {
                    $action .= ajax_anchor(base_url('admin/payroll/delete_salary_template/' . $v_salary_info->salary_template_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                }
                $sub_array[] = $action;
                $data[] = $sub_array;

            }

            render_table($data);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function set_salary_details($id = NULL)
    {
        $created = can_action('94', 'created');
        $edited = can_action('94', 'edited');
        if (!empty($created) || !empty($edited) && !empty($id)) {
// inout data to salate template
            $template_data = $this->payroll_model->array_from_post(array('salary_grade', 'basic_salary', 'overtime_salary'));
// ************* Save into tbl_salary_template *************
            $this->payroll_model->_table_name = "tbl_salary_template"; // table name
            $this->payroll_model->_primary_key = "salary_template_id"; // $id
            $salary_template_id = $this->payroll_model->save($template_data, $id);

            // inout data salary_allowance information
            // Input data defualt salary_allowance
            $house_rent_allowance = $this->input->post('house_rent_allowance', TRUE);
            $medical_allowance = $this->input->post('medical_allowance', TRUE);
            // check defualt salary_allowance empty or not
            if (!empty($house_rent_allowance)) {
                $asalary_allowance_data['allowance_label'][] = lang('house_rent_allowance');
                $asalary_allowance_data['allowance_value'][] = $house_rent_allowance;
            }
            if (!empty($medical_allowance)) {
                $asalary_allowance_data['allowance_label'][] = lang('medical_allowance');
                $asalary_allowance_data['allowance_value'][] = $medical_allowance;
            }
// check salary_allowance data empty or not 
// if not empty then save into table
            if (!empty($asalary_allowance_data['allowance_label'])) {
                foreach ($asalary_allowance_data['allowance_label'] as $hkey => $h_salary_allowance_label) {
                    $alsalary_allowance_data['salary_template_id'] = $salary_template_id;
                    $alsalary_allowance_data['allowance_label'] = $h_salary_allowance_label;
                    $alsalary_allowance_data['allowance_value'] = $asalary_allowance_data['allowance_value'][$hkey];

// *********** save defualt value into tbl_salary_allowance    *******************
                    $this->payroll_model->_table_name = "tbl_salary_allowance"; // table name
                    $this->payroll_model->_primary_key = "salary_allowance_id"; // $id
                    $this->payroll_model->save($alsalary_allowance_data);
                }
            }
            // save add more value into tbl_salary_allowance
            $salary_allowance_label = $this->input->post('allowance_label', TRUE);
            $salary_allowance_value = $this->input->post('allowance_value', TRUE);
            // input id for update
            $salary_allowance_id = $this->input->post('salary_allowance_id', TRUE);
            $salary_allowance = get_any_field('tbl_salary_allowance', array('salary_template_id' => $salary_template_id), 'salary_allowance_id', true);
            if (!empty($salary_allowance)) {
                $salary_allowance = array_column($salary_allowance, 'salary_allowance_id');
                if (!empty($salary_allowance)) {
                    $delete_salary_allowance_id = array_diff($salary_allowance, $salary_allowance_id);
                    if (!empty($delete_salary_allowance_id)) {
                        foreach ($delete_salary_allowance_id as $deleted_id) {
                            $this->payroll_model->_table_name = "tbl_salary_allowance"; // table name
                            $this->payroll_model->_primary_key = "salary_allowance_id"; // $id
                            $this->payroll_model->delete($deleted_id);
                        }
                    }

                }
            }
            if (!empty($salary_allowance_label)) {
                foreach ($salary_allowance_label as $key => $v_salary_allowance_label) {
                    if (!empty($salary_allowance_value[$key])) {
                        $salary_allowance_data['salary_template_id'] = $salary_template_id;
                        $salary_allowance_data['allowance_label'] = $v_salary_allowance_label;
                        $salary_allowance_data['allowance_value'] = $salary_allowance_value[$key];
// *********** save add more value into tbl_salary_allowance    *******************
                        $this->payroll_model->_table_name = "tbl_salary_allowance"; // table name
                        $this->payroll_model->_primary_key = "salary_allowance_id"; // $id
                        if (!empty($salary_allowance_id[$key])) {
                            $allowance_id = $salary_allowance_id[$key];
                            $this->payroll_model->save($salary_allowance_data, $allowance_id);
                        } else {
                            $this->payroll_model->save($salary_allowance_data);
                        }
                    }
                }
            }
// inout data Deduction information
// Input data defualt salary_allowance
            $provident_fund = $this->input->post('provident_fund', TRUE);
            $tax_deduction = $this->input->post('tax_deduction', TRUE);
// check defualt Deduction empty or not
            if (!empty($provident_fund)) {
                $ddeduction_data['deduction_label'][] = lang('provident_fund');
                $ddeduction_data['deduction_value'][] = $provident_fund;
            }
            if (!empty($tax_deduction)) {
                $ddeduction_data['deduction_label'][] = lang('tax_deduction');
                $ddeduction_data['deduction_value'][] = $tax_deduction;
            }
            if (!empty($ddeduction_data['deduction_label'])) {
                foreach ($ddeduction_data['deduction_label'] as $dkey => $d_deduction_label) {
                    $adeduction_data['salary_template_id'] = $salary_template_id;
                    $adeduction_data['deduction_label'] = $d_deduction_label;
                    $adeduction_data['deduction_value'] = $ddeduction_data['deduction_value'][$dkey];

// *********** save defualt value into tbl_salary_allowance    *******************
                    $this->payroll_model->_table_name = "tbl_salary_deduction"; // table name
                    $this->payroll_model->_primary_key = "salary_deduction_id"; // $id
                    $this->payroll_model->save($adeduction_data);
                }
            }
// check Deduction data empty or not
// if not empty then save into table

// input salary deduction id for update
            $salary_deduction_id = $this->input->post('salary_deduction_id', TRUE);
// save add more value into tbl_deduction
            $deduction_label = $this->input->post('deduction_label', TRUE);
            $deduction_value = $this->input->post('deduction_value', TRUE);

            $salary_deduction = get_any_field('tbl_salary_deduction', array('salary_template_id' => $salary_template_id), 'salary_deduction_id', true);
            if (!empty($salary_deduction)) {
                $salary_deduction = array_column($salary_deduction, 'salary_deduction_id');
                if (!empty($salary_deduction)) {

                    $delete_salary_deduction_id = array_diff($salary_deduction, $salary_deduction_id);
                    if (!empty($delete_salary_deduction_id)) {
                        foreach ($delete_salary_deduction_id as $deleted_id) {
                            $this->payroll_model->_table_name = "tbl_salary_deduction"; // table name
                            $this->payroll_model->_primary_key = "salary_deduction_id"; // $id
                            $this->payroll_model->delete($deleted_id);
                        }
                    }

                }
            }

            if (!empty($deduction_label)) {
                foreach ($deduction_label as $key => $v_deduction_label) {
                    if (!empty($deduction_value[$key])) {

                        $deduction_data['salary_template_id'] = $salary_template_id;
                        $deduction_data['deduction_label'] = $v_deduction_label;
                        $deduction_data['deduction_value'] = $deduction_value[$key];
// *********** save add more value into tbl_deductio    *******************
                        $this->payroll_model->_table_name = "tbl_salary_deduction"; // table name
                        $this->payroll_model->_primary_key = "salary_deduction_id"; // $id

                        if (!empty($salary_deduction_id[$key])) {
                            $deduction_id = $salary_deduction_id[$key];
                            $this->payroll_model->save($deduction_data, $deduction_id);
                        } else {
                            $this->payroll_model->save($deduction_data);
                        }
                    }
                }
            }
            if (!empty($id)) {
                $activity = 'activity_salary_template_update';
                $msg = lang('salary_template_update');
            } else {
                $activity = 'activity_salary_template_added';
                $msg = lang('salary_template_added');
            }
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'payroll',
                'module_field_id' => $id,
                'activity' => $activity,
                'icon' => 'fa-money',
                'link' => 'admin/payroll/view_salary_template/' . $salary_template_id,
                'value1' => $template_data['salary_grade'],
            );
            // Update into tbl_project
            $this->payroll_model->_table_name = "tbl_activities"; //table name
            $this->payroll_model->_primary_key = "activities_id";
            $this->payroll_model->save($activities);

            $type = 'success';
            $message = $msg;
            set_message($type, $message);
        }
        redirect('admin/payroll/salary_template');
    }

    public function view_salary_template($id)
    {
        $data['title'] = lang('total_salary_details');
// get salary_template_info by  salary template id
        $this->payroll_model->_table_name = "tbl_salary_template"; // table name
        $this->payroll_model->_order_by = "salary_template_id"; // $id
        $data['salary_template_info'] = $this->payroll_model->get_by(array('salary_template_id' => $id), TRUE);

// get salary allowance info by  salary template id
        $this->payroll_model->_table_name = "tbl_salary_allowance"; // table name
        $this->payroll_model->_order_by = "salary_allowance_id"; // $id
        $data['salary_allowance_info'] = $this->payroll_model->get_by(array('salary_template_id' => $id), FALSE);

// get salary deduction info by salary template id
        $this->payroll_model->_table_name = "tbl_salary_deduction"; // table name
        $this->payroll_model->_order_by = "salary_deduction_id"; // $id
        $data['salary_deduction_info'] = $this->payroll_model->get_by(array('salary_template_id' => $id), FALSE);

        $data['subview'] = $this->load->view('admin/payroll/salary_template_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function salary_template_details($id)
    {
        $data['title'] = lang('total_salary_details');
// get salary_template_info by  salary template id
        $this->payroll_model->_table_name = "tbl_salary_template"; // table name
        $this->payroll_model->_order_by = "salary_template_id"; // $id
        $data['salary_template_info'] = $this->payroll_model->get_by(array('salary_template_id' => $id), TRUE);

// get salary allowance info by  salary template id
        $this->payroll_model->_table_name = "tbl_salary_allowance"; // table name
        $this->payroll_model->_order_by = "salary_allowance_id"; // $id
        $data['salary_allowance_info'] = $this->payroll_model->get_by(array('salary_template_id' => $id), FALSE);

// get salary deduction info by salary template id
        $this->payroll_model->_table_name = "tbl_salary_deduction"; // table name
        $this->payroll_model->_order_by = "salary_deduction_id"; // $id
        $data['salary_deduction_info'] = $this->payroll_model->get_by(array('salary_template_id' => $id), FALSE);

        $data['subview'] = $this->load->view('admin/payroll/salary_template_details', $data, FALSE);
        $this->load->view('admin/_layout_modal_lg', $data);
    }

    public function salary_template_pdf($id)
    {
        $data['title'] = lang('total_salary_details');
// get salary_template_info by  salary template id
        $this->payroll_model->_table_name = "tbl_salary_template"; // table name
        $this->payroll_model->_order_by = "salary_template_id"; // $id
        $data['salary_template_info'] = $this->payroll_model->get_by(array('salary_template_id' => $id), TRUE);

// get salary allowance info by  salary template id
        $this->payroll_model->_table_name = "tbl_salary_allowance"; // table name
        $this->payroll_model->_order_by = "salary_allowance_id"; // $id
        $data['salary_allowance_info'] = $this->payroll_model->get_by(array('salary_template_id' => $id), FALSE);

// get salary deduction info by salary template id
        $this->payroll_model->_table_name = "tbl_salary_deduction"; // table name
        $this->payroll_model->_order_by = "salary_deduction_id"; // $id
        $data['salary_deduction_info'] = $this->payroll_model->get_by(array('salary_template_id' => $id), FALSE);

        $viewfile = $this->load->view('admin/payroll/salary_template_pdf', $data, TRUE);
        $this->load->helper('dompdf');
        pdf_create($viewfile, slug_it(lang('salary_template') . '-' . $data['salary_template_info']->salary_grade));
    }

    public function delete_salary_template($id)
    {
        $deleted = can_action('94', 'deleted');
        if (!empty($deleted)) {
            // check into employee payroll table
            // if is exist then do not delete this else delete the id
            $salary_template_info = $this->payroll_model->check_by(array('salary_template_id' => $id), 'tbl_salary_template');

            $check_existing_template = $this->payroll_model->check_by(array('salary_template_id' => $id), 'tbl_employee_payroll');
            if (!empty($check_existing_template)) {
                $type = 'error';
                $message = lang('salary_template_already_used');
            } else {
                // *********** Delete into tbl_salary_template *******************
                $this->payroll_model->_table_name = "tbl_salary_template"; // table name
                $this->payroll_model->_primary_key = "salary_template_id"; // $id
                $this->payroll_model->delete($id);
                // *********** Delete into tbl_salary_allowance *******************
                $this->payroll_model->_table_name = "tbl_salary_allowance"; // table name
                $this->payroll_model->delete_multiple(array('salary_template_id' => $id));

                // *********** Delete into tbl_salary_deduction *******************
                $this->payroll_model->_table_name = "tbl_salary_deduction"; // table name
                $this->payroll_model->delete_multiple(array('salary_template_id' => $id));

                $type = 'success';
                $message = lang('salary_template_deleted');

                // save into activities
                $activities = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'payroll',
                    'module_field_id' => $id,
                    'activity' => 'activity_salary_template_delete',
                    'icon' => 'fa-money',
                    'value1' => $salary_template_info->salary_grade,
                    'value2' => $salary_template_info->basic_salary,
                );
                // Update into tbl_project
                $this->payroll_model->_table_name = "tbl_activities"; //table name
                $this->payroll_model->_primary_key = "activities_id";
                $this->payroll_model->save($activities);
            }

            set_message($type, $message);
        }
        redirect('admin/payroll/salary_template');
    }

    public function hourly_rate($id = NULL)
    {
        $data['title'] = lang('hourly_rate');

        if (!empty($id)) {
// get salary template deatails
            $this->payroll_model->_table_name = "tbl_hourly_rate"; // table name
            $this->payroll_model->_order_by = "hourly_rate_id"; // $id
            $data['hourly_rate'] = $this->payroll_model->get_by(array('hourly_rate_id' => $id), TRUE);

            $data['active'] = 2;
        } else {
            $data['active'] = 1;
        }

        $data['subview'] = $this->load->view('admin/payroll/hourly_rate', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function hourly_rateList()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_hourly_rate';
            $this->datatables->column_order = array('hourly_grade', 'hourly_rate');
            $this->datatables->column_search = array('hourly_grade', 'hourly_rate');
            $this->datatables->order = array('hourly_rate_id' => 'desc');

            $fetch_data = make_datatables();

            $edited = can_action('95', 'edited');
            $deleted = can_action('95', 'deleted');

            $data = array();
            foreach ($fetch_data as $_key => $v_hourly_rate) {

                $action = null;
                $sub_array = array();
                $sub_array[] = $_key + 1;

                $sub_array[] = $v_hourly_rate->hourly_grade;

                $sub_array[] = display_money($v_hourly_rate->hourly_rate, default_currency());

                if (!empty($edited)) {
                    $action .= btn_edit('admin/payroll/hourly_rate/' . $v_hourly_rate->hourly_rate_id) . ' ';
                }
                if (!empty($deleted)) {
                    $action .= ajax_anchor(base_url('admin/payroll/delete_hourly_rate/' . $v_hourly_rate->hourly_rate_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                }
                $sub_array[] = $action;
                $data[] = $sub_array;

            }

            render_table($data);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function set_hourly_rate($id = null)
    {
        $created = can_action('95', 'created');
        $edited = can_action('95', 'edited');
        if (!empty($created) || !empty($edited) && !empty($id)) {
            $data = $this->payroll_model->array_from_post(array('hourly_grade', 'hourly_rate'));
            $where = array('hourly_grade' => $data['hourly_grade']);
// duplicate value check in DB
            if (!empty($id)) { // if id exist in db update data
                $hourly_rate_id = array('hourly_rate_id !=' => $id);
                $activity = 'activity_hourly_template_update';
                $msg = lang('hourly_template_update');
            } else { // if id is not exist then set id as null
                $hourly_rate_id = null;
                $activity = 'activity_hourly_template_added';
                $msg = lang('hourly_template_added');
            }
// check whether this input data already exist or not
            $check_hourly_rate = $this->payroll_model->check_update('tbl_hourly_rate', $where, $hourly_rate_id);
            if (!empty($check_hourly_rate)) { // if input data already exist show error alert
// massage for user
                $type = 'error';
                $message = lang('hourly_template_already_exist');
            } else {
                $this->payroll_model->_table_name = 'tbl_hourly_rate';
                $this->payroll_model->_primary_key = 'hourly_rate_id';
                $this->payroll_model->save($data, $id);

                $type = 'success';
                $message = $msg;
            }
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'payroll',
                'module_field_id' => $id,
                'activity' => $activity,
                'icon' => 'fa-money',
                'value1' => $data['hourly_grade'],
                'value2' => $data['hourly_rate'],
            );
            // Update into tbl_project
            $this->payroll_model->_table_name = "tbl_activities"; //table name
            $this->payroll_model->_primary_key = "activities_id";
            $this->payroll_model->save($activities);
            set_message($type, $message);
        }
        redirect('admin/payroll/hourly_rate');
    }

    public function delete_hourly_rate($id)
    {
        $deleted = can_action('95', 'deleted');
        if (!empty($deleted)) {
            // check into employee payroll table
            // if is exist then do not delete this else delete the id
            $hourly_template = $this->payroll_model->check_by(array('hourly_rate_id' => $id), 'tbl_hourly_rate');

            $check_existing_template = $this->payroll_model->check_by(array('hourly_rate_id' => $id), 'tbl_employee_payroll');
            if (!empty($check_existing_template)) {
                $type = 'error';
                $message = lang('hourly_template_already_exist');
            } else {
                $this->payroll_model->_table_name = 'tbl_hourly_rate';
                $this->payroll_model->_primary_key = 'hourly_rate_id';
                $this->payroll_model->delete($id);

                $type = 'success';
                $message = lang('hourly_template_deleted');
            }
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'payroll',
                'module_field_id' => $id,
                'activity' => 'activity_hourly_template_deleted',
                'icon' => 'fa-money',
                'value1' => $hourly_template->hourly_grade,
                'value2' => $hourly_template->hourly_rate,
            );
            // Update into tbl_project
            $this->payroll_model->_table_name = "tbl_activities"; //table name
            $this->payroll_model->_primary_key = "activities_id";
            $this->payroll_model->save($activities);

            set_message($type, $message);
        }
        redirect('admin/payroll/hourly_rate');
    }

    public function manage_salary_details($departments_id = NULL)
    {
        $data['title'] = lang('manage_salary_details');
        // retrive all data from department table
        $data['all_department_info'] = $this->db->get('tbl_departments')->result();

        $flag = $this->input->post('flag', TRUE);
        if (!empty($flag) || !empty($departments_id)) { // check employee id is empty or not
            $data['flag'] = 1;
            if (!empty($departments_id)) {
                $data['departments_id'] = $departments_id;
            } else {
                $data['departments_id'] = $this->input->post('departments_id', TRUE);
            }
            // get all designation info by Department id
            $designation_info = $this->db->where('departments_id', $data['departments_id'])->get('tbl_designations')->result();

            if (!empty($designation_info)) {
                foreach ($designation_info as $v_designatio) {
                    $data['employee_info'][] = $this->payroll_model->get_emp_info_by_id($v_designatio->designations_id);
                    $employee_info = $this->payroll_model->get_emp_info_by_id($v_designatio->designations_id);
                    foreach ($employee_info as $value) {
                        // get all salary Template info
                        $data['salary_grade_info'][] = $this->db->where('user_id', $value->user_id)->get('tbl_employee_payroll')->result();
                    }
                }
            }
            // get all Hourly payment info
            $data['hourly_grade'] = $this->db->get('tbl_hourly_rate')->result();
            // get all salary Template info
            $data['salary_grade'] = $this->db->get('tbl_salary_template')->result();
        }
        $data['subview'] = $this->load->view('admin/payroll/manage_salary_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function save_salary_details()
    {
        // inout data to salate template
        $user_id = $this->input->post('user_id', TRUE);

        $hourly_status = $this->input->post('hourly_status', TRUE);
        $hourly_rate_id = $this->input->post('hourly_rate_id', TRUE);

        $monthly_status = $this->input->post('monthly_status', TRUE);
        $salary_template_id = $this->input->post('salary_template_id', TRUE);
        $payroll_id = $this->input->post('payroll_id', TRUE);
        foreach ($user_id as $key => $v_emp_id) {
            $data['user_id'] = $v_emp_id;
            $data['salary_template_id'] = NULL;
            $data['hourly_rate_id'] = NULL;
            if (!empty($hourly_status)) {
                foreach ($hourly_status as $v_hourly) {
                    if ($v_emp_id == $v_hourly) {
                        $data['hourly_rate_id'] = $hourly_rate_id[$key];
                        $data['salary_template_id'] = NULL;
                    }
                }
            }
            if (!empty($monthly_status)) {
                foreach ($monthly_status as $v_monthly) {
                    if ($v_emp_id == $v_monthly) {
                        $data['salary_template_id'] = $salary_template_id[$key];
                        $data['hourly_rate_id'] = NULL;
                    }
                }
            }
            // save into tbl employee payroll
            $this->payroll_model->_table_name = "tbl_employee_payroll"; // table name
            $this->payroll_model->_primary_key = "payroll_id"; // $id
            if (!empty($payroll_id[$key])) {
                $id = $payroll_id[$key];
                $this->payroll_model->save($data, $id);
            } else {
                $id = $this->payroll_model->save($data);
            }
        }
        $departments_id = $this->input->post('departments_id', TRUE);
        $dept_info = $this->db->where('departments_id', $departments_id)->get('tbl_departments')->row();
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'payroll',
            'module_field_id' => $id,
            'activity' => 'activity_salary_details_update',
            'icon' => 'fa-money',
            'value1' => $dept_info->deptname,
        );
        // Update into tbl_project
        $this->payroll_model->_table_name = "tbl_activities"; //table name
        $this->payroll_model->_primary_key = "activities_id";
        $this->payroll_model->save($activities);

        $type = 'success';
        $message = lang('salary_details_updated');
        set_message($type, $message);
        redirect('admin/payroll/employee_salary_list');
    }

    public function delete_salary($id)
    {
        $emp_salary_info = $this->db->where('payroll_id', $id)->get('tbl_employee_payroll')->row();
        $template_name = '';
        if (!empty($emp_salary_info->salary_template_id)) {
            $template_info = $this->db->where('salary_template_id', $id)->get('tbl_salary_template')->row();
            if (!empty($template_info)) {
                $template_name = $template_info->salary_grade;
            }
        } elseif (!empty($emp_salary_info->hourly_rate_id)) {
            $hourly_template = $this->db->where('hourly_rate_id', $id)->get('tbl_hourly_rate')->row();
            if (!empty($hourly_template)) {
                $template_name = $hourly_template->hourly_grade;
            }
        }
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'payroll',
            'module_field_id' => $id,
            'activity' => 'activity_delete_employee_salary_list',
            'icon' => 'fa-money',
            'value1' => $template_name,
        );
        $this->payroll_model->_table_name = "tbl_activities"; //table name
        $this->payroll_model->_primary_key = "activities_id";
        $this->payroll_model->save($activities);


        $this->payroll_model->_table_name = "tbl_employee_payroll"; // table name
        $this->payroll_model->_primary_key = "payroll_id"; // $id
        $this->payroll_model->delete($id);

        $type = "success";
        $message = lang('salary_information_deleted');
        set_message($type, $message);
        redirect('admin/payroll/employee_salary_list');
    }

    public function employee_salary_list()
    {
        $data['title'] = lang('employee_salary_details');
        $data['subview'] = $this->load->view('admin/payroll/employee_salary_list', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function employee_salaryList()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_employee_payroll';
            $this->datatables->join_table = array('tbl_account_details', 'tbl_salary_template', 'tbl_hourly_rate', 'tbl_designations', 'tbl_departments');
            $this->datatables->join_where = array('tbl_employee_payroll.user_id = tbl_account_details.user_id', 'tbl_employee_payroll.salary_template_id = tbl_salary_template.salary_template_id', 'tbl_employee_payroll.hourly_rate_id = tbl_hourly_rate.hourly_rate_id', 'tbl_designations.designations_id  = tbl_account_details.designations_id', 'tbl_departments.departments_id  = tbl_designations.departments_id');
            $this->datatables->column_order = array('tbl_account_details.employment_id', 'tbl_account_details.fullname', 'tbl_salary_template.salary_grade', 'tbl_salary_template.basic_salary', 'tbl_hourly_rate.hourly_grade', 'tbl_hourly_rate.hourly_rate', 'tbl_salary_template.overtime_salary');
            $this->datatables->column_search = array('tbl_account_details.employment_id', 'tbl_account_details.fullname', 'tbl_salary_template.salary_grade', 'tbl_salary_template.basic_salary', 'tbl_hourly_rate.hourly_grade', 'tbl_hourly_rate.hourly_rate', 'tbl_salary_template.overtime_salary');
            $this->datatables->order = array('payroll_id' => 'desc');

            $fetch_data = make_datatables();

            $data = array();
            foreach ($fetch_data as $_key => $v_emp_salary) {

                $action = null;
                $sub_array = array();
                $sub_array[] = $v_emp_salary->employment_id;

                $title = null;
                if (!empty($v_emp_salary->salary_grade)) {
                    $title = '<a data-toggle="modal" data-target="#myModal_lg" class="text-info" href="' . base_url() . 'admin/payroll/view_salary_details/' . $v_emp_salary->salary_template_id . '/' . $v_emp_salary->user_id . '">' . $v_emp_salary->fullname . '</a>';
                } else {
                    $title = $v_emp_salary->fullname;
                }
                $sub_array[] = $title;

                if (!empty($v_emp_salary->salary_grade)) {
                    $grade = $v_emp_salary->salary_grade . ' <small>(' . lang("monthly") . ')</small>';
                } else {
                    $grade = $v_emp_salary->hourly_grade . ' <small>(' . lang("hourly") . ')</small>';
                }
                $sub_array[] = $grade;

                if (!empty($v_emp_salary->basic_salary)) {
                    $basic_salary = display_money($v_emp_salary->basic_salary, default_currency());
                } else {
                    $basic_salary = display_money($v_emp_salary->hourly_rate, default_currency()) . ' <small>(' . lang("per_hour") . ')</small>';
                }
                $sub_array[] = $basic_salary;

                if (!empty($v_emp_salary->overtime_salary)) {
                    $overtime_salary = display_money($v_emp_salary->overtime_salary, default_currency());
                } else {
                    $overtime_salary = 0;
                }
                $sub_array[] = $overtime_salary;

                if (!empty($v_emp_salary->salary_grade)) {
                    $action .= btn_view_modal('admin/payroll/view_salary_details/' . $v_emp_salary->salary_template_id . '/' . $v_emp_salary->user_id) . ' ';
                }
                if ($this->session->userdata('user_type') == '1') {
                    $action .= ajax_anchor(base_url('admin/payroll/delete_salary/' . $v_emp_salary->payroll_id), "<i
    class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                }
                $action .= btn_edit('admin/payroll/manage_salary_details/' . $v_emp_salary->departments_id) . ' ';

                $sub_array[] = $action;
                $data[] = $sub_array;

            }
            render_table($data);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function view_salary_details($salary_template_id, $id)
    {
        if (empty($salary_template_id)) {
            $type = "error";
            $message = lang('operation_failed');
            set_message($type, $message);
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/payroll/employee_salary_list');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
        $data['title'] = lang('view_salary_details');
// get all employee salary info   by id
        $data['emp_salary_info'] = $this->payroll_model->get_emp_salary_list($id);

// get salary allowance info by  salary template id
        $this->payroll_model->_table_name = "tbl_salary_allowance"; // table name
        $this->payroll_model->_order_by = "salary_allowance_id"; // $id
        $data['salary_allowance_info'] = $this->payroll_model->get_by(array('salary_template_id' => $salary_template_id), FALSE);

// get salary deduction info by salary template id
        $this->payroll_model->_table_name = "tbl_salary_deduction"; // table name
        $this->payroll_model->_order_by = "salary_deduction_id"; // $id
        $data['salary_deduction_info'] = $this->payroll_model->get_by(array('salary_template_id' => $salary_template_id), FALSE);

        $data['subview'] = $this->load->view('admin/payroll/employee_salary_details', $data, FALSE);
        $this->load->view('admin/_layout_modal_lg', $data);
    }

    public function make_pdf($id)
    {
        $data['title'] = lang('view_salary_details');
// get all employee salary info  by id
        $data['emp_salary_info'] = $this->payroll_model->get_emp_salary_list($id);

// get salary allowance info by  salary template id
        $this->payroll_model->_table_name = "tbl_salary_allowance"; // table name
        $this->payroll_model->_order_by = "salary_allowance_id"; // $id
        $data['salary_allowance_info'] = $this->payroll_model->get_by(array('salary_template_id' => $data['emp_salary_info']->salary_template_id), FALSE);

// get salary deduction info by salary template id
        $this->payroll_model->_table_name = "tbl_salary_deduction"; // table name
        $this->payroll_model->_order_by = "salary_deduction_id"; // $id
        $data['salary_deduction_info'] = $this->payroll_model->get_by(array('salary_template_id' => $data['emp_salary_info']->salary_template_id), FALSE);

        $viewfile = $this->load->view('admin/payroll/employee_salary_pdf', $data, TRUE);
        $this->load->helper('dompdf');
        pdf_create($viewfile, slug_it(lang('salary_details') . '- ' . $data['emp_salary_info']->fullname));
    }

    public function add_advance_salary($id = null)
    {
        // active check with current month
        $data['current_month'] = date('m');
        if ($this->input->post('year', TRUE)) { // if input year
            $data['year'] = $this->input->post('year', TRUE);
        } else { // else current year
            $data['year'] = date('Y'); // get current year
        }
        $data['all_employee'] = $this->payroll_model->get_all_employee();
        if ($id == 'true') {
            $advance_salary_id = $this->uri->segment(5);
        } else {
            $advance_salary_id = $id;
        }
        if (!empty($advance_salary_id)) {
            $data['advance_salary'] = $this->db->where('advance_salary_id', $advance_salary_id)->get('tbl_advance_salary')->row();
        }
        $data['modal_subview'] = $this->load->view('admin/payroll/add_advance_salary', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function save_advance_salary($id = null)
    {
        $data['advance_amount'] = $this->input->post('advance_amount', TRUE);
//receive form input by post
        $user_id = $this->input->post('user_id', TRUE);
        if (!empty($user_id)) {
            $data['user_id'] = $user_id;
        } else {
            $data['user_id'] = $this->session->userdata('user_id');
        }

        $this->load->model('global_model');
        $basic_salary = $this->global_model->get_advance_amount($data['user_id']);
        if (!empty($basic_salary)) {
            if ($basic_salary < $data['advance_amount']) {
// messages for user
                $type = "error";
                $message = lang('exced_basic_salary');
                set_message($type, $message);
                redirect('admin/payroll/advance_salary');
            }
        } else {
            $type = "error";
            $message = lang('you_can_not_apply');
            set_message($type, $message);
            redirect('admin/payroll/advance_salary');
        }

        $data['reason'] = $this->input->post('reason', TRUE);
        $data['deduct_month'] = $this->input->post('deduct_month', TRUE);

        if ($this->session->userdata('user_type') == 1) {
            $data['status'] = 1;
        }
//save data in database
        $this->payroll_model->_table_name = "tbl_advance_salary"; // table name
        $this->payroll_model->_primary_key = "advance_salary_id"; // $id
        $id = $this->payroll_model->save($data, $id);

// save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'payroll',
            'module_field_id' => $id,
            'activity' => 'activity_apply_advance_salary',
            'icon' => 'cc-mastercard',
            'link' => 'admin/payroll/view_advance_salary/' . $id,
            'value1' => $this->db->where('user_id', $data['user_id'])->get('tbl_account_details')->row()->fullname,
            'value2' => $data['advance_amount'],
        );

// Update into tbl_project
        $this->payroll_model->_table_name = "tbl_activities"; //table name
        $this->payroll_model->_primary_key = "activities_id";
        $this->payroll_model->save($activities);

        $advance_salary_info = $this->payroll_model->check_by(array('advance_salary_id' => $id), 'tbl_advance_salary');
        $profile_info = $this->payroll_model->check_by(array('user_id' => $advance_salary_info->user_id), 'tbl_account_details');
// send email to departments head
        if ($advance_salary_info->status == 0) {
            if (!empty($profile_info->designations_id)) {
// get departments head user id
                $designation_info = $this->payroll_model->check_by(array('designations_id' => $profile_info->designations_id), 'tbl_designations');
// get departments head by departments id
                $dept_head = $this->payroll_model->check_by(array('departments_id' => $designation_info->departments_id), 'tbl_departments');

                if (!empty($dept_head->department_head_id)) {
                    $advance_salary_email = config_item('advance_salary_email');
                    if (!empty($advance_salary_email) && $advance_salary_email == 1) {

                        $email_template = email_templates(array('email_group' => 'advance_salary_email'), $advance_salary_info->user_id, true);
                        $user_info = $this->payroll_model->check_by(array('user_id' => $dept_head->department_head_id), 'tbl_users');
                        $message = $email_template->template_body;
                        $subject = $email_template->subject;
                        $username = str_replace("{NAME}", $profile_info->fullname, $message);
                        $Link = str_replace("{LINK}", base_url() . 'admin/payroll/view_advance_salary/' . $id, $username);
                        $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);
                        $data['message'] = $message;
                        $message = $this->load->view('email_template', $data, TRUE);

                        $params['subject'] = $subject;
                        $params['message'] = $message;
                        $params['resourceed_file'] = '';
                        $params['recipient'] = $user_info->email;
                        $this->payroll_model->send_email($params);
                    }

                    $notifyUser = array($dept_head->department_head_id);
                    if (!empty($notifyUser)) {
                        foreach ($notifyUser as $v_user) {
                            add_notification(array(
                                'to_user_id' => $v_user,
                                'description' => 'not_advance_salary_request',
                                'icon' => 'cc-mastercard',
                                'link' => 'admin/payroll/view_advance_salary/' . $id,
                                'value' => lang('by') . ' ' . $this->session->userdata('name'),
                            ));
                        }
                    }
                    if (!empty($notifyUser)) {
                        show_notification($notifyUser);
                    }
                }
            }
        }
// messages for user
        $type = "success";
        $message = lang('advance_salary_supmited');
        set_message($type, $message);
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('admin/payroll/advance_salary');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function advance_salary($details = null)
    {
// list view
        if (!empty($details)) {
            $data['active'] = 1;
            $data['switch'] = 1;
        }
        $data['title'] = lang('advance_salary');
// active check with current month
        $data['current_month'] = date('m');
        if ($this->input->post('year', TRUE)) { // if input year
            $data['year'] = $this->input->post('year', TRUE);
        } else { // else current year
            $data['year'] = date('Y'); // get current year
        }
// get all expense list by year and month
        $data['advance_salary_info'] = $this->get_advance_salary_info($data['year']);

        $data['subview'] = $this->load->view('admin/payroll/advance_salary', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function my_advance_salaryList()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_advance_salary';
            $this->datatables->join_table = array('tbl_account_details');
            $this->datatables->join_where = array('tbl_account_details.user_id=tbl_advance_salary.user_id');
            $this->datatables->column_order = array('tbl_account_details.employment_id', 'tbl_account_details.fullname', 'advance_amount', 'deduct_month', 'request_date');
            $this->datatables->column_search = array('tbl_account_details.employment_id', 'tbl_account_details.fullname', 'advance_amount', 'deduct_month', 'request_date');
            $this->datatables->order = array('advance_salary_id' => 'desc');
            $where = array('tbl_advance_salary.user_id' => $this->session->userdata('user_id'));
            $fetch_data = make_datatables($where);
            $data = array();
            foreach ($fetch_data as $_key => $my_salary) {
                $staff_details = get_staff_details($my_salary->user_id);
                $action = null;
                $sub_array = array();
                $sub_array[] = $staff_details->employment_id;
                $sub_array[] = $staff_details->fullname;
                $sub_array[] = display_money($my_salary->advance_amount, default_currency());
                $sub_array[] = display_date($my_salary->request_date);
                $sub_array[] = date('M,Y', strtotime($my_salary->deduct_month));
                if ($my_salary->status == '0') {
                    $status = '<span class="label label-warning">' . lang('pending') . '</span>';
                } elseif ($my_salary->status == '1') {
                    $status = '<span class="label label-success"> ' . lang('accepted') . '</span>';
                } elseif ($my_salary->status == '2') {
                    $status = '<span class="label label-danger">' . lang('rejected') . '</span>';
                } else {
                    $status = '<span class="label label-info">' . lang('paid') . '</span>';
                }
                $sub_array[] = $status;
                if ($this->session->userdata('user_type') == 1) {
                    $sub_array[] = '<a href="' . base_url() . 'admin/payroll/advance_salary_details/' . $my_salary->advance_salary_id . '"
                               class="btn btn-info btn-xs" title="' . lang('view') . '" data-toggle="modal"
                               data-target="#myModal"><span class="fa fa-list-alt"></span></a>';
                }
                $data[] = $sub_array;
            }

            render_table($data, $where);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function all_advance_salaryList()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_advance_salary';
            $this->datatables->join_table = array('tbl_account_details');
            $this->datatables->join_where = array('tbl_account_details.user_id=tbl_advance_salary.user_id');
            $this->datatables->column_order = array('tbl_account_details.employment_id', 'tbl_account_details.fullname', 'advance_amount', 'deduct_month', 'request_date');
            $this->datatables->column_search = array('tbl_account_details.employment_id', 'tbl_account_details.fullname', 'advance_amount', 'deduct_month', 'request_date');
            $this->datatables->order = array('tbl_advance_salary.request_date' => 'desc');
            $fetch_data = make_datatables();

            $data = array();
            foreach ($fetch_data as $_key => $my_salary) {

                $action = null;
                $sub_array = array();
                $sub_array[] = $my_salary->employment_id;
                $sub_array[] = $my_salary->fullname;
                $sub_array[] = display_money($my_salary->advance_amount, default_currency());
                $sub_array[] = display_date($my_salary->request_date);
                $sub_array[] = date('M,Y', strtotime($my_salary->deduct_month));
                if ($my_salary->status == '0') {
                    $status = '<span class="label label-warning">' . lang('pending') . '</span>';
                } elseif ($my_salary->status == '1') {
                    $status = '<span class="label label-success"> ' . lang('accepted') . '</span>';
                } elseif ($my_salary->status == '2') {
                    $status = '<span class="label label-danger">' . lang('rejected') . '</span>';
                } else {
                    $status = '<span class="label label-info">' . lang('paid') . '</span>';
                }
                $sub_array[] = $status;
                if ($this->session->userdata('user_type') == 1) {
                    $sub_array[] = '<a href="' . base_url() . 'admin/payroll/advance_salary_details/' . $my_salary->advance_salary_id . '"
                               class="btn btn-info btn-xs" title="' . lang('view') . '" data-toggle="modal"
                               data-target="#myModal"><span class="fa fa-list-alt"></span></a>';
                }
                $data[] = $sub_array;
            }

            render_table($data);
        } else {
            redirect('admin/dashboard');
        }
    }


    public function get_advance_salary_info($year, $month = NULL)
    {// this function is to create get monthy recap report
        if (!empty($month)) {
            $advance_salary_info = $this->payroll_model->get_advance_salary_info_by_date($month); // get all report by start date and in date
        } else {
            for ($i = 1; $i <= 12; $i++) { // query for months
                if ($i >= 1 && $i <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                    $month = $year . "-" . '0' . $i;
                } else {
                    $month = $year . "-" . $i;
                }
                $advance_salary_info[$i] = $this->payroll_model->get_advance_salary_info_by_date($month); // get all report by start date and in date
            }
        }
        return $advance_salary_info; // return the result
    }

    public function advance_salary_pdf($year, $month)
    {
        if ($month >= 1 && $month <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
            $month = $year . "-" . '0' . $month;
        } else {
            $month = $year . "-" . $month;
        }
        $data['advance_salary_info'] = $this->get_advance_salary_info($year, $month);

        $month_name = date('F', strtotime($year . '-' . $month)); // get full name of month by date query
        $data['monthyaer'] = $month_name . '  ' . $year;

        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/payroll/advance_salary_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it(lang('advance_salary_report') . ' - ' . $data['monthyaer']));
    }

    public function advance_salary_details($id)
    {
        $data['title'] = lang('advance_salary_details');
        $data['advance_salary_info'] = $this->payroll_model->view_advance_salary($id);
        $data['subview'] = $this->load->view('admin/payroll/advance_salary_details', $data, false);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function view_advance_salary($id)
    {
        $data['title'] = lang('advance_salary_details');
        $data['advance_salary_info'] = $this->payroll_model->view_advance_salary($id);
        $data['subview'] = $this->load->view('admin/payroll/advance_salary_details', $data, true);
        $this->load->view('admin/_layout_main', $data);
    }


    public function change_status($status, $id)
    {
        if (empty($status)) {
            $type = "error";
            $message = lang('operation_failed');
            set_message($type, $message);
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/payroll/advance_salary');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
        $data['status'] = $status;
        $data['advance_salary'] = $this->payroll_model->check_by(array('advance_salary_id' => $id), 'tbl_advance_salary');

        $data['modal_subview'] = $this->load->view('admin/leave_management/_change_status', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);

    }

    public function set_salary_status($status, $id)
    {
        $data['status'] = $status;
        $data['approve_by'] = $this->session->userdata('user_id');

        $where = array('advance_salary_id' => $id);
        $this->payroll_model->set_action($where, $data, 'tbl_advance_salary');

        $advance_salary_info = $this->payroll_model->check_by(array('advance_salary_id' => $id), 'tbl_advance_salary');
        $profile_info = $this->payroll_model->check_by(array('user_id' => $advance_salary_info->user_id), 'tbl_account_details');

        if ($advance_salary_info->status == '0') {
            $status = lang('pending');
        } elseif ($advance_salary_info->status == '1') {
            $status = lang('accepted');
            $this->send_email_by_status($advance_salary_info, true);
        } elseif ($advance_salary_info->status == '2') {
            $status = lang('rejected');
            $this->send_email_by_status($advance_salary_info);
        } else {
            $status = lang('paid');
        }
// save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'payroll',
            'module_field_id' => $id,
            'activity' => 'activity_advance_salary_update',
            'icon' => 'cc-mastercard',
            'link' => 'admin/payroll/view_advance_salary/' . $id,
            'value1' => $profile_info->fullname . ' ' . lang('request_date') . strftime(config_item('date_format'), strtotime($advance_salary_info->request_date)),
            'value2' => $status . ' ' . lang('amount') . ': ' . $advance_salary_info->advance_amount,
        );
// Update into tbl_project
        $this->payroll_model->_table_name = "tbl_activities"; //table name
        $this->payroll_model->_primary_key = "activities_id";
        $this->payroll_model->save($activities);

        $type = "success";
        $message = lang('advance_salary_status_update');
        set_message($type, $message);
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('admin/payroll/advance_salary');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    function send_email_by_status($advance_salary_info, $approve = null)
    {
        $user_info = $this->payroll_model->check_by(array('user_id' => $advance_salary_info->user_id), 'tbl_users');
        $curency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
        $advance_salary_email = config_item('advance_salary_email');
        if (!empty($advance_salary_email) && $advance_salary_email == 1) {

            if (!empty($approve)) {
                $email_template = email_templates(array('email_group' => 'advance_salary_approve_email'), $advance_salary_info->user_id, true);
                $description = 'not_advance_salary_approve';
            } else {
                $email_template = email_templates(array('email_group' => 'advance_salary_reject_email'), $advance_salary_info->user_id, true);
                $description = 'not_advance_salary_reject';
            }
            $message = $email_template->template_body;
            $subject = $email_template->subject;
            $advance_amount = str_replace("{AMOUNT}", display_money($advance_salary_info->advance_amount, $curency->symbol), $message);
            $deduct_month = str_replace("{DEDUCT_MOTNH}", date('Y M', strtotime('deduct_month')), $advance_amount);
            $message = str_replace("{SITE_NAME}", config_item('company_name'), $deduct_month);
            $data['message'] = $message;
            $message = $this->load->view('email_template', $data, TRUE);

            $params['subject'] = $subject;
            $params['message'] = $message;
            $params['resourceed_file'] = '';
            $params['recipient'] = $user_info->email;

            $this->payroll_model->send_email($params);
        } else {
            return true;
        }
        $notifyUser = array($user_info->user_id);
        if (!empty($notifyUser)) {
            foreach ($notifyUser as $v_user) {
                add_notification(array(
                    'to_user_id' => $v_user,
                    'description' => $description,
                    'icon' => 'cc-mastercard',
                    'link' => 'admin/payroll/view_advance_salary/' . $advance_salary_info->advance_salary_id,
                    'value' => display_money($advance_salary_info->advance_amount, $curency->symbol),
                ));
            }
        }
        if (!empty($notifyUser)) {
            show_notification($notifyUser);
        }
    }

    public function make_payment($user_id = NULL, $departments_id = NULL, $payment_month = NULL)
    {
        $data['title'] = "Make Payment";
// retrive all data from department table
        $data['all_department_info'] = $this->db->get('tbl_departments')->result();
        if ($user_id != 0 && !empty($payment_month)) {
// check payment history by employee id
            $check_existing_payment = $this->db->where('user_id', $user_id)->get('tbl_salary_payment')->result();

            $data['user_id'] = $user_id;
            $data['staff_details'] = get_staff_details($user_id);
            $total_slary_amount = 0;
            if (!empty($check_existing_payment)) {
                foreach ($check_existing_payment as $key => $v_paymented_id) {
                    $salary_payment_id = $v_paymented_id->salary_payment_id;
                    $data['emp_salary_info'] = $this->payroll_model->get_salary_payment_info($salary_payment_id);
                    $data['salary_payment_info'][] = $this->payroll_model->get_salary_payment_info($salary_payment_id, true);

                    $this->payroll_model->_table_name = "tbl_salary_payment_details"; // table name
                    $this->payroll_model->_order_by = "salary_payment_id"; // $id
                    $salary_payment_history = $this->db->where('salary_payment_id', $salary_payment_id)->get('tbl_salary_payment_details')->result();
                    if (!empty($salary_payment_history)) {
                        foreach ($salary_payment_history as $v_payment_history) {
                            if (is_numeric($v_payment_history->salary_payment_details_value)) {
                                if ($v_payment_history->salary_payment_details_label == 'overtime_salary') {
                                    $rate = $v_payment_history->salary_payment_details_value;
                                } elseif ($v_payment_history->salary_payment_details_label == 'hourly_rates') {
                                    $rate = $v_payment_history->salary_payment_details_value;
                                }
                                $total_slary_amount += $v_payment_history->salary_payment_details_value;
                            }
                        }
                    }
                    $salary_allowance_info = $this->db->where('salary_payment_id', $salary_payment_id)->get('tbl_salary_payment_allowance')->result();
                    $total_allowance = 0;
                    if (!empty($salary_allowance_info)) {
                        foreach ($salary_allowance_info as $v_salary_allowance_info) {
                            $total_allowance += $v_salary_allowance_info->salary_payment_allowance_value;
                        }
                    }
                    if (!empty($rate)) {
                        $rate = $rate;
                    } else {
                        $rate = 0;
                    }

                    $data['total_paid_amount'][] = $total_slary_amount + $total_allowance - $rate;
                    $salary_deduction_info = $this->db->where('salary_payment_id', $salary_payment_id)->get('tbl_salary_payment_deduction')->result();
                    $total_deduction = 0;
                    if (!empty($salary_deduction_info)) {
                        foreach ($salary_deduction_info as $v_salary_deduction_info) {
                            $total_deduction += $v_salary_deduction_info->salary_payment_deduction_value;
                        }
                    }
                    $data['total_deduction'][] = $total_deduction;
                }
            }
            $data['payment_month'] = $payment_month;
            $data['payment_flag'] = 1;
            $data['departments_id'] = $departments_id;
// get employee info by employee id
            $data['employee_info'] = $this->payroll_model->get_emp_salary_list($user_id);
// get all allowance info by salary template id
            if (!empty($data['employee_info']->salary_template_id)) {
                $data['allowance_info'] = $this->get_allowance_info_by_id($data['employee_info']->salary_template_id);
// get all deduction info by salary template id
                $data['deduction_info'] = $this->get_deduction_info_by_id($data['employee_info']->salary_template_id);
// get all overtime info by month and employee id
                $data['overtime_info'] = $this->get_overtime_info_by_id($user_id, $data['payment_month']);
            }
// get all advance salary info by month and employee id
            $data['advance_salary'] = $this->get_advance_salary_info_by_id($user_id, $data['payment_month']);
// get award info by employee id and payment month
// get award info by employee id and payment date
            $this->payroll_model->_table_name = 'tbl_employee_award';
            $this->payroll_model->_order_by = 'user_id';
            $data['award_info'] = $this->payroll_model->get_by(array('user_id' => $user_id, 'award_date' => $data['payment_month']), FALSE);
// check hourly payment info
// if exist count total hours in a month
// get hourly payment info by id
            if (!empty($data['employee_info']->hourly_rate_id)) {
                $data['total_hours'] = $this->get_total_hours_in_month($user_id, $data['payment_month']);
            }
            if (!empty($data['total_hours'])) {
                if ($data['total_hours'] == 0 && $data['total_minutes'] == 0) {
                    $type = 'error';
                    $message = '<strong>' . $data['employee_info']->fullname . ' ' . '</strong>' . lang('working_hour_empty');
                    set_message($type, $message);
                    redirect('admin/payroll/make_payment/' . '0' . '/' . $data['employee_info']->departments_id . '/' . $data['payment_month']);
                }
            }
        } else {
            $flag = $this->input->post('flag', TRUE);
            if (!empty($flag) || !empty($departments_id)) { // check employee id is empty or not
                $data['flag'] = 1;
                if (!empty($departments_id)) {
                    $data['departments_id'] = $departments_id;
                } else {
                    $data['departments_id'] = $this->input->post('departments_id', TRUE);
                }
                if (!empty($payment_month)) {
                    $data['payment_month'] = $payment_month;
                } else {
                    $data['payment_month'] = $this->input->post('payment_month', TRUE);
                }
// get all designation info by Department id
                $designation_info = $this->db->where('departments_id', $data['departments_id'])->get('tbl_designations')->result();
                if (!empty($designation_info)) {
                    foreach ($designation_info as $v_designatio) {
                        $data['employee_info'][] = $this->payroll_model->get_emp_salary_list('', $v_designatio->designations_id);
                        $employee_info = $this->payroll_model->get_emp_salary_list('', $v_designatio->designations_id);
                        foreach ($employee_info as $value) {

// get all allowance info by salary template id
                            if (!empty($value->salary_template_id)) {
                                $data['allowance_info'][$value->user_id] = $this->get_allowance_info_by_id($value->salary_template_id);
// get all deduction info by salary template id
                                $data['deduction_info'][$value->user_id] = $this->get_deduction_info_by_id($value->salary_template_id);
// get all overtime info by month and employee id
                                $data['overtime_info'][$value->user_id] = $this->get_overtime_info_by_id($value->user_id, $data['payment_month']);
                            }
// get all advance salary info by month and employee id
                            $data['advance_salary'][$value->user_id] = $this->get_advance_salary_info_by_id($value->user_id, $data['payment_month']);
// get award info by employee id and payment month
                            $data['award_info'][$value->user_id] = $this->get_award_info_by_id($value->user_id, $data['payment_month']);
// check hourly payment info
// if exist count total hours in a month
// get hourly payment info by id
                            if (!empty($value->hourly_rate_id)) {
                                $data['total_hours'][$value->user_id] = $this->get_total_hours_in_month($value->user_id, $data['payment_month']);
                            }
                        }
                    }
                }
            }
        }
        $data['subview'] = $this->load->view('admin/payroll/make_payment', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function get_allowance_info_by_id($salary_template_id)
    {
        $salary_allowance_info = $this->db->where('salary_template_id', $salary_template_id)->get('tbl_salary_allowance')->result();
        $total_allowance = 0;
        foreach ($salary_allowance_info as $v_allowance_info) {
            $total_allowance += $v_allowance_info->allowance_value;
        }
        return $total_allowance;
    }

    public function get_deduction_info_by_id($salary_template_id)
    {
        $salary_deduction_info = $this->db->where('salary_template_id', $salary_template_id)->get('tbl_salary_deduction')->result();
        $total_deduction = 0;
        foreach ($salary_deduction_info as $v_deduction_info) {
            $total_deduction += $v_deduction_info->deduction_value;
        }
        return $total_deduction;
    }

    public function get_total_hours_in_month($user_id, $payment_month)
    {

        $start_date = $payment_month . '-' . '01';
        $end_date = $payment_month . '-' . '31';
        $attendance_info = $this->payroll_model->get_attendance_info_by_date($start_date, $end_date, $user_id); // get all report by start date and in date


        $total_hh = 0;
        $total_mm = 0;
        foreach ($attendance_info as $v_clock_time) {
// calculate the start timestamp
            $startdatetime = strtotime($v_clock_time->date_in . " " . $v_clock_time->clockin_time);
// calculate the end timestamp
            $enddatetime = strtotime($v_clock_time->date_out . " " . $v_clock_time->clockout_time);
// calulate the difference in seconds
            $difference = $enddatetime - $startdatetime;
            $years = abs(floor($difference / 31536000));
            $days = abs(floor(($difference - ($years * 31536000)) / 86400));
            $hours = abs(floor(($difference - ($years * 31536000) - ($days * 86400)) / 3600));
            $mins = abs(floor(($difference - ($years * 31536000) - ($days * 86400) - ($hours * 3600)) / 60));#floor($difference / 60);
            $total_mm += $mins;
            $total_hh += $hours;
        }
        if ($total_mm > 59) {
            $total_hh += intval($total_mm / 60);
            $total_mm = intval($total_mm % 60);
        }
        $result['total_hours'] = $total_hh;
        $result['total_minutes'] = $total_mm;
        return $result;
    }

    public function get_advance_salary_info_by_id($user_id, $payment_month)
    {

        $advance_salary_info = $this->payroll_model->get_advance_salary_info_by_date($payment_month, '', $user_id); // get all report by start date and in date
        $advance_amount = 0;
        foreach ($advance_salary_info as $v_advance_salary) {
            $advance_amount += $v_advance_salary->advance_amount;
        }
        $result['advance_amount'] = $advance_amount;
        return $result;

    }

    public function get_award_info_by_id($user_id, $payment_month)
    {
        $this->payroll_model->_table_name = 'tbl_employee_award';
        $this->payroll_model->_order_by = 'user_id';
        $award_info = $this->payroll_model->get_by(array('user_id' => $user_id, 'award_date' => $payment_month), FALSE);
        $result['award_amount'] = 0;
        foreach ($award_info as $v_award_info) {
            $result['award_amount'] += $v_award_info->award_amount;
        }
        if (!empty($result)) {
            return $result;
        }
    }

    public function get_overtime_info_by_id($user_id, $payment_month)
    {
        $start_date = $payment_month . '-' . '01';
        $end_date = $payment_month . '-' . '31';
        $this->payroll_model->_table_name = "tbl_overtime"; //table name
        $this->payroll_model->_order_by = "overtime_id";
        $all_overtime_info = $this->payroll_model->get_by(array('overtime_date >=' => $start_date, 'overtime_date <=' => $end_date, 'user_id' => $user_id), FALSE); // get all report by start date and in date
        $hh = 0;
        $mm = 0;
        foreach ($all_overtime_info as $overtime_info) {
            $hh += $overtime_info->overtime_hours;
            $mm += date('i', strtotime($overtime_info->overtime_hours));
        }
        if ($hh > 1 && $hh < 10 || $mm > 1 && $mm < 10) {
            $total_mm = '0' . $mm;
            $total_hh = '0' . $hh;
        } else {
            $total_mm = $mm;
            $total_hh = $hh;
        }
        if ($total_mm > 59) {
            $total_hh += intval($total_mm / 60);
            $total_mm = intval($total_mm % 60);
        }
        $result['overtime_hours'] = $total_hh;
        $result['overtime_minutes'] = $total_mm;
        return $result;
    }

    public function view_payment_details($user_id, $payment_month)
    {
        if (empty($user_id)) {
            $type = "error";
            $message = lang('operation_failed');
            set_message($type, $message);
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/payroll/make_payment');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
        $data['title'] = 'Payment Salary Details';
        $data['payment_month'] = $payment_month;
        $data['payment_flag'] = 1;
// get employee info by employee id
        $data['employee_info'] = $this->payroll_model->get_emp_salary_list($user_id);
// get all allowance info by salary template id
        if (!empty($data['employee_info']->salary_template_id)) {
            $data['allowance_info'] = $this->db->where('salary_template_id', $data['employee_info']->salary_template_id)->get('tbl_salary_allowance')->result();
// get all deduction info by salary template id
            $data['deduction_info'] = $this->db->where('salary_template_id', $data['employee_info']->salary_template_id)->get('tbl_salary_deduction')->result();
// get all overtime info by month and employee id
            $data['overtime_info'] = $this->get_overtime_info_by_id($user_id, $data['payment_month']);
        }

// get all advance salary info by month and employee id
        $data['advance_salary'] = $this->get_advance_salary_info_by_id($user_id, $data['payment_month']);

// get award info by employee id and payment month
// get award info by employee id and payment date
        $this->payroll_model->_table_name = 'tbl_employee_award';
        $this->payroll_model->_order_by = 'user_id';
        $data['award_info'] = $this->payroll_model->get_by(array('user_id' => $user_id, 'award_date' => $data['payment_month']), FALSE);
// check hourly payment info
// if exist count total hours in a month
// get hourly payment info by id
        if (!empty($data['employee_info']->hourly_rate_id)) {
            $data['total_hours'] = $this->get_total_hours_in_month($user_id, $data['payment_month']);
        }

        $data['subview'] = $this->load->view('admin/payroll/view_payment_details', $data, FALSE);
        $this->load->view('admin/_layout_modal_lg', $data);
    }

    public function get_payment($id = NULL)
    {
// input data
        $data = $this->payroll_model->array_from_post(array('user_id', 'payment_month', 'fine_deduction', 'payment_type', 'comments'));
//        // save into tbl employee paymenet
        $this->payroll_model->_table_name = "tbl_salary_payment"; // table name
        $this->payroll_model->_primary_key = "salary_payment_id"; // $id
        if (!empty($id)) {
            $details_data['salary_payment_id'] = $id;
            $this->payroll_model->save($data, $id);
        } else {
            $data['deduct_from'] = 0;
            $details_data['salary_payment_id'] = $this->payroll_model->save($data);
        }
// get employee info by employee id
        $employee_info = $this->payroll_model->get_emp_salary_list($data['user_id']);

// get all allowance info by salary template id
        if (!empty($employee_info->salary_template_id)) {
            $salary_payment_details_label[] = lang('salary_grade');
            $salary_payment_details_value[] = $employee_info->salary_grade;

            $salary_payment_details_label[] = lang('basic_salary');
            $salary_payment_details_value[] = $employee_info->basic_salary;
            if (!empty($employee_info->overtime_salary)) {
                $salary_payment_details_label[] = 'overtime_salary';
                $salary_payment_details_value[] = $employee_info->overtime_salary;
            }
// ************ Save all allwance info **********
            $this->payroll_model->_table_name = 'tbl_salary_allowance';
            $this->payroll_model->_order_by = 'salary_template_id';
            $allowance_info = $this->payroll_model->get_by(array('salary_template_id' => $employee_info->salary_template_id), FALSE);
            if (!empty($allowance_info)) {
                foreach ($allowance_info as $v_allowance_info) {
                    $aldata['salary_payment_id'] = $details_data['salary_payment_id'];
                    $aldata['salary_payment_allowance_label'] = $v_allowance_info->allowance_label;
                    $aldata['salary_payment_allowance_value'] = $v_allowance_info->allowance_value;

//  save into tbl employee paymenet
                    $this->payroll_model->_table_name = "tbl_salary_payment_allowance"; // table name
                    $this->payroll_model->_primary_key = "salary_payment_allowance_id"; // $id
                    $this->payroll_model->save($aldata);
                }
            }
// get all deduction info by salary template id
// ************ Save all deduction info **********
            $this->payroll_model->_table_name = 'tbl_salary_deduction';
            $this->payroll_model->_order_by = 'salary_template_id';
            $deduction_info = $this->payroll_model->get_by(array('salary_template_id' => $employee_info->salary_template_id), FALSE);
            if (!empty($deduction_info)) {
                foreach ($deduction_info as $v_deduction_info) {
                    $salary_payment_deduction_label[] = $v_deduction_info->deduction_label;
                    $salary_payment_deduction_value[] = $v_deduction_info->deduction_value;
                }
            }
// ************ Save all Overtime info **********
// get all overtime info by month and employee id
            $overtime_info = $this->get_overtime_info_by_id($data['user_id'], $data['payment_month']);
            $salary_payment_details_label[] = lang('overtime_hour');
            $salary_payment_details_value[] = $overtime_info['overtime_hours'] . ':' . $overtime_info['overtime_minutes'];

            $overtime_hour = $overtime_info['overtime_hours'];
            $overtime_minutes = $overtime_info['overtime_minutes'];
            if ($overtime_hour > 0) {
                $ov_hours_ammount = $overtime_minutes * $employee_info->overtime_salary;
            } else {
                $ov_hours_ammount = 0;
            }
            if ($overtime_minutes > 0) {
                $ov_amount = round($employee_info->overtime_salary / 60, 2);
                $ov_minutes_ammount = $overtime_minutes * $ov_amount;
            } else {
                $ov_minutes_ammount = 0;
            }
            $overtime_amount = $ov_hours_ammount + $ov_minutes_ammount;
            $salary_payment_details_label[] = lang('overtime_amount');
            $salary_payment_details_value[] = $overtime_amount;
        }
// ************ Save all Advance Salary info **********
// get all advance salary info by month and employee id
        $advance_salary = $this->get_advance_salary_info_by_id($data['user_id'], $data['payment_month']);
        if ($advance_salary['advance_amount']) {
            $salary_payment_deduction_label[] = lang('advance_amount');
            $salary_payment_deduction_value[] = $advance_salary['advance_amount'];
            $advance_salary_info = $this->payroll_model->check_by(array('user_id' => $data['user_id'], 'deduct_month' => $data['payment_month']), 'tbl_advance_salary');
            if (!empty($advance_salary_info)) {
                $this->payroll_model->_table_name = "tbl_advance_salary"; // table name
                $this->payroll_model->_primary_key = "advance_salary_id"; // $id
                $advnce_slry_date['status'] = 3;
                $this->payroll_model->save($advnce_slry_date, $advance_salary_info->advance_salary_id);
            }
        }
// ************ Save all Hourly info **********
// check hourly payment info
// if exist count total hours in a month
// get hourly payment info by id
        if (!empty($employee_info->hourly_rate_id)) {
            $total_hours = $this->get_total_hours_in_month($data['user_id'], $data['payment_month']);
            $salary_payment_details_label[] = lang('hourly_grade');
            $salary_payment_details_value[] = $employee_info->hourly_grade;

            $salary_payment_details_label[] = 'hourly_rates';
            $salary_payment_details_value[] = $employee_info->hourly_rate;

            $salary_payment_details_label[] = lang('total_hour');
            $salary_payment_details_value[] = $total_hours['total_hours'] . ':' . $total_hours['total_minutes'];

            $total_hour = $total_hours['total_hours'];
            $total_minutes = $total_hours['total_minutes'];
            if ($total_hour > 0) {
                $hours_ammount = $total_hour * $employee_info->hourly_rate;
            } else {
                $hours_ammount = 0;
            }
            if ($total_minutes > 0) {
                $amount = round($employee_info->hourly_rate / 60, 2);
                $minutes_ammount = $total_minutes * $amount;
            } else {
                $minutes_ammount = 0;
            }
            $total_hours_amount = $hours_ammount + $minutes_ammount;
            $salary_payment_details_label[] = lang('amount');
            $salary_payment_details_value[] = $total_hours_amount;
        }
// get award info by employee id and payment date
        $this->payroll_model->_table_name = 'tbl_employee_award';
        $this->payroll_model->_order_by = 'user_id';
        $award_info = $this->payroll_model->get_by(array('user_id' => $data['user_id'], 'award_date' => $data['payment_month']), FALSE);
        if (!empty($award_info)) {
            foreach ($award_info as $v_award_info) {
                $salary_payment_details_label[] = lang('award_name') . '
<small> ( ' . $v_award_info->award_name . ' )</small>';
                $salary_payment_details_value[] = $v_award_info->award_amount;
            }
        }
        if (!empty($salary_payment_details_label)) {
            foreach ($salary_payment_details_label as $key => $payment_label) {
                $details_data['salary_payment_details_label'] = $payment_label;
                $details_data['salary_payment_details_value'] = $salary_payment_details_value[$key];

//  save into tbl employee paymenet
                $this->payroll_model->_table_name = "tbl_salary_payment_details"; // table name
                $this->payroll_model->_primary_key = "salary_payment_details_id"; // $id
                $this->payroll_model->save($details_data);
            }
        }
        if (!empty($salary_payment_deduction_label)) {
            foreach ($salary_payment_deduction_label as $dkey => $deduction_label) {
                $ddetails_data['salary_payment_id'] = $details_data['salary_payment_id'];
                $ddetails_data['salary_payment_deduction_label'] = $deduction_label;
                $ddetails_data['salary_payment_deduction_value'] = $salary_payment_deduction_value[$dkey];

//  save into tbl employee paymenet
                $this->payroll_model->_table_name = "tbl_salary_payment_deduction"; // table name
                $this->payroll_model->_primary_key = "salary_payment_deduction_id"; // $id
                $this->payroll_model->save($ddetails_data);
            }
        }
        if (!empty($employee_info->hourly_rate_id) || !empty($employee_info->salary_template_id)) {

            $deduct_from_account = $this->input->post('deduct_from_account', true);
            if (!empty($deduct_from_account)) {
                $account_id = $this->input->post('account_id', true);
                if (empty($account_id)) {
                    $account_id = config_item('default_account');
                }
                if (!empty($account_id)) {
                    $reference = lang('salary_month') . ' : ' . date('F Y', strtotime($data['payment_month'])) . ' ' . lang('salary_payment') . ' ' . lang('for') . ' ' . $employee_info->fullname . ' ' . lang('and') . ' ' . lang('comments') . ': ' . $data['comments'];
// save into tbl_transaction
                    $tr_data = array(
                        'name' => lang('salary_payment') . ' ' . lang('for') . ' ' . $employee_info->fullname,
                        'type' => 'Expense',
                        'amount' => $this->input->post('payment_amount', TRUE),
                        'debit' => $this->input->post('payment_amount', TRUE),
                        'date' => date('Y-m-d'),
                        'paid_by' => '0',
                        'payment_methods_id' => $this->input->post('payment_type', TRUE),
                        'reference' => lang('salary_month') . ' ' . $this->input->post('payment_month'),
                        'notes' => lang('this_expense_from_salary_payment', $reference),
                        'permission' => 'all',
                    );
                    $account_info = $this->payroll_model->check_by(array('account_id' => $account_id), 'tbl_accounts');
                    if (!empty($account_info)) {
                        $ac_data['balance'] = $account_info->balance - $tr_data['amount'];
                        $this->payroll_model->_table_name = "tbl_accounts"; //table name
                        $this->payroll_model->_primary_key = "account_id";
                        $this->payroll_model->save($ac_data, $account_info->account_id);

                        $aaccount_info = $this->payroll_model->check_by(array('account_id' => $account_id), 'tbl_accounts');
                        $tr_data['total_balance'] = $aaccount_info->balance;
                        $tr_data['account_id'] = $account_id;
// save into tbl_transaction
                        $this->payroll_model->_table_name = "tbl_transactions"; //table name
                        $this->payroll_model->_primary_key = "transactions_id";
                        $return_id = $this->payroll_model->save($tr_data);

// save into activities
                        $activities = array(
                            'user' => $this->session->userdata('user_id'),
                            'module' => 'transactions',
                            'module_field_id' => $return_id,
                            'activity' => 'activity_new_expense',
                            'icon' => 'fa-building-o',
                            'link' => 'admin/transactions/view_details/' . $return_id,
                            'value1' => $account_info->account_name,
                            'value2' => $this->input->post('payment_amount', TRUE),
                        );
// Update into tbl_project
                        $this->payroll_model->_table_name = "tbl_activities"; //table name
                        $this->payroll_model->_primary_key = "activities_id";
                        $this->payroll_model->save($activities);

                        $this->payroll_model->_table_name = "tbl_salary_payment"; // table name
                        $this->payroll_model->_primary_key = "salary_payment_id"; // $id
                        $deduct_account['deduct_from'] = $account_id;
                        $this->payroll_model->save($deduct_account, $details_data['salary_payment_id']);
                    }
                }
            }
// save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'payroll',
                'module_field_id' => $id,
                'activity' => 'activity_make_payment',
                'icon' => 'fa-list-ul',
                'value1' => $employee_info->fullname,
                'value2' => date('F Y', strtotime($data['payment_month'])),
            );
// Update into tbl_project
            $this->payroll_model->_table_name = "tbl_activities"; //table name
            $this->payroll_model->_primary_key = "activities_id";
            $this->payroll_model->save($activities);
        }

        $type = 'success';
        $message = lang('payment_information_update');
        set_message($type, $message);
        redirect('admin/payroll/make_payment/0/' . $employee_info->departments_id . '/' . $data['payment_month']);
    }

    public function salary_payment_details($salary_payment_id)
    {
        $data['title'] = lang('manage_salary_details') . ' ' . lang('details');

        $data['salary_payment_info'] = $this->payroll_model->get_salary_payment_info($salary_payment_id);

        $this->payroll_model->_table_name = "tbl_salary_payment_details"; // table name
        $this->payroll_model->_order_by = "salary_payment_id"; // $id
        $data['salary_payment_details_info'] = $this->payroll_model->get_by(array('salary_payment_id' => $salary_payment_id), FALSE);

        $this->payroll_model->_table_name = "tbl_salary_payment_allowance"; // table name
        $this->payroll_model->_order_by = "salary_payment_id"; // $id
        $data['allowance_info'] = $this->payroll_model->get_by(array('salary_payment_id' => $salary_payment_id), FALSE);

        $this->payroll_model->_table_name = "tbl_salary_payment_deduction"; // table name
        $this->payroll_model->_order_by = "salary_payment_id"; // $id
        $data['deduction_info'] = $this->payroll_model->get_by(array('salary_payment_id' => $salary_payment_id), FALSE);

        $data['subview'] = $this->load->view('admin/payroll/salary_payment_details', $data, FALSE);
        $this->load->view('admin/_layout_modal_lg', $data);
    }

    public function salary_payment_details_pdf($salary_payment_id)
    {

        $data['salary_payment_info'] = $this->payroll_model->get_salary_payment_info($salary_payment_id);

        $this->payroll_model->_table_name = "tbl_salary_payment_details"; // table name
        $this->payroll_model->_order_by = "salary_payment_id"; // $id
        $data['salary_payment_details_info'] = $this->payroll_model->get_by(array('salary_payment_id' => $salary_payment_id), FALSE);

        $this->payroll_model->_table_name = "tbl_salary_payment_allowance"; // table name
        $this->payroll_model->_order_by = "salary_payment_id"; // $id
        $data['allowance_info'] = $this->payroll_model->get_by(array('salary_payment_id' => $salary_payment_id), FALSE);

        $this->payroll_model->_table_name = "tbl_salary_payment_deduction"; // table name
        $this->payroll_model->_order_by = "salary_payment_id"; // $id
        $data['deduction_info'] = $this->payroll_model->get_by(array('salary_payment_id' => $salary_payment_id), FALSE);
// get all employee salary info  by id
        $viewfile = $this->load->view('admin/payroll/salary_payment_details_pdf', $data, TRUE);

        $this->load->helper('dompdf');
        pdf_create($viewfile, slug_it(lang('salary_details') . '- ' . $data['salary_payment_info']->fullname));
    }

    public function generate_payslip()
    {
        $data['title'] = "Generate Payslip";
// retrive all data from department table
        $this->payroll_model->_table_name = "tbl_departments"; //table name
        $this->payroll_model->_order_by = "departments_id";
        $data['all_department_info'] = $this->payroll_model->get();
        $flag = $this->input->post('flag', TRUE);
        if (!empty($flag)) { // check employee id is empty or not
            $data['flag'] = 1;
            $data['departments_id'] = $this->input->post('departments_id', TRUE);
            $data['payment_month'] = $this->input->post('payment_month', TRUE);

// get all designation info by Department id
            $this->payroll_model->_table_name = 'tbl_designations';
            $this->payroll_model->_order_by = 'designations_id';
            $designation_info = $this->payroll_model->get_by(array('departments_id' => $data['departments_id']), FALSE);
            if (!empty($designation_info)) {
                foreach ($designation_info as $v_designatio) {
                    $data['employee_info'][] = $this->payroll_model->get_emp_salary_list('', $v_designatio->designations_id);
                    $employee_info = $this->payroll_model->get_emp_salary_list('', $v_designatio->designations_id);
                    foreach ($employee_info as $value) {
// get all allowance info by salary template id
                        if (!empty($value->salary_template_id)) {
                            $data['allowance_info'][$value->user_id] = $this->get_allowance_info_by_id($value->salary_template_id);
// get all deduction info by salary template id
                            $data['deduction_info'][$value->user_id] = $this->get_deduction_info_by_id($value->salary_template_id);
// get all overtime info by month and employee id
                            $data['overtime_info'][$value->user_id] = $this->get_overtime_info_by_id($value->user_id, $data['payment_month']);
                        }
// get all advance salary info by month and employee id
                        $data['advance_salary'][$value->user_id] = $this->get_advance_salary_info_by_id($value->user_id, $data['payment_month']);
// get award info by employee id and payment month
                        $data['award_info'][$value->user_id] = $this->get_award_info_by_id($value->user_id, $data['payment_month']);
// check hourly payment info
// if exist count total hours in a month
// get hourly payment info by id
                        if (!empty($value->hourly_rate_id)) {
                            $data['total_hours'][$value->user_id] = $this->get_total_hours_in_month($value->user_id, $data['payment_month']);
                        }
                    }
                }
            }
        }

        $data['subview'] = $this->load->view('admin/payroll/generate_payslip', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function receive_generated($salary_payment_id)
    {
// check existing_recept_no by where
        $where = array('salary_payment_id' => $salary_payment_id);
        $check_existing_recipt_no = $this->payroll_model->check_by($where, 'tbl_salary_payslip');

        if (!empty($check_existing_recipt_no)) {
            $data['payslip_number'] = $check_existing_recipt_no->payslip_number;
        } else {
            $this->payroll_model->_table_name = "tbl_salary_payslip"; //table name
            $this->payroll_model->_primary_key = "payslip_id";
            $payslip_id = $this->payroll_model->save($where);

            $pdata['payslip_number'] = date('Ym') . $payslip_id;
            $this->payroll_model->save($pdata, $payslip_id);

            $payslip_email = config_item('payslip_email');
            if (!empty($payslip_email) && $payslip_email == 1) {
                $this->send_payslip($salary_payment_id, true);
            }
            redirect('admin/payroll/receive_generated/' . $salary_payment_id);

        }
        $data['title'] = lang('generate_payslip');
        $data['employee_salary_info'] = $this->payroll_model->get_salary_payment_info($salary_payment_id);

        $this->payroll_model->_table_name = "tbl_salary_payment_details"; // table name
        $this->payroll_model->_order_by = "salary_payment_id"; // $id
        $data['salary_payment_details_info'] = $this->payroll_model->get_by(array('salary_payment_id' => $salary_payment_id), FALSE);

        $this->payroll_model->_table_name = "tbl_salary_payment_allowance"; // table name
        $this->payroll_model->_order_by = "salary_payment_id"; // $id
        $data['allowance_info'] = $this->payroll_model->get_by(array('salary_payment_id' => $salary_payment_id), FALSE);

        $this->payroll_model->_table_name = "tbl_salary_payment_deduction"; // table name
        $this->payroll_model->_order_by = "salary_payment_id"; // $id
        $data['deduction_info'] = $this->payroll_model->get_by(array('salary_payment_id' => $salary_payment_id), FALSE);

        $data['subview'] = $this->load->view('admin/payroll/payslip_info', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function send_payslip($salary_payment_id, $auto = null)
    {
        $where = array('salary_payment_id' => $salary_payment_id);
        $check_existing_recipt_no = $this->payroll_model->check_by($where, 'tbl_salary_payslip');
        $data['payslip_number'] = $check_existing_recipt_no->payslip_number;

        $data['employee_salary_info'] = $this->payroll_model->get_salary_payment_info($salary_payment_id);

        $this->payroll_model->_table_name = "tbl_salary_payment_details"; // table name
        $this->payroll_model->_order_by = "salary_payment_id"; // $id
        $data['salary_payment_details_info'] = $this->payroll_model->get_by(array('salary_payment_id' => $salary_payment_id), FALSE);

        $this->payroll_model->_table_name = "tbl_salary_payment_allowance"; // table name
        $this->payroll_model->_order_by = "salary_payment_id"; // $id
        $data['allowance_info'] = $this->payroll_model->get_by(array('salary_payment_id' => $salary_payment_id), FALSE);

        $this->payroll_model->_table_name = "tbl_salary_payment_deduction"; // table name
        $this->payroll_model->_order_by = "salary_payment_id"; // $id
        $data['deduction_info'] = $this->payroll_model->get_by(array('salary_payment_id' => $salary_payment_id), FALSE);
// get all employee salary info  by id
        $viewfile = $this->load->view('admin/payroll/payslip_info', $data, TRUE);

        $email_template = email_templates(array('email_group' => 'payslip_generated_email'));

        $message = $email_template->template_body;
        $subject = $email_template->subject;

        $NAME = str_replace("{NAME}", $data['employee_salary_info']->fullname, $message);
        $month_year = str_replace("{MONTH_YEAR}", date('F  Y', strtotime($data['employee_salary_info']->payment_month)), $NAME);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $month_year);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = $viewfile;

        $login_info = $this->payroll_model->check_by(array('user_id' => $data['employee_salary_info']->user_id), 'tbl_users');
        $params['recipient'] = $login_info->email;

        $result = $this->payroll_model->send_email($params);

        if (!empty($result)) {
// save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'payroll',
                'module_field_id' => $salary_payment_id,
                'activity' => 'activity_payslip_send',
                'icon' => 'fa-list-ul',
                'value1' => $data['employee_salary_info']->fullname,
                'value2' => date('F Y', strtotime($data['employee_salary_info']->payment_month)),
            );
// Update into tbl_project
            $this->payroll_model->_table_name = "tbl_activities"; //table name
            $this->payroll_model->_primary_key = "activities_id";
            $this->payroll_model->save($activities);

            $type = 'success';
            $message = lang('payslip_information_successfully_send');
        } else {
            $type = 'error';
            $message = lang('something_went_wrong');
        }
        if ($auto) {
            return true;
        } else {
            set_message($type, $message);
            redirect('admin/payroll/make_payment');
        }

    }

    public function provident_fund()
    {
        $data['title'] = "Provident Found Details";
// active check with current month
        $data['current_month'] = date('m');

        if ($this->input->post('year', TRUE)) { // if input year
            $data['year'] = $this->input->post('year', TRUE);
        } else { // else current year
            $data['year'] = date('Y'); // get current year
        }
// get all expense list by year and month
        $data['provident_fund_info'] = $this->get_provident_fund_info($data['year']);

        $data['subview'] = $this->load->view('admin/payroll/provident_fund_info', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function get_provident_fund_info($year, $month = NULL)
    {// this function is to create get monthy recap report
        if (!empty($month)) {
            if ($month >= 1 && $month <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                $start_date = $year . "-" . '0' . $month;
                $end_date = $year . "-" . '0' . $month;
            } else {
                $start_date = $year . "-" . $month;
                $end_date = $year . "-" . $month;
            }
            $provident_fund_info = $this->payroll_model->get_provident_fund_info_by_date($start_date, $end_date); // get all report by start date and in date
        } else {
            for ($i = 1; $i <= 12; $i++) { // query for months
                if ($i >= 1 && $i <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                    $start_date = $year . "-" . '0' . $i;
                    $end_date = $year . "-" . '0' . $i;
                } else {
                    $start_date = $year . "-" . $i;
                    $end_date = $year . "-" . $i;
                }
                $provident_fund_info[$i] = $this->payroll_model->get_provident_fund_info_by_date($start_date, $end_date); // get all report by start date and in date
            }
        }

        return $provident_fund_info; // return the result
    }

    public function provident_fund_pdf($year, $month)
    {

        $data['provident_fund_info'] = $this->get_provident_fund_info($year, $month);

        $month_name = date('F', strtotime($year . '-' . $month)); // get full name of month by date query
        $data['monthyaer'] = $month_name . '  ' . $year;

        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/payroll/provident_fund_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it(lang('provident_found_report') . ' - ' . $data['monthyaer']));
    }

    public function payroll_summary()
    {
        $data['title'] = lang('payroll_summary');
        $search_type = $this->input->post('search_type', true);
        if (!empty($search_type)) {
            $data['search_type'] = $search_type;
            if ($search_type == 'employee') {
                $data['user_id'] = $this->input->post('user_id', true);
            }
            if ($search_type == 'month') {
                $data['by_month'] = $this->input->post('by_month', true);
            }
            if ($search_type == 'period') {
                $data['start_month'] = $this->input->post('start_month', true);
                $data['end_month'] = $this->input->post('end_month', true);
            }
        }
        $data['subview'] = $this->load->view('admin/payroll/payroll_summary', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function payment_historyList($user_id = null)
    {
        if (!empty($user_id)) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_salary_payment';
            $this->datatables->join_table = array('tbl_account_details', 'tbl_designations', 'tbl_departments');
            $this->datatables->join_where = array('tbl_salary_payment.user_id = tbl_account_details.user_id', 'tbl_designations.designations_id  = tbl_account_details.designations_id', 'tbl_departments.departments_id  = tbl_designations.departments_id');
            $this->datatables->column_order = array('tbl_account_details.fullname', 'tbl_account_details.employment_id', 'tbl_salary_payment.comments', 'tbl_salary_payment.payment_type', 'tbl_salary_payment.payment_month', 'tbl_salary_payment.fine_deduction', 'tbl_salary_payment.paid_date');
            $this->datatables->column_search = array('tbl_account_details.fullname', 'tbl_account_details.employment_id', 'tbl_salary_payment.comments', 'tbl_salary_payment.payment_type', 'tbl_salary_payment.payment_month', 'tbl_salary_payment.fine_deduction', 'tbl_salary_payment.paid_date');
            $this->datatables->order = array('salary_payment_id' => 'desc');

            $where = array('tbl_salary_payment.user_id' => $user_id);
            $all_payment_history = make_datatables($where);

            $pdata = array();
            foreach ($all_payment_history as $p_key => $v_history) {
                if (!empty($v_history)) {
                    $salary_payment_history = get_result('tbl_salary_payment_details', array('salary_payment_id' => $v_history->salary_payment_id));
                    $total_salary_amount = 0;
                    if (!empty($salary_payment_history)) {
                        foreach ($salary_payment_history as $v_payment_history) {
                            if (is_numeric($v_payment_history->salary_payment_details_value)) {
                                if ($v_payment_history->salary_payment_details_label == 'overtime_salary') {
                                    $rate = $v_payment_history->salary_payment_details_value;
                                } elseif ($v_payment_history->salary_payment_details_label == 'hourly_rates') {
                                    $rate = $v_payment_history->salary_payment_details_value;
                                }
                                $total_salary_amount += $v_payment_history->salary_payment_details_value;
                            }
                        }
                    }
                    $salary_allowance_info = get_result('tbl_salary_payment_allowance', array('salary_payment_id' => $v_history->salary_payment_id));
                    $total_allowance = 0;
                    if (!empty($salary_allowance_info)) {
                        foreach ($salary_allowance_info as $v_salary_allowance_info) {
                            $total_allowance += $v_salary_allowance_info->salary_payment_allowance_value;
                        }
                    }
                    if (empty($rate)) {
                        $rate = 0;
                    }
                    $salary_deduction_info = get_result('tbl_salary_payment_deduction', array('salary_payment_id' => $v_history->salary_payment_id));
                    $total_deduction = 0;
                    if (!empty($salary_deduction_info)) {
                        foreach ($salary_deduction_info as $v_salary_deduction_info) {
                            $total_deduction += $v_salary_deduction_info->salary_payment_deduction_value;
                        }
                    }

                    $total_paid_amount = $total_salary_amount + $total_allowance - $rate;

                    $action = null;
                    $psub_array = array();
                    $psub_array[] = date('F-Y', strtotime($v_history->payment_month));
                    $psub_array[] = display_date($v_history->paid_date);
                    $psub_array[] = display_money($total_paid_amount, default_currency());
                    $psub_array[] = display_money($total_deduction, default_currency());
                    $psub_array[] = display_money($net_salary = $total_paid_amount - $total_deduction, default_currency());

                    if (!empty($v_history->fine_deduction)) {
                        $fine_deduction = $v_history->fine_deduction;
                    } else {
                        $fine_deduction = 0;
                    }
                    $psub_array[] = display_money($fine_deduction, default_currency());
                    $psub_array[] = display_money($net_salary - $fine_deduction, default_currency());
                    $psub_array[] = '<a href="' . base_url() . 'admin/payroll/salary_payment_details/' . $v_history->salary_payment_id . '"
                               class="btn btn-info btn-xs" title="' . lang('view') . '" data-toggle="modal"
                               data-target="#myModal_lg"><span class="fa fa-list-alt"></span></a>';
                    $pdata[] = $psub_array;
                }
            }
            render_table($pdata, $where);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function payment_historyMonth($month = null)
    {
        if ($this->input->is_ajax_request()) {

            $this->load->model('datatables');
            $this->datatables->table = 'tbl_salary_payment';
            $this->datatables->join_table = array('tbl_account_details', 'tbl_designations', 'tbl_departments');
            $this->datatables->join_where = array('tbl_salary_payment.user_id = tbl_account_details.user_id', 'tbl_designations.designations_id  = tbl_account_details.designations_id', 'tbl_departments.departments_id  = tbl_designations.departments_id');
            $this->datatables->column_order = array('tbl_account_details.fullname', 'tbl_account_details.employment_id', 'tbl_salary_payment.comments', 'tbl_salary_payment.payment_type', 'tbl_salary_payment.payment_month', 'tbl_salary_payment.fine_deduction', 'tbl_salary_payment.paid_date');
            $this->datatables->column_search = array('tbl_account_details.fullname', 'tbl_account_details.employment_id', 'tbl_salary_payment.comments', 'tbl_salary_payment.payment_type', 'tbl_salary_payment.payment_month', 'tbl_salary_payment.fine_deduction', 'tbl_salary_payment.paid_date');
            $this->datatables->order = array('salary_payment_id' => 'desc');

            $where = array('tbl_salary_payment.payment_month' => $month);
            $fetch_data = make_datatables($where);

            $data = array();
            foreach ($fetch_data as $_key => $v_payroll) {

                $salary_payment_history = get_result('tbl_salary_payment_details', array('salary_payment_id' => $v_payroll->salary_payment_id));
                $total_salary_amount = 0;
                if (!empty($salary_payment_history)) {
                    foreach ($salary_payment_history as $v_payment_history) {
                        if (is_numeric($v_payment_history->salary_payment_details_value)) {
                            if ($v_payment_history->salary_payment_details_label == 'overtime_salary') {
                                $rate = $v_payment_history->salary_payment_details_value;
                            } elseif ($v_payment_history->salary_payment_details_label == 'hourly_rates') {
                                $rate = $v_payment_history->salary_payment_details_value;
                            }
                            $total_salary_amount += $v_payment_history->salary_payment_details_value;
                        }
                    }
                }
                $salary_allowance_info = get_result('tbl_salary_payment_allowance', array('salary_payment_id' => $v_payroll->salary_payment_id));
                $total_allowance = 0;
                if (!empty($salary_allowance_info)) {
                    foreach ($salary_allowance_info as $v_salary_allowance_info) {
                        $total_allowance += $v_salary_allowance_info->salary_payment_allowance_value;
                    }
                }
                if (empty($rate)) {
                    $rate = 0;
                }
                $salary_deduction_info = get_result('tbl_salary_payment_deduction', array('salary_payment_id' => $v_payroll->salary_payment_id));
                $total_deduction = 0;
                if (!empty($salary_deduction_info)) {
                    foreach ($salary_deduction_info as $v_salary_deduction_info) {
                        $total_deduction += $v_salary_deduction_info->salary_payment_deduction_value;
                    }
                }

                $total_paid_amount = $total_salary_amount + $total_allowance - $rate;

                $action = null;
                $sub_array = array();
                $sub_array[] = date('F-Y', strtotime($v_payroll->payment_month));
                $sub_array[] = display_date($v_payroll->paid_date);
                $sub_array[] = display_money($total_paid_amount, default_currency());
                $sub_array[] = display_money($total_deduction, default_currency());
                $sub_array[] = display_money($net_salary = $total_paid_amount - $total_deduction, default_currency());

                if (!empty($v_payroll->fine_deduction)) {
                    $fine_deduction = $v_payroll->fine_deduction;
                } else {
                    $fine_deduction = 0;
                }
                $sub_array[] = display_money($fine_deduction, default_currency());
                $sub_array[] = display_money($net_salary - $fine_deduction, default_currency());
                $sub_array[] = '<a href="' . base_url() . 'admin/payroll/salary_payment_details/' . $v_payroll->salary_payment_id . '"
                               class="btn btn-info btn-xs" title="' . lang('view') . '" data-toggle="modal"
                               data-target="#myModal_lg"><span class="fa fa-list-alt"></span></a>';
                $data[] = $sub_array;
            }

            render_table($data);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function payment_historyPeriod($date = null)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $date = explode('n', $date);
            $data['start_month'] = $date[0];
            $data['end_month'] = $date[1];
            $this->datatables->table = 'tbl_salary_payment';
            $this->datatables->join_table = array('tbl_account_details', 'tbl_designations', 'tbl_departments');
            $this->datatables->join_where = array('tbl_salary_payment.user_id = tbl_account_details.user_id', 'tbl_designations.designations_id  = tbl_account_details.designations_id', 'tbl_departments.departments_id  = tbl_designations.departments_id');
            $this->datatables->column_order = array('tbl_account_details.fullname', 'tbl_account_details.employment_id', 'tbl_salary_payment.comments', 'tbl_salary_payment.payment_type', 'tbl_salary_payment.payment_month', 'tbl_salary_payment.fine_deduction', 'tbl_salary_payment.paid_date');
            $this->datatables->column_search = array('tbl_account_details.fullname', 'tbl_account_details.employment_id', 'tbl_salary_payment.comments', 'tbl_salary_payment.payment_type', 'tbl_salary_payment.payment_month', 'tbl_salary_payment.fine_deduction', 'tbl_salary_payment.paid_date');
            $this->datatables->order = array('salary_payment_id' => 'desc');

            $where = array('tbl_salary_payment.payment_month >=' => $date[0], 'tbl_salary_payment.payment_month <=' => $date[1]);
            $fetch_data = make_datatables($where);
            $data = array();
            foreach ($fetch_data as $_key => $v_payroll) {

                $salary_payment_history = get_result('tbl_salary_payment_details', array('salary_payment_id' => $v_payroll->salary_payment_id));
                $total_salary_amount = 0;
                if (!empty($salary_payment_history)) {
                    foreach ($salary_payment_history as $v_payment_history) {
                        if (is_numeric($v_payment_history->salary_payment_details_value)) {
                            if ($v_payment_history->salary_payment_details_label == 'overtime_salary') {
                                $rate = $v_payment_history->salary_payment_details_value;
                            } elseif ($v_payment_history->salary_payment_details_label == 'hourly_rates') {
                                $rate = $v_payment_history->salary_payment_details_value;
                            }
                            $total_salary_amount += $v_payment_history->salary_payment_details_value;
                        }
                    }
                }
                $salary_allowance_info = get_result('tbl_salary_payment_allowance', array('salary_payment_id' => $v_payroll->salary_payment_id));
                $total_allowance = 0;
                if (!empty($salary_allowance_info)) {
                    foreach ($salary_allowance_info as $v_salary_allowance_info) {
                        $total_allowance += $v_salary_allowance_info->salary_payment_allowance_value;
                    }
                }
                if (empty($rate)) {
                    $rate = 0;
                }
                $salary_deduction_info = get_result('tbl_salary_payment_deduction', array('salary_payment_id' => $v_payroll->salary_payment_id));
                $total_deduction = 0;
                if (!empty($salary_deduction_info)) {
                    foreach ($salary_deduction_info as $v_salary_deduction_info) {
                        $total_deduction += $v_salary_deduction_info->salary_payment_deduction_value;
                    }
                }

                $total_paid_amount = $total_salary_amount + $total_allowance - $rate;

                $action = null;
                $sub_array = array();
                $sub_array[] = date('F-Y', strtotime($v_payroll->payment_month));
                $sub_array[] = display_date($v_payroll->paid_date);
                $sub_array[] = display_money($total_paid_amount, default_currency());
                $sub_array[] = display_money($total_deduction, default_currency());
                $sub_array[] = display_money($net_salary = $total_paid_amount - $total_deduction, default_currency());

                if (!empty($v_payroll->fine_deduction)) {
                    $fine_deduction = $v_payroll->fine_deduction;
                } else {
                    $fine_deduction = 0;
                }
                $sub_array[] = display_money($fine_deduction, default_currency());
                $sub_array[] = display_money($net_salary - $fine_deduction, default_currency());
                $sub_array[] = '<a href="' . base_url() . 'admin/payroll/salary_payment_details/' . $v_payroll->salary_payment_id . '"
                               class="btn btn-info btn-xs" title="' . lang('view') . '" data-toggle="modal"
                               data-target="#myModal_lg"><span class="fa fa-list-alt"></span></a>';
                $data[] = $sub_array;
            }

            render_table($data);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function payroll_summary_pdf($search_type, $pdf)
    {
        $data['title'] = lang('payroll_summary');
        if (!empty($search_type)) {
            $data['search_type'] = $search_type;
            if ($search_type == 'employee') {
                $data['user_id'] = $pdf;
                $user_info = $this->db->where('user_id', $pdf)->get('tbl_account_details')->row();
                $data['by'] = ' - ' . ' ' . ' ' . $user_info->fullname;
                $data['employee_payroll'] = $this->payroll_model->get_salary_payment_info($data['user_id'], true, 'employee');
            }
            if ($search_type == 'month') {
                $data['by_month'] = $pdf;
                $data['by'] = ' - ' . ' ' . date('F-Y', strtotime($pdf));
                $data['employee_payroll'] = $this->payroll_model->get_salary_payment_info($data['by_month'], true, 'month');
            }
            if ($search_type == 'period') {
                $date = explode('n', $pdf);
                $data['start_month'] = $date[0];
                $data['end_month'] = $date[1];
                $data['employee_payroll'] = $this->payroll_model->get_salary_payment_info($data, true, 'period');
                $data['by'] = ' - ' . ' ' . date('F-Y', strtotime($date[0])) . ' ' . lang('TO') . ' ' . date('F-Y', strtotime($date[1]));
            }
        }
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/payroll/payroll_summary_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it(lang('payroll_summary') . ' ' . $data['by']));
    }

    public function clear_activities()
    {
        $this->payroll_model->_table_name = "tbl_activities"; //table name
        $this->payroll_model->delete_multiple(array('module' => 'payroll'));
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('admin/dashboard');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

}