<?php

class Training_Model extends MY_Model {

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    public function get_all_training_info($id = NULL) {
        $this->db->select('tbl_training.*', FALSE);
        $this->db->select('tbl_account_details.*', FALSE);
        $this->db->from('tbl_training');
        $this->db->join('tbl_account_details', 'tbl_training.user_id = tbl_account_details.user_id', 'left');
        if (!empty($id)) {
            $this->db->where('tbl_training.training_id', $id);
            $query_result = $this->db->get();
            $result = $query_result->row();
        } else {
            $query_result = $this->db->get();
            $result = $query_result->result();
        }
        
        return $result;
    }

}
