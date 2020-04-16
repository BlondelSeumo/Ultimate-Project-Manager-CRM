<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of attendance_model
 *
 * @author NaYeM
 */
class Attendance_Model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    public function get_employee_id_by_dept_id($departments_id)
    {
        $this->db->select('tbl_account_details.*', FALSE);
        $this->db->select('tbl_designations.*', FALSE);
        $this->db->select('tbl_departments.*', FALSE);
        $this->db->from('tbl_account_details');
        $this->db->join('tbl_designations', 'tbl_designations.designations_id = tbl_account_details.designations_id', 'left');
        $this->db->join('tbl_departments', 'tbl_departments.departments_id = tbl_designations.departments_id', 'left');
        $this->db->where('tbl_departments.departments_id', $departments_id);
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    public function get_designation_by_dept_id($departments_id)
    {
        $this->db->select('tbl_designations.*', FALSE);
        $this->db->select('tbl_departments.*', FALSE);
        $this->db->from('tbl_designations');
        $this->db->join('tbl_departments', 'tbl_departments.departments_id = tbl_designations.departments_id', 'left');
        $this->db->where('tbl_departments.departments_id', $departments_id);
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    public function attendance_report_by_empid($user_id = null, $sdate = null, $flag = NULL, $leave = NULL)
    {

        $this->db->select('tbl_attendance.*', FALSE);
        $this->db->select('tbl_clock.*', FALSE);
        $this->db->select('tbl_account_details.user_id', FALSE);
        $this->db->from('tbl_attendance');
        $this->db->join('tbl_clock', 'tbl_clock.attendance_id  = tbl_attendance.attendance_id', 'left');
        $this->db->join('tbl_account_details', 'tbl_attendance.user_id  = tbl_account_details.user_id', 'left');
        $this->db->where('tbl_attendance.user_id', $user_id);
        $this->db->where('tbl_attendance.date_in', $sdate);
        //$this->db->where('tbl_attendance.date_out <=', $sdate);

        $query_result = $this->db->get();
        $result = $query_result->result();

        if (empty($result)) {
            //$val['attendance_status'] = $leave;
            $val['attendance_status'] = $flag;
            $val['date'] = $sdate;
            $result[] = (object)$val;
        }
        return $result;
    }

    public function attendance_report_by_manual($user_id = null, $sdate = null, $flag = NULL, $leave = NULL)
    {

        $this->db->select('tbl_attendance.*', FALSE);
//        $this->db->select('tbl_clock.*', FALSE);
        $this->db->from('tbl_attendance');
//        $this->db->join('tbl_clock', 'tbl_clock.attendance_id  = tbl_attendance.attendance_id', 'left');
        $this->db->where('tbl_attendance.user_id', $user_id);
        $this->db->where('tbl_attendance.date_in', $sdate);
        //$this->db->where('tbl_attendance.date_out <=', $sdate);

        $query_result = $this->db->get();
        $result = $query_result->result();

        if (empty($result)) {
            //$val['attendance_status'] = $leave;
            $val['attendance_status'] = $flag;
            $val['date'] = $sdate;
            $result[] = (object)$val;
        }
        return $result;
    }

    public function attendance_report_2_by_empid($user_id = null, $sdate = null, $flag = NULL, $leave = NULL)
    {

        $this->db->select('tbl_attendance.*', FALSE);
//        $this->db->select('tbl_clock.*', FALSE);
        $this->db->select('tbl_account_details.user_id', FALSE);
        $this->db->from('tbl_attendance');
//        $this->db->join('tbl_clock', 'tbl_clock.attendance_id  = tbl_attendance.attendance_id', 'left');
        $this->db->join('tbl_account_details', 'tbl_attendance.user_id  = tbl_account_details.user_id', 'left');
        $this->db->where('tbl_attendance.user_id', $user_id);
        $this->db->where('tbl_attendance.date_in', $sdate);
        //$this->db->where('tbl_attendance.date_out <=', $sdate);

        $query_result = $this->db->get();
        $result = $query_result->result();

        if (empty($result)) {
            //$val['attendance_status'] = $leave;
            $val['attendance_status'] = $flag;
            $val['date'] = $sdate;
            $result[] = (object)$val;
        } else {
            if ($result[0]->attendance_status == 0) {
                if ($flag == 'H') {
                    $result[0]->attendance_status = 'H';
                }
            }
        }
        return $result;
    }

    public function get_all_clock_history($clock_history_id = null)
    {

        $this->db->select('tbl_clock.*', FALSE);
        $this->db->select('tbl_clock_history.*', FALSE);
        $this->db->select('tbl_account_details.*', FALSE);
        $this->db->from('tbl_clock_history');
        $this->db->join('tbl_account_details', 'tbl_clock_history.user_id  = tbl_account_details.user_id', 'left');
        $this->db->join('tbl_clock', 'tbl_clock_history.clock_id  = tbl_clock.clock_id', 'left');
        if (!empty($clock_history_id)) {
            $this->db->where('tbl_clock_history.clock_history_id', $clock_history_id);
            $query_result = $this->db->get();
            $result = $query_result->row();
        } else {
            if (!empty($_POST["length"]) && $_POST["length"] != -1) {
                $this->db->limit($_POST['length'], $_POST['start']);
            }
            $this->db->order_by('tbl_clock_history.clock_history_id', "DESC");
            $query_result = $this->db->get();
            $result = $query_result->result();
        }
        return $result;
    }

    public function get_mytime_info($attendance_id = NULL, $clock_id = NULL)
    {

        $this->db->select('tbl_attendance.*', FALSE);
        $this->db->select('tbl_clock.*', FALSE);
        $this->db->from('tbl_attendance');
        $this->db->join('tbl_clock', 'tbl_clock.attendance_id  = tbl_attendance.attendance_id', 'left');
        if (!empty($attendance_id)) {
            $this->db->where('tbl_attendance.attendance_id', $attendance_id);
            $query_result = $this->db->get();
            $result = $query_result->result();
        }
        if (!empty($clock_id)) {
            $this->db->where('tbl_clock.clock_id', $clock_id);
            $query_result = $this->db->get();
            $result = $query_result->row();
        }

        return $result;
    }

}
