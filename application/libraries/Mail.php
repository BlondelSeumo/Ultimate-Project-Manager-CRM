<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of sendEmail
 *
 * @author nayem
 */
class Mail {

    public function __construct() {

        $this->CI = &get_instance();
        $this->CI->load->library('email');
    }

    //put your code here

    public function sendEmail($from, $to, $subject, $page) {
        $this->CI->email->set_mailtype("html");
        $this->CI->email->from($from[0], $from[1]);
        $this->CI->email->to($to);
        $this->CI->email->subject($subject);
        $body = $page;
        $this->CI->email->message($body);
        $send = $this->CI->email->send();
        if ($send) {
            return $send;
        } else {
            $error = show_error($this->CI->email->print_debugger());
            return $error;
        }
    }

}
