<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of message_model
 *
 * @author Sadiqul
 */
class Chat_Model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    //put your code here

    public function get_chat_messages($id, $deleted)
    {
        $this->db->select('tbl_private_chat_messages.*', FALSE);
        $this->db->select('tbl_users.user_id', FALSE);
        $this->db->from('tbl_private_chat_messages');
        $this->db->join('tbl_users', 'tbl_users.user_id = tbl_private_chat_messages.user_id', 'left');
        $this->db->where("tbl_private_chat_messages.private_chat_id", $id);
        $this->db->where("tbl_private_chat_messages.private_chat_messages_id >", $deleted);
//        $this->db->limit($limit);
        $this->db->order_by("tbl_private_chat_messages.private_chat_messages_id", "DESC");
        $query_result = $this->db->get();
        $result = $query_result->result();

        return $result;
    }

    public function get_open_chats($to_user_id = null)
    {
        $this->db->select('tbl_private_chat_users.*', FALSE);
        $this->db->select('tbl_private_chat.chat_title,tbl_private_chat.time', FALSE);
        $this->db->from('tbl_private_chat_users');
        $this->db->join('tbl_private_chat', 'tbl_private_chat.private_chat_id = tbl_private_chat_users.private_chat_id', 'left');
        $this->db->where("tbl_private_chat_users.user_id", $this->session->userdata('user_id'));
        $this->db->where("tbl_private_chat_users.active != ", 2);
        if (!empty($to_user_id)) {
            $this->db->where("tbl_private_chat_users.to_user_id", $to_user_id);
        }
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    function check_total_chat($id)
    {
        $this->db->select('*');
        $this->db->group_by('user_id');
        $this->db->from('tbl_private_chat_messages');
        $this->db->where('private_chat_id', $id);
//        $this->db->where('deleted', 0);
        $query = $this->db->get();
        return $query->result();
    }


}
