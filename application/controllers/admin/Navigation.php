<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Navigation extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('navigation_model');
        $this->language_files = $this->navigation_model->all_files();
    }

    public function index()
    {
        $data['page'] = 'user';
        $data['menu'] = array("navigation_manager" => 1, "manage_navigation" => 1);
        $data['title'] = "Manage Navigation";

        $this->navigation_model->_table_name = "tbl_menu"; //table name
        $this->navigation_model->_order_by = "menu_id";
        $data['nav'] = $this->navigation_model->get();

        $data['subview'] = $this->load->view('admin/menu/manage_navigation', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    /**
     * Add New Navigation
     */
    public function add_navigation($id = NULL)
    {
        $data['title'] = "Add New Navigation";
        $data['page'] = 'user';
        $this->navigation_model->_table_name = "tbl_menu"; //table name
        $this->navigation_model->_order_by = "menu_id";
        if ($id) {
            $data['menu_info'] = $this->navigation_model->get_by(array('menu_id' => $id,), TRUE);
            if (empty($data['menu_info'])) {
                // messages for user
                $type = "error";
                $message = "Sorry no record found!";
                set_message($type, $message);
                redirect('admin/navigation'); //redirect page
            }
        } else {
            $data['menu_info'] = $this->navigation_model->get_new_menuInfo();
        }

        $data['nav'] = $this->navigation_model->get();
        $data['subview'] = $this->load->view('admin/menu/add_navigation', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    /**
     * Save Navigation
     */
    public function save_navigation($id = NULL)
    {
        $data = $this->navigation_model->array_from_post(array('label', 'link', 'icon', 'sort'));
        $data['parent'] = $this->input->post('parent',true);
        if (empty($data['parent'])) {
            $data['parent'] = 0;
        }
        $this->navigation_model->_table_name = "tbl_menu"; //table name
        $this->navigation_model->_primary_key = "menu_id"; // $id
        $this->navigation_model->_order_by = "menu_id";
        $this->navigation_model->save($data, $id);

        // messages for user
        $type = "success";
        $message = "Save Navigation Successfully!";
        set_message($type, $message);
        redirect('admin/navigation/add_navigation'); //redirect page
    }

    /**
     * Delete Navigation
     */
    public function delete_navigation($id = NULL)
    {
        $this->navigation_model->_table_name = "tbl_menu"; // table name
        $this->navigation_model->_primary_key = "menu_id"; // $id
        $this->navigation_model->_order_by = "menu_id";
        if ($id) {
            $result = $this->navigation_model->get_by(array('menu_id' => $id,), TRUE);

            if (empty($result)) {
                // messages for user
                $type = "error";
                $message = "Sorry no record for Delete!";
                set_message($type, $message);
                redirect('admin/navigation'); //redirect page
            } else {
                $parent = $this->navigation_model->get_by(array('parent' => $id,), TRUE);

                if (empty($parent)) {
                    $this->navigation_model->delete($id);
                    // messages for user
                    $type = "success";
                    $message = "Delete Successfully!";
                    set_message($type, $message);
                    redirect('admin/navigation'); //redirect page   
                } else {
                    // messages for user
                    $type = "error";
                    $message = "You can not delete, this recored already used!";
                    set_message($type, $message);
                    redirect('admin/navigation'); //redirect page
                }
            }
        }
    }

}
