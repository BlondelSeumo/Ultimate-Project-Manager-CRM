<?php

/**
 * Description of award_model
 *
 * @author Ashraf
 */
class Award_Model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    public function get_employee_award_by_id($id = NULL, $user = null)
    {

        $this->db->select('tbl_employee_award.*', FALSE);
        $this->db->select('tbl_account_details.*', FALSE);
        $this->db->from('tbl_employee_award');
        $this->db->join('tbl_account_details', 'tbl_account_details.user_id = tbl_employee_award.user_id', 'left');
        if (!empty($id) && empty($user)) {
            $this->db->where('tbl_employee_award.employee_award_id', $id);
            $query_result = $this->db->get();
            $result = $query_result->row();
        } elseif (!empty($id) && !empty($user)) {
            $this->db->where('tbl_employee_award.user_id', $id);
            $this->db->order_by('tbl_employee_award.user_id', 'DESC');
            if (!empty($_POST["length"]) && $_POST["length"] != -1) {
                $this->db->limit($_POST['length'], $_POST['start']);
            }
            $query_result = $this->db->get();
            $result = $query_result->result();
        } else {
            $this->db->order_by('tbl_employee_award.employee_award_id', 'DESC');
            if (!empty($_POST["length"]) && $_POST["length"] != -1) {
                $this->db->limit($_POST['length'], $_POST['start']);
            }
            $query_result = $this->db->get();
            $result = $query_result->result();
        }
        return $result;
    }

}
