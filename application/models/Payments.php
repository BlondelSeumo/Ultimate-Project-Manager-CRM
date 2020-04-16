<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of admistrator
 *
 * @author pc mart ltd
 */
class Payments extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('payments_model');
    }

    public function index() {
        $data['title'] = config_item('company_name');

        $data['subview'] = $this->load->view('admin/payments/all_payment', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }    

}
