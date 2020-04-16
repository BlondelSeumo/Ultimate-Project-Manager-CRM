<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of mailbox_model
 *
 * @author NaYeM
 */
class Mailbox_Model extends MY_Model {

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    public function get_inbox_message($email, $flag = Null, $del_info = NULL) {
        $this->db->select('*');
        $this->db->from('tbl_inbox');
        $this->db->where('to', $email);
        if (!empty($del_info)) {
            $this->db->where('deleted', 'Yes');
        } else {
            $this->db->where('deleted', 'No');
        }
        if (!empty($flag)) {
            $this->db->where('view_status', '2');
        }
        $this->db->order_by('message_time', 'DESC');
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    public function get_sent_message($user_id, $del_info = NULL) {
        $this->db->select('*');
        $this->db->from('tbl_sent');
        $this->db->where('user_id', $user_id);
        if (!empty($del_info)) {
            $this->db->where('deleted', 'Yes');
        } else {
            $this->db->where('deleted', 'No');
        }
        $this->db->order_by('message_time', 'DESC');
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    public function get_draft_message($user_id, $del_info = NULL) {
        $this->db->select('*');
        $this->db->from('tbl_draft');
        $this->db->where('user_id', $user_id);
        if (!empty($del_info)) {
            $this->db->where('deleted', 'Yes');
        } else {
            $this->db->where('deleted', 'No');
        }
        $this->db->order_by('message_time', 'DESC');
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

}
