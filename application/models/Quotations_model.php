<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of client_model
 *
 * @author NaYeM
 */
class Quotations_Model extends MY_Model {

    public $_table_name;
    public $_order_by;
    public $_primary_key;
    

    public function get_client_contacts($client_id) {

        $this->db->select('tbl_account_details.*', FALSE);
        $this->db->select('tbl_users.*', FALSE);
        $this->db->from('tbl_account_details');

        $this->db->join('tbl_users', 'tbl_users.user_id = tbl_account_details.user_id', 'left');
        $this->db->where('tbl_account_details.company', $client_id);
        $query_result = $this->db->get();

        $result = $query_result->result();
        return $result;
    }

}
