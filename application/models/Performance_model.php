<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Performance_Model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    public function get_add_department_by_id($department_id)
    {
        $this->db->select('tbl_departments.deptname', FALSE);
        $this->db->select('tbl_designations.*', FALSE);
        $this->db->from('tbl_departments');
        $this->db->join('tbl_designations', 'tbl_departments.departments_id = tbl_designations.departments_id', 'left');
        $this->db->where('tbl_departments.departments_id', $department_id);
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }
    public function get_all_indicator_info($performance_indicator_id = NULL)
    {
        $this->db->select('tbl_performance_indicator.*', FALSE);
        $this->db->select('tbl_designations.*', FALSE);
        $this->db->select('tbl_departments.*', FALSE);
        $this->db->from('tbl_performance_indicator');
        $this->db->join('tbl_designations', 'tbl_performance_indicator.designations_id = tbl_designations.designations_id', 'left');
        $this->db->join('tbl_departments', 'tbl_designations.departments_id = tbl_departments.departments_id', 'left');
        if (!empty($performance_indicator_id)) {
            $this->db->where('tbl_performance_indicator.performance_indicator_id', $performance_indicator_id);
            $query_result = $this->db->get();
            $result = $query_result->row();
        } else {
            $query_result = $this->db->get();
            $result = $query_result->result();
        }

        return $result;
    }

    public function get_appraisal_value_by_id($id = NULL)
    {
        $this->db->select('tbl_performance_apprisal.*', FALSE);
        $this->db->select('tbl_account_details.*', FALSE);
        $this->db->select('tbl_designations.designations', FALSE);
        $this->db->select('tbl_departments.deptname', FALSE);
        $this->db->from('tbl_performance_apprisal');
        $this->db->join('tbl_account_details', 'tbl_account_details.user_id = tbl_performance_apprisal.user_id', 'left');
        $this->db->join('tbl_designations', 'tbl_account_details.designations_id = tbl_designations.designations_id', 'left');
        $this->db->join('tbl_departments', 'tbl_designations.departments_id = tbl_departments.departments_id', 'left');
        if (!empty($id)) {
            $this->db->where('tbl_performance_apprisal.performance_appraisal_id', $id);
            $query_result = $this->db->get();
            $result = $query_result->row();
        } else {
            $query_result = $this->db->get();
            $result = $query_result->result();
        }

        return $result;
    }

    public function get_performance_info_by_month($month)
    {
        $this->db->select('tbl_performance_apprisal.*', FALSE);
        $this->db->select('tbl_account_details.*', FALSE);
        $this->db->select('tbl_designations.designations', FALSE);
        $this->db->select('tbl_departments.deptname', FALSE);
        $this->db->from('tbl_performance_apprisal');
        $this->db->join('tbl_account_details', 'tbl_account_details.user_id = tbl_performance_apprisal.user_id', 'left');
        $this->db->join('tbl_designations', 'tbl_account_details.designations_id = tbl_designations.designations_id', 'left');
        $this->db->join('tbl_departments', 'tbl_designations.departments_id = tbl_departments.departments_id', 'left');
        $this->db->where('tbl_performance_apprisal.appraisal_month', $month);
        $query_result = $this->db->get();
        $result = $query_result->result();

        return $result;
    }

}
