<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of department
 *
 * @author Uniquecoder
 */
class Departments extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('department_model');
    }

    public function index($id = NULL, $des_id = null)
    {
        $data['title'] = lang('departments');
        //department table initials
        $data['active'] = 1;
        if ($id) { // retrive data from db by id
            $data['active'] = 2;
            // get all department by id
            $data['department_info'] = $this->db->where('departments_id', $id)->get('tbl_departments')->row();
            $data['designations_info'] = $this->db->where('designations_id', $des_id)->get('tbl_designations')->row();

            if (empty($data['designations_info'])) {
                $type = "error";
                $message = lang('no_record_found');
                set_message($type, $message);
                redirect('admin/department/department_list');
            }
            $role = $this->department_model->select_user_roll_by_id($des_id);
            if ($role) {
                foreach ($role as $value) {
                    $result[$value->menu_id] = $value->menu_id;
                }
                $data['roll'] = $result;
            }
        }
        $menu_info = $this->db->where('status', '1')->order_by('sort')->get('tbl_menu')->result();
        foreach ($menu_info as $items) {
            $menu['parents'][$items->parent][] = $items;
        }

        $data['result'] = $this->buildChild(0, $menu);

        $this->department_model->_table_name = "tbl_departments"; //table name
        $this->department_model->_order_by = "departments_id";
        $data['all_dept_info'] = $this->department_model->get();
        // get all department info and designation info
        foreach ($data['all_dept_info'] as $v_dept_info) {
            $data['all_department_info'][] = $this->department_model->get_add_department_by_id($v_dept_info->departments_id);
        }
        $data['subview'] = $this->load->view('admin/department/department_list', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function save_departments($id = NULL)
    {
        $created = can_action('70', 'created');
        $edited = can_action('70', 'edited');
        if (!empty($created) || !empty($edited) && !empty($id)) {
            $this->department_model->_table_name = "tbl_departments"; // table name
            $this->department_model->_primary_key = "departments_id"; // $id

            $where = $this->department_model->array_from_post(array('departments_id')); //input post

            $data['deptname'] = $this->input->post('deptname', true);
            // check department by department_name
            // if not empty return this id else save
            $check_department = $this->department_model->check_by($where, 'tbl_departments');
            $check_input_department = $this->department_model->check_by($data, 'tbl_departments');
            if (!empty($check_department)) {
                $departments_id = $check_department->departments_id;
                $actvt = $check_department->deptname;
            } elseif (!empty($check_input_department)) {
                $departments_id = $check_input_department->departments_id;
                $actvt = $check_input_department->deptname;
            } else {
                $data['deptname'] = $this->input->post('deptname', true);
                $actvt = $data['deptname'];
                if (!empty($data['deptname'])) {
                    $departments_id = $this->department_model->save($data);
                } else {
                    $departments_id = $this->department_model->save($data, $id);
                }
            }

            // input data
            $designations = $this->input->post('designations', TRUE);

            // update input data designations_id
            $designations_id = $this->input->post('designations_id', TRUE);

            $this->department_model->_table_name = "tbl_designations"; // table name
            $this->department_model->_primary_key = "designations_id"; // $id
            $desi_data['designations'] = $designations;
            $desi_data['departments_id'] = $departments_id;
            if (!empty($designations_id)) {
                $desi_id = $designations_id;
                $this->department_model->save($desi_data, $designations_id);
            } else {
                $desi_id = $this->department_model->save($desi_data);
            }
            // delete existing userroll by login id
            if (!empty($designations_id)) {
                $this->department_model->_table_name = 'tbl_user_role'; //table name
                $this->department_model->_order_by = 'designations_id';
                $this->department_model->_primary_key = 'user_role_id';
                $roll = $this->department_model->get_by(array('designations_id' => $designations_id), false);

                foreach ($roll as $v_roll) {
                    $this->department_model->_table_name = 'tbl_user_role'; //table name
                    $this->department_model->delete_multiple(array('user_role_id' => $v_roll->user_role_id));
                }
            }
            $menu = $this->department_model->array_from_post(array('menu'));
            $this->department_model->_table_name = 'tbl_user_role'; // table name
            $this->department_model->_primary_key = 'user_role_id'; // $id
            if (!empty($menu['menu'])) {
                $dashboard = in_array('1', $menu['menu']);
                $calendar = in_array('2', $menu['menu']);
            }
            if (empty($dashboard)) {
                $mdata['menu_id'] = 1;
                $mdata['designations_id'] = $desi_id;
                $this->department_model->save($mdata);
            }
            if (empty($calendar)) {
                $mdata['menu_id'] = 2;
                $mdata['designations_id'] = $desi_id;
                $this->department_model->save($mdata);
            }

            if (!empty($menu['menu'])) {
                foreach ($menu['menu'] as $key => $v_menu) {
                    if ($v_menu != 'undefined') {
                        if ($v_menu != 1 || $v_menu != 2) {
                            $view = $this->input->post('view_' . $v_menu, true);
                            $created = $this->input->post('created_' . $v_menu, true);
                            $edited = $this->input->post('edited_' . $v_menu, true);
                            $deleted = $this->input->post('deleted_' . $v_menu, true);
                            $mdata['view'] = (!empty($view) ? $view : 0);
                            $mdata['created'] = (!empty($created) ? $created : 0);
                            $mdata['edited'] = (!empty($edited) ? $edited : 0);
                            $mdata['deleted'] = (!empty($deleted) ? $deleted : 0);
                            $mdata['menu_id'] = $v_menu;
                            $mdata['designations_id'] = $desi_id;
                            $this->department_model->save($mdata);
                        }

                    }
                }
            }
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'departments',
                'module_field_id' => $id,
                'activity' => ('activity_added_a_department'),
                'value1' => $actvt
            );
            $this->department_model->_table_name = 'tbl_activities';
            $this->department_model->_primary_key = 'activities_id';
            $this->department_model->save($activity);

            // messages for user
            $type = "success";
            $message = lang('department_added');
            set_message($type, $message);
        }

        $option = $this->input->post('save', true);
        if ($option == 1) {
            redirect('admin/departments');
        } elseif ($option == 2) {
            redirect('admin/departments/details');
        }
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('admin/departments');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function save_inline_departments($id = NULL)
    {
        $created = can_action('70', 'created');
        $edited = can_action('70', 'edited');
        if (!empty($created) || !empty($edited) && !empty($id)) {
            $this->department_model->_table_name = "tbl_departments"; // table name
            $this->department_model->_primary_key = "departments_id"; // $id

            $where = $this->department_model->array_from_post(array('departments_id')); //input post

            $data['deptname'] = $this->input->post('deptname', true);
            // check department by department_name
            // if not empty return this id else save
            $check_department = $this->department_model->check_by($where, 'tbl_departments');
            $check_input_department = $this->department_model->check_by($data, 'tbl_departments');
            if (!empty($check_department)) {
                $departments_id = $check_department->departments_id;
                $actvt = $check_department->deptname;
            } elseif (!empty($check_input_department)) {
                $departments_id = $check_input_department->departments_id;
                $actvt = $check_input_department->deptname;
            } else {
                $data['deptname'] = $this->input->post('deptname', true);
                $actvt = $data['deptname'];
                if (!empty($data['deptname'])) {
                    $departments_id = $this->department_model->save($data);
                } else {
                    $departments_id = $this->department_model->save($data, $id);
                }
            }

            // input data
            $designations = $this->input->post('designations', TRUE);

            // update input data designations_id
            $designations_id = $this->input->post('designations_id', TRUE);

            $this->department_model->_table_name = "tbl_designations"; // table name
            $this->department_model->_primary_key = "designations_id"; // $id
            $desi_data['designations'] = $designations;
            $desi_data['departments_id'] = $departments_id;
            if (!empty($designations_id)) {
                $desi_id = $designations_id;
                $this->department_model->save($desi_data, $designations_id);
            } else {
                $desi_id = $this->department_model->save($desi_data);
            }
            // delete existing userroll by login id
            if (!empty($designations_id)) {
                $this->department_model->_table_name = 'tbl_user_role'; //table name
                $this->department_model->_order_by = 'designations_id';
                $this->department_model->_primary_key = 'user_role_id';
                $roll = $this->department_model->get_by(array('designations_id' => $designations_id), false);

                foreach ($roll as $v_roll) {
                    $this->department_model->_table_name = 'tbl_user_role'; //table name
                    $this->department_model->delete_multiple(array('user_role_id' => $v_roll->user_role_id));
                }
            }
            $menu = $this->department_model->array_from_post(array('menu'));
            $this->department_model->_table_name = 'tbl_user_role'; // table name
            $this->department_model->_primary_key = 'user_role_id'; // $id
            if (!empty($menu['menu'])) {
                $dashboard = in_array('1', $menu['menu']);
                $calendar = in_array('2', $menu['menu']);
            }
            if (empty($dashboard)) {
                $mdata['menu_id'] = 1;
                $mdata['designations_id'] = $desi_id;
                $this->department_model->save($mdata);
            }
            if (empty($calendar)) {
                $mdata['menu_id'] = 2;
                $mdata['designations_id'] = $desi_id;
                $this->department_model->save($mdata);
            }

            if (!empty($menu['menu'])) {
                foreach ($menu['menu'] as $key => $v_menu) {
                    if ($v_menu != 'undefined') {
                        if ($v_menu != 1 || $v_menu != 2) {
                            $view = $this->input->post('view_' . $v_menu, true);
                            $created = $this->input->post('created_' . $v_menu, true);
                            $edited = $this->input->post('edited_' . $v_menu, true);
                            $deleted = $this->input->post('deleted_' . $v_menu, true);
                            $mdata['view'] = (!empty($view) ? $view : 0);
                            $mdata['created'] = (!empty($created) ? $created : 0);
                            $mdata['edited'] = (!empty($edited) ? $edited : 0);
                            $mdata['deleted'] = (!empty($deleted) ? $deleted : 0);
                            $mdata['menu_id'] = $v_menu;
                            $mdata['designations_id'] = $desi_id;
                            $this->department_model->save($mdata);
                        }

                    }
                }
            }
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'departments',
                'module_field_id' => $id,
                'activity' => ('activity_added_a_department'),
                'value1' => $actvt
            );
            $this->department_model->_table_name = 'tbl_activities';
            $this->department_model->_primary_key = 'activities_id';
            $this->department_model->save($activity);
            if (!empty($departments_id)) {
                $result = array(
                    'id' => $departments_id,
                    'deptname' => $data['deptname'],
                    'status' => 'success',
                    'message' => lang('department_added'),
                );
            } else {
                $result = array(
                    'status' => 'error',
                    'message' => lang('there_in_no_value'),
                );
            }
            echo json_encode($result);
            exit();
        }

    }

    public function details($id = null)
    {
        $edited = can_action('70', 'edited');
        if (!empty($edited)) {
            $data['title'] = lang('departments');
            if (!empty($id)) {
                $data['designations_info'] = $this->db->where('designations_id', $id)->get('tbl_designations')->row();
                if (!empty($data['designations_info'])) {
                    $data['departments_info'] = $this->db->where('departments_id', $data['designations_info']->departments_id)->get('tbl_departments')->row();
                }

                $role = $this->department_model->select_user_roll_by_id($id);
                if ($role) {
                    foreach ($role as $value) {
                        $result[$value->menu_id] = $value;
                    }
                    $data['roll'] = $result;
                }
            }
            $data['subview'] = $this->load->view('admin/department/details', $data, TRUE);
        } else {
            $data['subview'] = $this->load->view('admin/settings/not_found');
        }
        $this->load->view('admin/_layout_main', $data);
    }

    public function new_departments()
    {
        $data['title'] = lang('new_departments');
        $data['subview'] = $this->load->view('admin/department/new_departments', $data, FALSE);
        $this->load->view('admin/_layout_modal_extra_lg', $data);
    }

    public function edit_departments($id, $inline = null)
    {
        $edited = can_action('70', 'edited');
        if (!empty($edited) && !empty($id)) {
            $post = $this->department_model->array_from_post(array('deptname')); //input post
            if (!empty($post['deptname'])) {
                $this->department_model->_table_name = "tbl_departments"; // table name
                $this->department_model->_primary_key = "departments_id"; // $id
                if (!empty($inline)) {
                    $id = $this->department_model->save($post);
                } else {
                    $this->department_model->save($post, $id);
                }
                $activity = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'departments',
                    'module_field_id' => $id,
                    'activity' => ('activity_update_a_department'),
                    'value1' => $post['deptname']
                );
                $this->department_model->_table_name = 'tbl_activities';
                $this->department_model->_primary_key = 'activities_id';
                $this->department_model->save($activity);

                $type = "success";
                $message = lang('department_update');
                if (!empty($inline)) {
                    $result = array(
                        'id' => $id,
                        'deptname' => $post['deptname'],
                        'status' => $type,
                        'message' => $message,
                    );
                    echo json_encode($result);
                    exit();
                } else {
                    set_message($type, $message);
                    redirect('admin/departments'); //redirect page
                }
            }
            $data['title'] = lang('edit') . ' ' . lang('departments');
            if (!empty($inline)) {
                $data['inline'] = true;
                $data['department_info'] = $this->db->where('departments_id', $id)->get('tbl_departments')->row();
            }
            $data['modal_subview'] = $this->load->view('admin/department/edit_departments', $data, FALSE);
            $this->load->view('admin/_layout_modal', $data);
        }
    }

    public function delete_department($id)
    {
        $deleted = can_action('70', 'deleted');
        if (!empty($deleted) && !empty($id)) {
            $where = array('departments_id' => $id);
            //get designation id by dept id
            // check into designation table
            // if data exist do not delete the department
            // else delete the department
            $get_designations_id = $this->db->select('designations_id')->where($where)->get('tbl_designations')->result();
            if (!empty($get_designations_id)) {
                foreach ($get_designations_id as $v_designation) {
                    $or_where = array('designations_id' => $v_designation->designations_id);
                    $get_existing_id = $this->department_model->check_by($or_where, 'tbl_account_details');
                    if (!empty($get_existing_id)) {
                        $type = "error";
                        $message = lang('department_already_used');
                        set_message($type, $message);
                        redirect('admin/departments'); //redirect page
                    } else {
                        // delete all department by id
                        $this->department_model->_table_name = "tbl_departments"; // table name
                        $this->department_model->_primary_key = "departments_id"; // $id
                        $this->department_model->delete($id);

                        // delete all designation by  department id
                        $this->department_model->_table_name = "tbl_designations"; // table name
                        $this->department_model->delete_multiple($where);

                        // delete all user role
                        $this->department_model->_table_name = 'tbl_user_role'; //table name
                        $this->department_model->delete_multiple($or_where);

                        $activity = array(
                            'user' => $this->session->userdata('user_id'),
                            'module' => 'departments',
                            'module_field_id' => $id,
                            'activity' => ('activity_delete_a_department'),
                            'value1' => $this->department_model->get_any_field('tbl_departments', $where, 'deptname'),
                        );
                        $this->department_model->_table_name = 'tbl_activities';
                        $this->department_model->_primary_key = 'activities_id';
                        $this->department_model->save($activity);

                        $type = "success";
                        $message = lang('department_info_deleted');
                    }

                }
            } else {
                // delete all department by id
                $this->department_model->_table_name = "tbl_departments"; // table name
                $this->department_model->_primary_key = "departments_id"; // $id
                $this->department_model->delete($id);

                $type = "success";
                $message = lang('department_info_deleted');
            }
            echo json_encode(array("status" => $type, 'message' => $message));
            exit();
        } else {
            echo json_encode(array("status" => 'error', 'message' => lang('there_in_no_value')));
            exit();
        }

    }

    public function delete_designations($id)
    {
        $deleted = can_action('70', 'deleted');
        if (!empty($deleted)) {
            // check into designation table by id
            // if data exist do not delete the department
            // else delete the department
            $or_where = array('designations_id' => $id);
            $get_existing_id = $this->department_model->check_by($or_where, 'tbl_account_details');
            if (!empty($get_existing_id)) {
                $type = "error";
                $message = lang('designation_already_used');
            } else {
                // delete all designations by id
                $activity = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'departments',
                    'module_field_id' => $id,
                    'activity' => ('activity_delete_a_designation'),
                    'value1' => $this->department_model->get_any_field('tbl_designations', $or_where, 'designations'),
                );
                $this->department_model->_table_name = 'tbl_activities';
                $this->department_model->_primary_key = 'activities_id';
                $this->department_model->save($activity);

                $this->department_model->_table_name = "tbl_designations"; // table name
                $this->department_model->_primary_key = "designations_id"; // $id
                $this->department_model->delete($id);

                $type = "success";
                $message = lang('designation_info_deleted');
            }
            echo json_encode(array("status" => $type, 'message' => $message));
            exit();
        } else {
            echo json_encode(array("status" => 'error', 'message' => lang('there_in_no_value')));
            exit();
        }
    }

    public function buildChild($parent, $menu)
    {
        if (isset($menu['parents'][$parent])) {
            foreach ($menu['parents'][$parent] as $ItemID) {
                if (!isset($menu['parents'][$ItemID->menu_id])) {
                    $result[$ItemID->label] = $ItemID->menu_id;
                }
                if (isset($menu['parents'][$ItemID->menu_id])) {
                    $result[$ItemID->label][$ItemID->menu_id] = self::buildChild($ItemID->menu_id, $menu);
                }
            }
        }
        return $result;
    }

    public function user_by_designation($id)
    {
        $data['title'] = lang('edit') . ' ' . lang('departments');
        $data['users_info'] = $this->db->where('designations_id', $id)->get('tbl_account_details')->result();
        $data['designation_info'] = $this->db->where('designations_id', $id)->get('tbl_designations')->row();
        $data['modal_subview'] = $this->load->view('admin/department/user_by_designation', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function new_designation()
    {
        $data['title'] = lang('new_designation');
        $data['subview'] = $this->load->view('admin/department/new_designation', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function saved_departments($id = NULL)
    {
        $created = can_action('70', 'created');
        $edited = can_action('70', 'edited');
        if (!empty($created) || !empty($edited) && !empty($id)) {
            $this->department_model->_table_name = "tbl_departments"; // table name
            $this->department_model->_primary_key = "departments_id"; // $id

            $where = $this->department_model->array_from_post(array('departments_id')); //input post

            $data['deptname'] = $this->input->post('deptname', true);
            // check department by department_name
            // if not empty return this id else save
            $check_department = $this->department_model->check_by($where, 'tbl_departments');
            $check_input_department = $this->department_model->check_by($data, 'tbl_departments');
            if (!empty($check_department)) {
                $departments_id = $check_department->departments_id;
                $actvt = $check_department->deptname;
            } elseif (!empty($check_input_department)) {
                $departments_id = $check_input_department->departments_id;
                $actvt = $check_input_department->deptname;
            } else {
                $data['deptname'] = $this->input->post('deptname', true);
                $actvt = $data['deptname'];
                if (!empty($data['deptname'])) {
                    $departments_id = $this->department_model->save($data);
                } else {
                    $departments_id = $this->department_model->save($data, $id);
                }
            }

            // input data
            $designations = $this->input->post('designations', TRUE);

            // update input data designations_id
            $designations_id = $this->input->post('designations_id', TRUE);

            $this->department_model->_table_name = "tbl_designations"; // table name
            $this->department_model->_primary_key = "designations_id"; // $id
            $desi_data['designations'] = $designations;
            $desi_data['departments_id'] = $departments_id;
            if (!empty($designations_id)) {
                $desi_id = $designations_id;
                $this->department_model->save($desi_data, $designations_id);
            } else {
                $desi_id = $this->department_model->save($desi_data);
            }
            // delete existing userroll by login id
            if (!empty($designations_id)) {
                $this->department_model->_table_name = 'tbl_user_role'; //table name
                $this->department_model->_order_by = 'designations_id';
                $this->department_model->_primary_key = 'user_role_id';
                $roll = $this->department_model->get_by(array('designations_id' => $designations_id), false);

                foreach ($roll as $v_roll) {
                    $this->department_model->_table_name = 'tbl_user_role'; //table name
                    $this->department_model->delete_multiple(array('user_role_id' => $v_roll->user_role_id));
                }
            }
            $menu = $this->department_model->array_from_post(array('menu'));
            $this->department_model->_table_name = 'tbl_user_role'; // table name
            $this->department_model->_primary_key = 'user_role_id'; // $id
            if (!empty($menu['menu'])) {
                $dashboard = in_array('1', $menu['menu']);
                $calendar = in_array('2', $menu['menu']);
            }
            if (empty($dashboard)) {
                $mdata['menu_id'] = 1;
                $mdata['designations_id'] = $desi_id;
                $this->department_model->save($mdata);
            }
            if (empty($calendar)) {
                $mdata['menu_id'] = 2;
                $mdata['designations_id'] = $desi_id;
                $this->department_model->save($mdata);
            }

            if (!empty($menu['menu'])) {
                foreach ($menu['menu'] as $key => $v_menu) {
                    if ($v_menu != 'undefined') {
                        if ($v_menu != 1 || $v_menu != 2) {
                            $view = $this->input->post('view_' . $v_menu, true);
                            $created = $this->input->post('created_' . $v_menu, true);
                            $edited = $this->input->post('edited_' . $v_menu, true);
                            $deleted = $this->input->post('deleted_' . $v_menu, true);
                            $mdata['view'] = (!empty($view) ? $view : 0);
                            $mdata['created'] = (!empty($created) ? $created : 0);
                            $mdata['edited'] = (!empty($edited) ? $edited : 0);
                            $mdata['deleted'] = (!empty($deleted) ? $deleted : 0);
                            $mdata['menu_id'] = $v_menu;
                            $mdata['designations_id'] = $desi_id;
                            $this->department_model->save($mdata);
                        }

                    }
                }
            }
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'departments',
                'module_field_id' => $id,
                'activity' => ('activity_added_a_department'),
                'value1' => $actvt
            );
            $this->department_model->_table_name = 'tbl_activities';
            $this->department_model->_primary_key = 'activities_id';
            $this->department_model->save($activity);

            // messages for user
            $type = "success";
            $message = lang('department_added');
            if (!empty($desi_id)) {
                $result = array(
                    'id' => $desi_id,
                    'designations' => $designations,
                    'status' => $type,
                    'message' => $message,
                );
            } else {
                $result = array(
                    'status' => $type,
                    'message' => $message,
                );
            }
            echo json_encode($result);
            exit();
        }
    }


}
