<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Supplier extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('supplier_model');
    }

    public function index($id = NULL)
    {
        $data['title'] = lang('all') . ' ' . lang('supplier');
        if (!empty($id) && is_numeric($id)) {
            $data['active'] = 2;
            $edited = can_action('151', 'edited');
            if (!empty($edited)) {
                $data['supplier_info'] = $this->supplier_model->check_by(array('supplier_id' => $id), 'tbl_suppliers');
            }
        } else {
            $data['active'] = 1;
        }
        $data['subview'] = $this->load->view('admin/supplier/supplier_list', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function supplierList()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_suppliers';
            $this->datatables->column_order = array('name', 'mobile', 'phone', 'email', 'address');
            $this->datatables->column_search = array('name', 'mobile', 'phone', 'email', 'address');
            $this->datatables->order = array('supplier_id' => 'desc');

            $fetch_data = make_datatables();

            $data = array();

            $edited = can_action('151', 'edited');
            $deleted = can_action('151', 'deleted');
            foreach ($fetch_data as $_key => $v_items) {
                $action = null;
                $sub_array = array();
                $sub_array[] = $v_items->name;
                $sub_array[] = $v_items->mobile;
                $sub_array[] = $v_items->phone;
                $sub_array[] = $v_items->email;
                $sub_array[] = $v_items->address;

                $custom_form_table = custom_form_table(19, $v_items->supplier_id);
                if (!empty($custom_form_table)) {
                    foreach ($custom_form_table as $c_label => $v_fields) {
                        $sub_array[] = $v_fields;
                    }
                }
                if (!empty($edited)) {
                    $action .= btn_edit('admin/supplier/index/' . $v_items->supplier_id) . ' ';
                }
                if (!empty($deleted)) {
                    $action .= ajax_anchor(base_url("admin/supplier/delete_supplier/$v_items->supplier_id"), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                }
                $sub_array[] = $action;
                $data[] = $sub_array;
            }
            render_table($data);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function new_supplier()
    {
        $data['title'] = lang('new') . ' ' . lang('supplier');
        $data['permission_user'] = $this->supplier_model->all_permission_user('151');
        $data['subview'] = $this->load->view('admin/supplier/new_supplier', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function saved_supplier($id = NULL)
    {
        $created = can_action('151', 'created');
        $edited = can_action('151', 'edited');
        if (!empty($created) && !empty($edited)) {

            if (!empty($id) && $id == 'inline') {
                $id = null;
                $inline = true;
            }
            $this->supplier_model->_table_name = 'tbl_suppliers';
            $this->supplier_model->_primary_key = 'supplier_id';

            $data = $this->supplier_model->array_from_post(array('name', 'mobile', 'phone', 'email', 'address'));
            // update root category
            $where = array('name' => $data['name'], 'email' => $data['email']);
            // duplicate value check in DB
            if (!empty($id)) { // if id exist in db update data
                $supplier_id = array('supplier_id !=' => $id);
            } else { // if id is not exist then set id as null
                $supplier_id = null;
            }
            // check whether this input data already exist or not
            $check_items = $this->supplier_model->check_update('tbl_suppliers', $where, $supplier_id);
            if (!empty($check_items)) { // if input data already exist show error alert
                // massage for user
                $type = 'error';
                $msg = "<strong style='color:#000'>" . $data['name'] . '</strong>  ' . lang('already_exist');
            } else { // save and update query
                $permission = $this->input->post('permission', true);
                if (!empty($permission)) {
                    if ($permission == 'everyone') {
                        $assigned = 'all';
                    } else {
                        $assigned_to = $this->supplier_model->array_from_post(array('assigned_to'));
                        if (!empty($assigned_to['assigned_to'])) {
                            foreach ($assigned_to['assigned_to'] as $assign_user) {
                                $assigned[$assign_user] = $this->input->post('action_' . $assign_user, true);
                            }
                        }
                    }
                    if (!empty($assigned)) {
                        if ($assigned != 'all') {
                            $assigned = json_encode($assigned);
                        }
                    } else {
                        $assigned = 'all';
                    }
                    $data['permission'] = $assigned;
                } else {
                    set_message('error', lang('assigned_to') . ' Field is required');
                    if (empty($_SERVER['HTTP_REFERER'])) {
                        redirect('admin/supplier');
                    } else {
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                }
                $return_id = $this->supplier_model->save($data, $id);

                save_custom_field(19, $id);
                
                if (!empty($id)) {
                    $id = $id;
                    $action = 'update_supplier';
                    $msg = lang('update_supplier');
                } else {
                    $id = $return_id;
                    $action = 'save_supplier';
                    $msg = lang('save_supplier');
                }
                $activity = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'supplier',
                    'module_field_id' => $id,
                    'activity' => $action,
                    'icon' => 'fa-circle-o',
                    'value1' => $data['name']
                );
                $this->supplier_model->_table_name = 'tbl_activities';
                $this->supplier_model->_primary_key = 'activities_id';
                $this->supplier_model->save($activity);
                // messages for user
                $type = "success";
            }
            if (!empty($inline)) {
                if (!empty($return_id)) {
                    $result = array(
                        'id' => $id,
                        'name' => $data['name'],
                        'status' => $type,
                        'message' => $msg,
                    );
                } else {
                    $result = array(
                        'status' => $type,
                        'message' => $msg,
                    );
                }
                echo json_encode($result);
                exit();
            } else {
                $message = $msg;
                set_message($type, $message);
            }
        }
        redirect('admin/supplier');
    }

    public function delete_supplier($id)
    {
        $supplier_info = $this->supplier_model->check_by(array('supplier_id' => $id), 'tbl_suppliers');
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'items',
            'module_field_id' => $id,
            'activity' => 'supplier_deleted',
            'icon' => 'fa-circle-o',
            'value1' => $supplier_info->name
        );
        $this->supplier_model->_table_name = 'tbl_activities';
        $this->supplier_model->_primary_key = 'activities_id';
        $this->supplier_model->save($activity);

        $this->supplier_model->_table_name = 'tbl_suppliers';
        $this->supplier_model->_primary_key = 'supplier_id';
        $this->supplier_model->delete($id);
        $type = 'success';
        $message = lang('supplier_deleted');
        echo json_encode(array("status" => $type, 'message' => $message));
        exit();
    }

}
