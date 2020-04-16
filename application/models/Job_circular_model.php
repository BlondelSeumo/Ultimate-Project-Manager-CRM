<?php

class Job_Circular_Model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    public function get_job_application_info($id = NULL, $flag = null)
    {
        $this->db->select('tbl_job_appliactions.*', FALSE);
        $this->db->select('tbl_job_circular.*', FALSE);
        $this->db->from('tbl_job_appliactions');
        $this->db->join('tbl_job_circular', 'tbl_job_circular.job_circular_id = tbl_job_appliactions.job_circular_id', 'left');
        if (!empty($id)) {
            $this->db->where('tbl_job_appliactions.job_appliactions_id', $id);
            $query_result = $this->db->get();
            if (!empty($flag)) {
                $result = $query_result->result();
            } else {
                $result = $query_result->row();
            }
        } else {
            $query_result = $this->db->get();
            $result = $query_result->result();
        }
        return $result;
    }

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

}
