<?php

/**
 * Description of Project_Model
 *
 * @author NaYeM
 */
class Utilities_Model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    public function get_overtime_info_by_date($start_date, $end_date, $user_id = null)
    {
        $this->db->select('tbl_overtime.*', FALSE);
        $this->db->select('tbl_account_details.fullname', FALSE);
        $this->db->from('tbl_overtime');
        $this->db->join('tbl_account_details', 'tbl_account_details.user_id = tbl_overtime.user_id', 'left');
        $this->db->where('tbl_overtime.overtime_date >=', $start_date);
        $this->db->where('tbl_overtime.overtime_date <=', $end_date);
        if ($this->session->userdata('user_type') != 1) {
            $this->db->where('tbl_overtime.user_id', $this->session->userdata('user_id'));
        }
        if (!empty($user_id)) {
            $this->db->where('tbl_overtime.user_id', $user_id);
        }
        $query_result = $this->db->get();
        $result = $query_result->result();

        return $result;
    }

    public function get_overtime_info_by_emp_id($overtime_id)
    {

        $this->db->select('tbl_overtime.*', FALSE);
        $this->db->select('tbl_account_details.*', FALSE);
        $this->db->from('tbl_overtime');
        $this->db->join('tbl_account_details', 'tbl_account_details.user_id = tbl_overtime.user_id', 'left');
        $this->db->where('tbl_overtime.overtime_id', $overtime_id);
        $query_result = $this->db->get();
        $result = $query_result->row();

        return $result;
    }
}
