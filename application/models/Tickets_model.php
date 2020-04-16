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
class Tickets_Model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    public function get_tickets($filterBy)
    {
        $tickets = array();
        $all_tickets = array_reverse($this->get_permission('tbl_tickets'));
        if (empty($filterBy)) {
            return $all_tickets;
        } else {
            foreach ($all_tickets as $v_tickets) {
                if ($v_tickets->status == $filterBy) {
                    array_push($tickets, $v_tickets);
                }
            }
        }
        return $tickets;
    }
    public function get_client_tickets($filterBy)
    {
        $tickets = array();
        $all_tickets = array_reverse(get_result('tbl_tickets', array('reporter' => $this->session->userdata('user_id'))));
        if (empty($filterBy)) {
            return $all_tickets;
        } else {
            foreach ($all_tickets as $v_tickets) {
                if ($v_tickets->status == $filterBy) {
                    array_push($tickets, $v_tickets);
                }
            }
        }
        return $tickets;
    }
}
