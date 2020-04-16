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
class Message_Model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    //put your code here

    public function get_chat_messages($id, $limit)
    {
        return $this->db
            ->where("tbl_private_chat_messages.private_chat_id", $id)
            ->join("tbl_users", "tbl_users.user_id = tbl_private_chat_messages.user_id")
            ->limit($limit)
            ->order_by("tbl_private_chat_messages.private_chat_messages_id", "DESC")
            ->get("tbl_private_chat_messages");
    }

}
