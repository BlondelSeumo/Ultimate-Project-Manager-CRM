<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of payroll_model
 *
 * @author NaYeM
 */
class Payroll_Model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    public function get_department_by_id($departments_id)
    {
        $this->db->select('tbl_departments.deptname', FALSE);
        $this->db->select('tbl_designations.*', FALSE);
        $this->db->from('tbl_departments');
        $this->db->join('tbl_designations', 'tbl_departments.departments_id = tbl_designations.departments_id', 'left');
        $this->db->where('tbl_departments.departments_id', $departments_id);
        $query_result = $this->db->get();
        $result = $query_result->result();

        return $result;
    }

    public function get_emp_info_by_id($designation_id)
    {
        $this->db->select('tbl_account_details.*', FALSE);
        $this->db->select('tbl_designations.designations', FALSE);
        $this->db->from('tbl_account_details');
        $this->db->join('tbl_designations', 'tbl_designations.designations_id  = tbl_account_details.designations_id', 'left');
        $this->db->where('tbl_designations.designations_id', $designation_id);
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    public function get_emp_salary_list($id = NULL, $designation_id = NULL)
    {
        $this->db->select('tbl_employee_payroll.*', FALSE);
        $this->db->select('tbl_account_details.*', FALSE);
        $this->db->select('tbl_salary_template.*', FALSE);
        $this->db->select('tbl_hourly_rate.*', FALSE);
        $this->db->select('tbl_designations.*', FALSE);
        $this->db->select('tbl_departments.deptname', FALSE);
        $this->db->from('tbl_employee_payroll');
        $this->db->join('tbl_account_details', 'tbl_employee_payroll.user_id = tbl_account_details.user_id', 'left');
        $this->db->join('tbl_salary_template', 'tbl_employee_payroll.salary_template_id = tbl_salary_template.salary_template_id', 'left');
        $this->db->join('tbl_hourly_rate', 'tbl_employee_payroll.hourly_rate_id = tbl_hourly_rate.hourly_rate_id', 'left');
        $this->db->join('tbl_designations', 'tbl_designations.designations_id  = tbl_account_details.designations_id', 'left');
        $this->db->join('tbl_departments', 'tbl_departments.departments_id  = tbl_designations.departments_id', 'left');

        if (!empty($designation_id)) {
            $this->db->where('tbl_designations.designations_id', $designation_id);
        }
        if (!empty($id)) {
            $this->db->where('tbl_employee_payroll.user_id', $id);
            $query_result = $this->db->get();
            $result = $query_result->row();
        } else {
            if (!empty($_POST["length"]) && $_POST["length"] != -1) {
                $this->db->limit($_POST['length'], $_POST['start']);
            }
            $query_result = $this->db->get();
            $result = $query_result->result();
        }
        return $result;
    }

    public function get_salary_payment_info($salary_payment_id, $result = NULL, $search_type = null)
    {

        $this->db->select('tbl_salary_payment.*', FALSE);
        $this->db->select('tbl_account_details.*', FALSE);
        $this->db->select('tbl_designations.*', FALSE);
        $this->db->select('tbl_departments.deptname', FALSE);
        $this->db->from('tbl_salary_payment');
        $this->db->join('tbl_account_details', 'tbl_salary_payment.user_id = tbl_account_details.user_id', 'left');
        $this->db->join('tbl_designations', 'tbl_designations.designations_id  = tbl_account_details.designations_id', 'left');
        $this->db->join('tbl_departments', 'tbl_departments.departments_id  = tbl_designations.departments_id', 'left');
        if (!empty($search_type)) {
            if ($search_type == 'employee') {
                $this->db->where("tbl_salary_payment.user_id", $salary_payment_id);
            } elseif ($search_type == 'month') {
                $this->db->where("tbl_salary_payment.payment_month", $salary_payment_id);
            } elseif ($search_type == 'period') {
                $this->db->where("tbl_salary_payment.payment_month >=", $salary_payment_id['start_month']);
                $this->db->where("tbl_salary_payment.payment_month <=", $salary_payment_id['end_month']);
            }
        } else {
            $this->db->where("tbl_salary_payment.salary_payment_id", $salary_payment_id);
        }
        if (!empty($_POST["length"]) && $_POST["length"] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query_result = $this->db->get();
        if (!empty($result)) {
            $result = $query_result->result();
        } else {
            $result = $query_result->row();
        }
        return $result;
    }

    public function get_advance_salary_info_by_date($payment_month = NULL, $id = NULL, $user_id = NULL)
    {
        $this->db->select('tbl_advance_salary.*', FALSE);
        $this->db->select('tbl_account_details.*', FALSE);
        $this->db->from('tbl_advance_salary');
        $this->db->join('tbl_account_details', 'tbl_account_details.user_id = tbl_advance_salary.user_id', 'left');
        if ($this->session->userdata('user_type') != 1) {
            $this->db->where('tbl_advance_salary.user_id', $this->session->userdata('user_id'));
            $this->db->where('tbl_advance_salary.deduct_month', $payment_month);
            $query_result = $this->db->get();
            $result = $query_result->result();
        } elseif (!empty($id)) {
            $this->db->where('tbl_advance_salary.advance_salary_id', $id);
            $query_result = $this->db->get();
            $result = $query_result->row();
        } elseif (!empty($user_id)) {
            $this->db->where('tbl_advance_salary.status', '1');
            $this->db->where('tbl_account_details.user_id', $user_id);
            $query_result = $this->db->get();
            $result = $query_result->result();
        } else {
            $this->db->where('tbl_advance_salary.deduct_month', $payment_month);
            $query_result = $this->db->get();
            $result = $query_result->result();
        }
        return $result;
    }

    public function view_advance_salary($id = NULL)
    {
        $this->db->select('tbl_advance_salary.*', FALSE);
        $this->db->select('tbl_account_details.*', FALSE);
        $this->db->from('tbl_advance_salary');
        $this->db->join('tbl_account_details', 'tbl_account_details.user_id = tbl_advance_salary.user_id', 'left');
        $this->db->where('tbl_advance_salary.advance_salary_id', $id);
        $query_result = $this->db->get();
        $result = $query_result->row();

        return $result;
    }

    public function my_advance_salary_info($all = null)
    {
        $this->db->select('tbl_advance_salary.*', FALSE);
        $this->db->select('tbl_account_details.*', FALSE);
        $this->db->from('tbl_advance_salary');
        $this->db->join('tbl_account_details', 'tbl_account_details.user_id = tbl_advance_salary.user_id', 'left');
        if (!empty($all)) {
            $this->db->order_by('tbl_advance_salary.request_date', "DESC");
        } else {
            $this->db->where('tbl_advance_salary.user_id', $this->session->userdata('user_id'));
        }
        if (!empty($_POST["length"]) && $_POST["length"] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;

    }

    public function get_attendance_info_by_date($start_date, $end_date, $user_id)
    {
        $this->db->select('tbl_attendance.*', FALSE);
        $this->db->select('tbl_clock.*', FALSE);
        $this->db->from('tbl_attendance');
        $this->db->join('tbl_clock', 'tbl_clock.attendance_id  = tbl_attendance.attendance_id', 'left');
        $this->db->where('tbl_attendance.date_in >=', $start_date);
        $this->db->where('tbl_attendance.date_in <=', $end_date);
        $this->db->where('tbl_attendance.user_id', $user_id);
        $this->db->where('tbl_attendance.attendance_status', 1);
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    public function get_provident_fund_info_by_date($start_date, $end_date, $user_id = null)
    {
        $this->db->select('tbl_salary_payment.*', FALSE);
        $this->db->select('tbl_salary_payment_deduction.*', FALSE);
        $this->db->select('tbl_account_details.*', FALSE);
        $this->db->from('tbl_salary_payment');
        $this->db->join('tbl_salary_payment_deduction', 'tbl_salary_payment_deduction.salary_payment_id  = tbl_salary_payment.salary_payment_id', 'left');
        $this->db->join('tbl_account_details', 'tbl_account_details.user_id  = tbl_salary_payment.user_id', 'left');
        $this->db->where('tbl_salary_payment.payment_month >=', $start_date);
        $this->db->where('tbl_salary_payment.payment_month <=', $end_date);
        $this->db->where('tbl_salary_payment_deduction.salary_payment_deduction_label', lang('provident_fund'));
        if (!empty($user_id)) {
            $this->db->where('tbl_salary_payment.user_id', $user_id);
        }
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

}
