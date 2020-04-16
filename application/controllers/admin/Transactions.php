<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Transactions extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('transactions_model');
        $this->load->model('invoice_model');

        $this->load->helper('ckeditor');
        $this->data['ckeditor'] = array(
            'id' => 'ck_editor',
            'path' => 'asset/js/ckeditor',
            'config' => array(
                'toolbar' => "Full",
                'width' => "99.8%",
                'height' => "400px"
            )
        );
    }

    public function deposit($id = NULL)
    {
        $data['title'] = lang('all_deposit');
        // get permission user by menu id
        $data['permission_user'] = $this->transactions_model->all_permission_user('30');
        $data['all_deposit_info'] = $this->transactions_model->get_permission('tbl_transactions');
        if (!empty($id)) {
            $data['active'] = 2;
            if (is_numeric($id)) {
                $deposit_info = $this->transactions_model->check_by(array('transactions_id' => $id), 'tbl_transactions');
                if (!empty($deposit_info))
                    $can_edit = $this->transactions_model->can_action('tbl_transactions', 'edit', array('transactions_id' => $id));
                if (!empty($can_edit)) {
                    $data['deposit_info'] = $deposit_info;
                }
            } else {
                $data['active'] = 1;
            }
        } else {
            $data['active'] = 1;
        }
        $data['dropzone'] = true;
        $all_deposit_info = $this->transactions_model->get_permission('tbl_transactions');
        $data['all_deposit_info'] = array();
        $id = $this->uri->segment(5);
        if (!empty($id)) {
            $data['search_by'] = $this->uri->segment(4);
            if ($data['search_by'] == 'category') {
                if (!empty($all_deposit_info)) {
                    foreach ($all_deposit_info as $v_deposit) {
                        if ($v_deposit->category_id == $id) {
                            array_push($data['all_deposit_info'], $v_deposit);
                        }

                    }
                }
            }
        } else {
            $data['all_deposit_info'] = $this->transactions_model->get_permission('tbl_transactions');
        }
        $data['subview'] = $this->load->view('admin/transactions/deposit', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function depositList($filterBy = null, $type = null)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_transactions';
            $this->datatables->join_table = array('tbl_accounts', 'tbl_client');
            $this->datatables->join_where = array('tbl_accounts.account_id=tbl_transactions.account_id', 'tbl_transactions.paid_by=tbl_client.client_id');
            $this->datatables->column_order = array('tbl_transactions.name', 'date', 'tbl_accounts.account_name', 'notes', 'tbl_client.name', 'amount', 'reference');
            $this->datatables->column_search = array('tbl_transactions.name', 'date', 'tbl_accounts.account_name', 'notes', 'tbl_client.name', 'amount', 'reference');
            $this->datatables->order = array('transactions_id' => 'desc');

            $where = null;
            if (empty($type)) {
                $where = array('type' => 'Income');
            }
            if ($type == 'by_account') {
                $where = array('type' => 'Income', 'tbl_transactions.account_id' => $filterBy);
            } else if ($type == 'by_category') {
                $where = array('type' => 'Income', 'category_id' => $filterBy);
            }
            // get all invoice
            $fetch_data = $this->datatables->get_deposit($filterBy, $type);

            $data = array();
            $edited = can_action('30', 'edited');
            $deleted = can_action('30', 'deleted');
            foreach ($fetch_data as $_key => $v_deposit) {
                if (!empty($v_deposit)) {
                    $action = null;
                    $can_edit = $this->transactions_model->can_action('tbl_transactions', 'edit', array('transactions_id' => $v_deposit->transactions_id));
                    $can_delete = $this->transactions_model->can_action('tbl_transactions', 'delete', array('transactions_id' => $v_deposit->transactions_id));

                    $account_info = $this->transactions_model->check_by(array('account_id' => $v_deposit->account_id), 'tbl_accounts');
                    $client_info = $this->transactions_model->check_by(array('client_id' => $v_deposit->paid_by), 'tbl_client');
                    $category_info = $this->transactions_model->check_by(array('income_category_id' => $v_deposit->category_id), 'tbl_income_category');
                    if (!empty($client_info)) {
                        $client_name = $client_info->name;
                    } else {
                        $client_name = '-';
                    }

                    $sub_array = array();
                    if (!empty($deleted) || !empty($can_delete)) {
                        $sub_array[] = '<div class="checkbox c-checkbox" ><label class="needsclick"> <input value="' . $v_deposit->transactions_id . '" type="checkbox"><span class="fa fa-check"></span></label></div>';
                    }
                    $name = null;
                    $name .= '<a data-toggle="modal" data-target="#myModal" class="text-info" href="' . base_url() . 'admin/transactions/view_expense/' . $v_deposit->transactions_id . '">' . (!empty($v_deposit->name) ? $v_deposit->name : '-') . '</a>';
                    $sub_array[] = $name;

                    $date = null;
                    $date .= '<a data-toggle="modal" data-target="#myModal" class="text-info" href="' . base_url() . 'admin/transactions/view_expense/' . $v_deposit->transactions_id . '">' . strftime(config_item('date_format'), strtotime($v_deposit->date)) . '</a>';
                    $sub_array[] = $date;

                    $sub_array[] = (!empty($account_info->account_name) ? $account_info->account_name : '-');

                    $sub_array[] = $client_name;

                    $sub_array[] = display_money($v_deposit->amount, default_currency());
                    $sub_array[] = display_money($v_deposit->total_balance, default_currency());

                    $custom_form_table = custom_form_table(1, $v_deposit->transactions_id);
                    if (!empty($custom_form_table)) {
                        foreach ($custom_form_table as $c_label => $v_fields) {
                            $sub_array[] = $v_fields;
                        }
                    }
                    $attachment = null;
                    $attachment_info = json_decode($v_deposit->attachement);
                    if (!empty($attachment_info)) {
                        $attachment = '<a href="' . base_url() . 'admin/transactions/download/' . $v_deposit->transactions_id . '">' . lang('download') . '</a>';
                    }
                    $sub_array[] = $attachment;

                    $action .= '<a class="btn btn-info btn-xs" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/transactions/view_expense/' . $v_deposit->transactions_id . '"><span class="fa fa-list-alt"></span></a>' . ' ';
                    if (!empty($can_edit) && !empty($edited)) {
                        $action .= btn_edit('admin/transactions/deposit/' . $v_deposit->transactions_id) . ' ';
                    }
                    if (!empty($can_delete) && !empty($deleted)) {
                        $action .= ajax_anchor(base_url("admin/transactions/delete_deposit/$v_deposit->transactions_id"), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                    }
                    $sub_array[] = $action;
                    $data[] = $sub_array;
                }
            }

            render_table($data, $where);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function import($type)
    {
        if ($type == 'Income') {
            $header = lang('deposit');
        } else {
            $header = lang('expense');
        }
        $data['title'] = lang('import') . ' ' . $header;
        $data['permission_user'] = $this->transactions_model->all_permission_user('30');
        $data['type'] = $type;
        $data['subview'] = $this->load->view('admin/transactions/import', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function save_imported()
    {
        //load the excel library
        $this->load->library('excel');
        ob_start();
        $file = $_FILES["upload_file"]["tmp_name"];
        if (!empty($file)) {
            $valid = false;
            $types = array('Excel2007', 'Excel5', 'CSV');
            foreach ($types as $type) {
                $reader = PHPExcel_IOFactory::createReader($type);
                if ($reader->canRead($file)) {
                    $valid = true;
                }
            }
            if (!empty($valid)) {
                try {
                    $objPHPExcel = PHPExcel_IOFactory::load($file);
                } catch (Exception $e) {
                    die("Error loading file :" . $e->getMessage());
                }
                //All data from excel
                $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);


                for ($x = 2; $x <= count($sheetData); $x++) {

                    // **********************
                    // Save Into leads table
                    // **********************

                    $data = $this->transactions_model->array_from_post(array('name', 'account_id', 'type', 'category_id', 'paid_by', 'payment_methods_id'));

                    $date = date('Y-m-d', strtotime($sheetData[$x]["A"]));

                    $data['date'] = trim($date);
                    $data['amount'] = trim($sheetData[$x]["B"]);

                    $account_info = $this->transactions_model->check_by(array('account_id' => $data['account_id']), 'tbl_accounts');
                    if ($data['type'] == 'Income') {
                        $ac_data['balance'] = $account_info->balance + $data['amount'];
                    } else {
                        $ac_data['balance'] = $account_info->balance - $data['amount'];
                    }
                    $this->transactions_model->_table_name = "tbl_accounts"; //table name
                    $this->transactions_model->_primary_key = "account_id";
                    $this->transactions_model->save($ac_data, $account_info->account_id);

                    $data['notes'] = trim($sheetData[$x]["C"]);
                    $data['reference'] = trim($sheetData[$x]["D"]);

                    if (!empty($_FILES['attachement']['name']['0'])) {
                        $old_path_info = $this->input->post('upload_path', true);
                        if (!empty($old_path_info)) {
                            foreach ($old_path_info as $old_path) {
                                unlink($old_path);
                            }
                        }
                        $mul_val = $this->transactions_model->multi_uploadAllType('attachement');
                        $data['attachement'] = json_encode($mul_val);
                    }

                    $permission = $this->input->post('permission', true);
                    if (!empty($permission)) {
                        if ($permission == 'everyone') {
                            $assigned = 'all';
                        } else {
                            $assigned_to = $this->transactions_model->array_from_post(array('assigned_to'));
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
                    }
                    $this->transactions_model->_table_name = "tbl_transactions"; //table name
                    $this->transactions_model->_primary_key = "transactions_id";
                    $this->transactions_model->save($data);
                }
                $type = 'success';
                if ($data['type'] == 'Income') {
                    $message = lang('save_new_deposit');
                    $redirect = 'deposit';
                } else {
                    $message = lang('save_new_expense');
                    $redirect = 'expense';
                }
            } else {
                $type = 'error';
                $message = "Sorry your uploaded file type not allowed ! please upload XLS/CSV File ";
            }
        } else {
            $type = 'error';
            $message = "You did not Select File! please upload XLS/CSV File ";
        }
        set_message($type, $message);
        if (empty($_SERVER['HTTP_REFERER'])) {
            if (empty($redirect)) {
                $redirect = 'dashboard';
            } else {
                $redirect = 'transactions/' . $redirect;
            }
            redirect('admin/' . $redirect);
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }

    }

    public function save_deposit($id = NULL)
    {

        $created = can_action('30', 'created');
        $edited = can_action('30', 'edited');
        if (!empty($created) || !empty($edited) && !empty($id)) {
            $data = $this->transactions_model->array_from_post(array('name', 'account_id', 'date', 'notes', 'category_id', 'paid_by', 'payment_methods_id', 'reference'));

            $data['type'] = 'Income';
            $data['amount'] = $this->input->post('amount', TRUE);
            $account_info = $this->transactions_model->check_by(array('account_id' => $data['account_id']), 'tbl_accounts');
            if (!empty($account_info)) {
                $account_info = $account_info;
            } else {
                $account_info = $this->db->get('tbl_accounts')->row();
            }
            if (!empty($data['amount'])) {
                if (!empty($id)) {
                    $data['account_id'] = $this->input->post('old_account_id', TRUE);
                } else {
                    $data['amount'] = $this->input->post('amount', TRUE);
                    $data['credit'] = $this->input->post('amount', TRUE);
                    $ac_data['balance'] = $account_info->balance + $data['amount'];

                    $this->transactions_model->_table_name = "tbl_accounts"; //table name
                    $this->transactions_model->_primary_key = "account_id";
                    $this->transactions_model->save($ac_data, $account_info->account_id);
                }
                $account_info = $this->transactions_model->check_by(array('account_id' => $data['account_id']), 'tbl_accounts');
                if (!empty($account_info)) {
                    $account_info = $account_info;
                } else {
                    $account_info = $this->db->get('tbl_accounts')->row();
                }
                $data['total_balance'] = $account_info->balance;

                $upload_file = array();

                $files = $this->input->post("files", true);
                $target_path = getcwd() . "/uploads/";
                //process the fiiles which has been uploaded by dropzone
                if (!empty($files) && is_array($files)) {
                    foreach ($files as $key => $file) {
                        if (!empty($file)) {
                            $file_name = $this->input->post('file_name_' . $file, true);
                            $new_file_name = move_temp_file($file_name, $target_path);
                            $file_ext = explode(".", $new_file_name);
                            $is_image = check_image_extension($new_file_name);
                            $size = $this->input->post('file_size_' . $file, true) / 1000;
                            if ($new_file_name) {
                                $up_data = array(
                                    "fileName" => $new_file_name,
                                    "path" => "uploads/" . $new_file_name,
                                    "fullPath" => getcwd() . "/uploads/" . $new_file_name,
                                    "ext" => '.' . end($file_ext),
                                    "size" => round($size, 2),
                                    "is_image" => $is_image,
                                );
                                array_push($upload_file, $up_data);
                            }
                        }
                    }
                }

                $fileName = $this->input->post('fileName', true);
                $path = $this->input->post('path', true);
                $fullPath = $this->input->post('fullPath', true);
                $size = $this->input->post('size', true);
                $is_image = $this->input->post('is_image', true);

                if (!empty($fileName)) {
                    foreach ($fileName as $key => $name) {
                        $old['fileName'] = $name;
                        $old['path'] = $path[$key];
                        $old['fullPath'] = $fullPath[$key];
                        $old['size'] = $size[$key];
                        $old['is_image'] = $is_image[$key];

                        array_push($upload_file, $old);
                    }
                }
                if (!empty($upload_file)) {
                    $data['attachement'] = json_encode($upload_file);
                } else {
                    $data['attachement'] = null;
                }

                $permission = $this->input->post('permission', true);
                if (!empty($permission)) {
                    if ($permission == 'everyone') {
                        $assigned = 'all';
                    } else {
                        $assigned_to = $this->transactions_model->array_from_post(array('assigned_to'));
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
                }


                $this->transactions_model->_table_name = "tbl_transactions"; //table name
                $this->transactions_model->_primary_key = "transactions_id";


                if (!empty($id)) {
                    $this->transactions_model->save($data, $id);
                    $activity = ('activity_update_deposit');
                    $msg = lang('update_a_deposit');
                    $description = 'not_deposit_update';
                    $not_value = lang('title') . ' ' . $data['name'] . ' ' . lang('date') . ' ' . strftime(config_item('date_format'), strtotime($data['date']));
                } else {
                    $id = $this->transactions_model->save($data);
                    $activity = ('activity_new_deposit');
                    $description = 'not_deposit_saved';
                    $not_value = lang('account') . ' ' . $account_info->account_name . ' ' . lang('amount') . ' ' . display_money($data['amount']);
                    $msg = lang('save_new_deposit');
                    // send sms
                    $this->send_transactions_sms('deposit', $id);
                }
                save_custom_field(1, $id);

                // save into activities
                $activities = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'transactions',
                    'module_field_id' => $id,
                    'activity' => $activity,
                    'icon' => 'fa-building-o',
                    'link' => 'admin/transactions/view_details/' . $id,
                    'value1' => $account_info->account_name,
                );
                // Update into tbl_project
                $this->transactions_model->_table_name = "tbl_activities"; //table name
                $this->transactions_model->_primary_key = "activities_id";
                $this->transactions_model->save($activities);

                $designation_id = $this->session->userdata('designations_id');
                if (!empty($designation_id)) {
                    $designation_info = $this->transactions_model->check_by(array('designations_id' => $this->session->userdata('designations_id')), 'tbl_designations');
                }
                if (!empty($designation_info)) {
                    $dept_head = $this->transactions_model->check_by(array('departments_id' => $designation_info->departments_id), 'tbl_departments');
                }

                $check_head = $this->db->where('department_head_id', $this->session->userdata('user_id'))->get('tbl_departments')->row();
                $role = $this->session->userdata('user_type');
                if ($role == 3 && empty($check_head)) {
                    $this->deposit_email($data, $id);
                }

                // get departments head by departments id
                $all_admin = $this->db->where('role_id', 1)->get('tbl_users')->result();
                if (!empty($dept_head)) {
                    $head = $this->db->where('user_id', $dept_head->department_head_id)->get('tbl_users')->row();
                    array_push($all_admin, $head);
                }
                $notifyUser = array();
                if (!empty($all_admin)) {
                    foreach ($all_admin as $v_user) {
                        if (!empty($v_user)) {
                            if ($v_user->user_id != $this->session->userdata('user_id')) {
                                array_push($notifyUser, $v_user->user_id);
                                add_notification(array(
                                    'to_user_id' => $v_user->user_id,
                                    'icon' => 'building-o',
                                    'description' => $description,
                                    'link' => 'admin/transactions/view_details/' . $id,
                                    'value' => $not_value,
                                ));
                            }
                        }
                    }
                }
                if (!empty($notifyUser)) {
                    show_notification($notifyUser);
                }

                $type = 'success';
            } else {
                $type = 'error';
                $msg = 'please enter the amount';
            }
            set_message($type, $msg);
        }
        redirect('admin/transactions/deposit/');
    }

    public function send_transactions_sms($type, $transaction_id)
    {
        $mobile = can_received_sms('transaction_record_sms_number');
        if (!empty($mobile)) {
            $merge_fields = [];
            $merge_fields = array_merge($merge_fields, merge_transactions_template($type, $transaction_id));
            $this->sms->send(SMS_TRANSACTION_RECORD, $mobile, $merge_fields);
        }
        return true;
    }

    public function bulk_delete_deposit()
    {
        $selected_id = $this->input->post('ids', true);
        if (!empty($selected_id)) {
            foreach ($selected_id as $id) {
                $result[] = $this->delete_deposit($id, true);
            }
            echo json_encode($result);
            exit();
        } else {
            $type = "error";
            $message = lang('you_need_select_to_delete');
            echo json_encode(array("status" => $type, 'message' => $message));
            exit();
        }
    }

    public function delete_deposit($id, $bulk = null)
    {
        $deleted = can_action('30', 'deleted');
        $can_delete = $this->transactions_model->can_action('tbl_transactions', 'delete', array('transactions_id' => $id));
        $deposit_info = $this->transactions_model->check_by(array('transactions_id' => $id), 'tbl_transactions');
        if (!empty($deposit_info)) {
            if (!empty($deleted) && !empty($can_delete)) {
                $account_info = $this->transactions_model->check_by(array('account_id' => $deposit_info->account_id), 'tbl_accounts');

                $ac_data['balance'] = $account_info->balance - $deposit_info->amount;
                $this->transactions_model->_table_name = "tbl_accounts"; //table name
                $this->transactions_model->_primary_key = "account_id";
                $this->transactions_model->save($ac_data, $account_info->account_id);

                $activity = ('activity_delete_deposit');
                $msg = lang('delete_deposit');
                // save into activities
                $activities = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'transactions',
                    'module_field_id' => $id,
                    'activity' => $activity,
                    'icon' => 'fa-building-o',
                    'link' => 'admin/transactions/view_details/' . $id,
                    'value1' => $account_info->account_name,
                    'value2' => $deposit_info->amount,
                );
                // Update into tbl_project
                $this->transactions_model->_table_name = "tbl_activities"; //table name
                $this->transactions_model->_primary_key = "activities_id";
                $this->transactions_model->save($activities);

                $comments_info = $this->transactions_model->check_by(array('transactions_id' => $id), 'tbl_transactions');
                if (!empty($comments_info->attachment)) {
                    $attachment = json_decode($comments_info->attachment);
                    foreach ($attachment as $v_file) {
                        remove_files($v_file->fileName);
                    }
                }

                $this->transactions_model->_table_name = "tbl_transactions"; //table name
                $this->transactions_model->_primary_key = "transactions_id";
                $this->transactions_model->delete($id);

                $type = 'success';
            } else {
                $type = 'error';
                $msg = lang('there_in_no_value');
            }
        } else {
            $type = 'error';
            $msg = lang('there_in_no_value');
        }
        if (!empty($bulk)) {
            return (array("status" => $type, 'message' => $msg));
        }
        echo json_encode(array("status" => $type, 'message' => $msg));
        exit();
    }

    public function expense($id = NULL)
    {
        $data['title'] = lang('all_expense');
        // get permission user by menu id
        $data['permission_user'] = $this->transactions_model->all_permission_user('31');


        if (!empty($id)) {
            $data['active'] = 2;
            if (is_numeric($id)) {
                $expense_info = $this->transactions_model->check_by(array('transactions_id' => $id), 'tbl_transactions');
                $can_edit = $this->transactions_model->can_action('tbl_transactions', 'edit', array('transactions_id' => $id));
                if (!empty($expense_info) && !empty($can_edit)) {
                    $data['expense_info'] = $expense_info;
                }
            } else {
                $data['active'] = 1;
                if ($id == 'project_expense') {
                    $data['active'] = 2;
                    $project_id = $this->uri->segment(5);
                    $project_info = get_row('tbl_project', array('project_id' => $project_id));
                    if ($project_info->permission == 'all') {
                        $data['permission_user'] = $this->transactions_model->allowed_user('57');
                    } else {
                        $data['permission_user'] = $this->transactions_model->permitted_allowed_user($project_info->permission);
                    }
                }
            }
        } else {
            $data['active'] = 1;
        }
        $data['dropzone'] = true;
        $all_expense_info = $this->transactions_model->get_permission('tbl_transactions');
        $data['all_expense_info'] = array();
        $id = $this->uri->segment(5);
        if (!empty($id)) {
            $data['search_by'] = $this->uri->segment(4);
            if ($data['search_by'] == 'category') {
                if (!empty($all_expense_info)) {
                    foreach ($all_expense_info as $v_expense) {
                        if ($v_expense->category_id == $id) {
                            array_push($data['all_expense_info'], $v_expense);
                        }

                    }
                }
            }
        } else {
            $data['all_expense_info'] = $this->transactions_model->get_permission('tbl_transactions');
        }
        $data['subview'] = $this->load->view('admin/transactions/expense', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function expenseList($filterBy = null, $type = null)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_transactions';
            $this->datatables->join_table = array('tbl_accounts');
            $this->datatables->join_where = array('tbl_accounts.account_id=tbl_transactions.account_id');
            $this->datatables->column_order = array('tbl_transactions.name', 'date', 'amount', 'notes', 'tbl_accounts.account_name', 'status', 'reference');
            $this->datatables->column_search = array('tbl_transactions.name', 'date', 'amount', 'notes', 'tbl_accounts.account_name', 'status', 'reference');
            $this->datatables->order = array('transactions_id' => 'desc');
            $where = null;
            if (empty($type)) {
                $where = array('type' => 'Expense');
            }
            if ($type == 'by_account') {
                $where = array('type' => 'Expense', 'tbl_transactions.account_id' => $filterBy);
            } else if ($type == 'by_category') {
                $where = array('type' => 'Expense', 'category_id' => $filterBy);
            }
            // get all invoice
            $fetch_data = $this->datatables->get_expense($filterBy, $type);

            $data = array();

            $edited = can_action('30', 'edited');
            $deleted = can_action('30', 'deleted');
            foreach ($fetch_data as $_key => $v_expense) {
                $action = null;
                $can_edit = $this->transactions_model->can_action('tbl_transactions', 'edit', array('transactions_id' => $v_expense->transactions_id));
                $can_delete = $this->transactions_model->can_action('tbl_transactions', 'delete', array('transactions_id' => $v_expense->transactions_id));
                $account_info = $this->transactions_model->check_by(array('account_id' => $v_expense->account_id), 'tbl_accounts');
                $recurring = null;
                if ($v_expense->recurring == 'Yes') {
                    $recurring = '<span data-toggle="tooltip" data-placement="top"
                                                              title="' . lang("recurring") . '"
                                                              class="label label-primary"><i
                                                                class="fa fa-retweet"></i></span>';
                }
                $sub_array = array();
                if (!empty($deleted) || !empty($can_delete)) {
                    $sub_array[] = '<div class="checkbox c-checkbox" ><label class="needsclick"> <input value="' . $v_expense->transactions_id . '" type="checkbox"><span class="fa fa-check"></span></label></div>';
                }
                $name = null;
                $name .= '<a class="text-info" href="' . base_url() . 'admin/transactions/view_details/' . $v_expense->transactions_id . '">' . (!empty($v_expense->name) ? $v_expense->name : '-') . '</a>';
                $sub_array[] = $name;

                $date = null;
                $date .= '<a class="text-info" href="' . base_url() . 'admin/transactions/view_details/' . $v_expense->transactions_id . '">' . strftime(config_item('date_format'), strtotime($v_expense->date)) . '</a>';
                $sub_array[] = $date;

                $sub_array[] = (!empty($account_info->account_name) ? $account_info->account_name : '-');
                $sub_array[] = display_money($v_expense->amount, default_currency());

                $status = $v_expense->status;
                $text = '';
                if ($v_expense->project_id != 0) {
                    if ($v_expense->billable == 'No') {
                        $status = 'not_billable';
                        $label = 'primary';
                        $title = lang('not_billable');
                        $e_action = '';
                    } else {
                        $status = 'billable';
                        $label = 'success';
                        $title = lang('billable');
                        $e_action = '';
                        if ($v_expense->invoices_id != 0 && !empty(get_row('tbl_invoices', array('invoices_id' => $v_expense->invoices_id)))) {
                            $payment_status = $this->invoice_model->get_payment_status($v_expense->invoices_id);
                            $text = '<a href="' . base_url() . 'admin/invoice/manage_invoice/invoice_details/' . $v_expense->invoices_id . '"> <p class="text-sm m0 p0"><span class="text-dark">' . lang('invoiced') . '</span><span class="text-danger">' . ' ' . $payment_status . '</span></p></a>';
                        } else {
                            $text = '<p class="text-sm m0 p0"><span class="text-danger">' . lang('not_invoiced') . '</span></p>';
                        }
                    }
                } else {
                    if ($v_expense->status == 'non_approved') {
                        $label = 'danger';
                        $title = lang('get_approved');
                        $e_action = 'approved';
                    } elseif ($v_expense->status == 'unpaid') {
                        $label = 'warning';
                        $title = lang('get_paid');
                        $e_action = 'paid';
                    } else {
                        $label = 'success';
                        $title = '';
                        $e_action = '';
                    }
                }
                $check_head = $this->db->where('department_head_id', $this->session->userdata('user_id'))->get('tbl_departments')->row();
                $role = $this->session->userdata('user_type');
                $e_status = null;
                if ($role == 1 || !empty($check_head)) {
                    $e_status .= '<a data-toggle="tooltip" data-placement="top" title="' . $title . '"
                                                           class="label label-' . $label . '"
                                                           href="' . ((!empty($action)) ? base_url() . 'admin/transactions/set_status/' . $action . '/' . $v_expense->transactions_id : "#") . '">
                                                            ' . lang($status) . '</a>' . ' ' . $recurring;
                } else {
                    $e_status .= '     <span class="label label-' . $label . '">' . lang($status) . '</span>' . ' ' . $recurring;
                }
                $sub_array[] = $e_status . $text;

                $custom_form_table = custom_form_table(2, $v_expense->transactions_id);
                if (!empty($custom_form_table)) {
                    foreach ($custom_form_table as $c_label => $v_fields) {
                        $sub_array[] = $v_fields;
                    }
                }
                $attachment = null;
                $attachment_info = json_decode($v_expense->attachement);
                if (!empty($attachment_info)) {
                    $attachment = '<a href="' . base_url() . 'admin/transactions/download/' . $v_expense->transactions_id . '">' . lang('download') . '</a>';
                }
                $sub_array[] = $attachment;

                $action .= '<a class="btn btn-info btn-xs"  href="' . base_url() . 'admin/transactions/view_details/' . $v_expense->transactions_id . '"><span class="fa fa-list-alt"></span></a>' . ' ';
                if (!empty($can_edit) && !empty($edited)) {
                    $action .= btn_edit('admin/transactions/expense/' . $v_expense->transactions_id) . ' ';
                }
                if (!empty($can_delete) && !empty($deleted)) {
                    $action .= ajax_anchor(base_url("admin/transactions/delete_expense/$v_expense->transactions_id"), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                }
                $sub_array[] = $action;
                $data[] = $sub_array;

            }
            render_table($data, $where);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function save_expense($id = NULL)
    {
        $created = can_action('31', 'created');
        $edited = can_action('31', 'edited');
        if (!empty($created) || !empty($edited) && !empty($id)) {
            $data = $this->transactions_model->array_from_post(array('name', 'date', 'notes', 'category_id', 'paid_by', 'payment_methods_id', 'reference', 'project_id', 'billable', 'client_visible',
                'repeat_every', 'total_cycles', 'done_cycles'));

            $repeat_every_custom = $this->input->post('repeat_every_custom', true);
            $repeat_type_custom = $this->input->post('repeat_type_custom', true);
            // Recurring expense set to NO, Cancelled
            if ($data['repeat_every'] == '') {
                $data['total_cycles'] = 0;
                $data['done_cycles'] = 0;
                $data['last_recurring_date'] = null;
            }
            if (isset($data['repeat_every']) && $data['repeat_every'] != '') {
                $data['recurring'] = 'Yes';
                if ($data['repeat_every'] == 'custom') {
                    $data['repeat_every'] = $repeat_every_custom;
                    $data['recurring_type'] = $repeat_type_custom;
                    $data['custom_recurring'] = 1;
                } else {
                    $_temp = explode('_', $data['repeat_every']);
                    $data['recurring_type'] = $_temp[1];
                    $data['repeat_every'] = $_temp[0];
                    $data['custom_recurring'] = 0;
                }
            } else {
                $data['recurring'] = 'No';
            }
            $data['total_cycles'] = !isset($data['total_cycles']) || $data['recurring'] == 'No' ? 0 : $data['total_cycles'];

            $data['type'] = 'Expense';
            if (empty($data['client_visible'])) {
                $data['client_visible'] = 'No';
            }
            if (empty($data['billable'])) {
                $data['billable'] = 'No';
            }
            $data['account_id'] = $this->input->post('account_id', TRUE);

            $account_info = $this->transactions_model->check_by(array('account_id' => $data['account_id']), 'tbl_accounts');
            if (!empty($account_info)) {
                $account_info = $account_info;
            } else {
                $account_info = $this->db->get('tbl_accounts')->row();
            }

            $data['amount'] = $this->input->post('amount', TRUE);

            if (!empty($data['amount'])) {
                $check_head = $this->db->where('department_head_id', $this->session->userdata('user_id'))->get('tbl_departments')->row();
                $role = $this->session->userdata('user_type');
                if ($role == 1 || !empty($check_head)) {
                    if (!empty($id)) {
                        $data['account_id'] = $this->input->post('old_account_id', TRUE);
                    } else {
                        $data['amount'] = $this->input->post('amount', TRUE);
                        $data['debit'] = $this->input->post('amount', TRUE);

                        $ac_data['balance'] = $account_info->balance - $data['amount'];
                        $this->transactions_model->_table_name = "tbl_accounts"; //table name
                        $this->transactions_model->_primary_key = "account_id";
                        $this->transactions_model->save($ac_data, $account_info->account_id);
                    }

                    $account_info = $this->transactions_model->check_by(array('account_id' => $data['account_id']), 'tbl_accounts');
                    if (!empty($account_info)) {
                        $account_info = $account_info;
                    } else {
                        $account_info = $this->db->get('tbl_accounts')->row();
                    }
                    $data['total_balance'] = $account_info->balance;
                    $data['status'] = 'paid';
                }

                $upload_file = array();
                $files = $this->input->post("files", true);
                $target_path = getcwd() . "/uploads/";
                //process the fiiles which has been uploaded by dropzone
                if (!empty($files) && is_array($files)) {
                    foreach ($files as $key => $file) {
                        if (!empty($file)) {
                            $file_name = $this->input->post('file_name_' . $file, true);
                            $new_file_name = move_temp_file($file_name, $target_path);
                            $file_ext = explode(".", $new_file_name);
                            $is_image = check_image_extension($new_file_name);
                            $size = $this->input->post('file_size_' . $file, true) / 1000;
                            if ($new_file_name) {
                                $up_data = array(
                                    "fileName" => $new_file_name,
                                    "path" => "uploads/" . $new_file_name,
                                    "fullPath" => getcwd() . "/uploads/" . $new_file_name,
                                    "ext" => '.' . end($file_ext),
                                    "size" => round($size, 2),
                                    "is_image" => $is_image,
                                );
                                array_push($upload_file, $up_data);
                            }
                        }
                    }
                }

                $fileName = $this->input->post('fileName', true);
                $path = $this->input->post('path', true);
                $fullPath = $this->input->post('fullPath', true);
                $size = $this->input->post('size', true);
                $is_image = $this->input->post('is_image', true);

                if (!empty($fileName)) {
                    foreach ($fileName as $key => $name) {
                        $old['fileName'] = $name;
                        $old['path'] = $path[$key];
                        $old['fullPath'] = $fullPath[$key];
                        $old['size'] = $size[$key];
                        $old['is_image'] = $is_image[$key];

                        array_push($upload_file, $old);
                    }
                }
                if (!empty($upload_file)) {
                    $data['attachement'] = json_encode($upload_file);
                } else {
                    $data['attachement'] = null;
                }

                $permission = $this->input->post('permission', true);
                if (!empty($permission)) {
                    if ($permission == 'everyone') {
                        $assigned = 'all';
                    } else {
                        $assigned_to = $this->transactions_model->array_from_post(array('assigned_to'));
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
                        redirect('admin/transactions/expense');
                    } else {
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                }


                $this->transactions_model->_table_name = "tbl_transactions"; //table name
                $this->transactions_model->_primary_key = "transactions_id";


                if (!empty($id)) {
                    $this->transactions_model->save($data, $id);
                    $activity = ('activity_update_expense');
                    $msg = lang('update_a_expense');
                    $description = 'not_expense_update';
                    $not_value = lang('title') . ' ' . $data['name'] . ' ' . lang('date') . ' ' . strftime(config_item('date_format'), strtotime($data['date']));
                } else {
                    $data['added_by'] = $this->session->userdata('user_id');
                    $id = $this->transactions_model->save($data);
                    // send sms
                    $this->send_transactions_sms('expense', $id);
                    $activity = ('activity_new_expense');
                    $msg = lang('save_new_expense');
                    $description = 'not_expense_saved';
                    $not_value = lang('account') . ': ' . $account_info->account_name . ' ' . lang('amount') . ': ' . display_money($data['amount']);
                }
                save_custom_field(2, $id);
                // save into activities
                $activities = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'transactions',
                    'module_field_id' => $id,
                    'activity' => $activity,
                    'icon' => 'fa-building-o',
                    'link' => 'admin/transactions/view_details/' . $id,
                    'value1' => $account_info->account_name,
                    'value2' => $data['amount'],
                );
                // Update into tbl_project
                $this->transactions_model->_table_name = "tbl_activities"; //table name
                $this->transactions_model->_primary_key = "activities_id";
                $this->transactions_model->save($activities);
                $type = 'success';
                if ($role == 3 && empty($check_head)) {
                    $this->expense_request_email($data, $id);
                }
                $designation_id = $this->session->userdata('designations_id');
                if (!empty($designation_id)) {
                    $designation_info = $this->transactions_model->check_by(array('designations_id' => $this->session->userdata('designations_id')), 'tbl_designations');
                }
                if (!empty($designation_info)) {
                    $dept_head = $this->transactions_model->check_by(array('departments_id' => $designation_info->departments_id), 'tbl_departments');
                }
                // get departments head by departments id
                $all_admin = $this->db->where('role_id', 1)->get('tbl_users')->result();
                if (!empty($dept_head)) {
                    $head = $this->db->where('user_id', $dept_head->department_head_id)->get('tbl_users')->row();
                    array_push($all_admin, $head);
                }

                $notifyUser = array();
                if (!empty($all_admin)) {
                    foreach ($all_admin as $v_user) {
                        if (!empty($v_user)) {
                            if ($v_user->user_id != $this->session->userdata('user_id')) {
                                array_push($notifyUser, $v_user->user_id);
                                add_notification(array(
                                    'to_user_id' => $v_user->user_id,
                                    'icon' => 'building-o',
                                    'description' => $description,
                                    'link' => 'admin/transactions/view_details/' . $id,
                                    'value' => $not_value,
                                ));
                            }
                        }
                    }
                }
                if (!empty($notifyUser)) {
                    show_notification($notifyUser);
                }

            } else {
                $type = 'error';
                $msg = 'please enter the amount';
            }
            $message = $msg;
            set_message($type, $message);
        }
        if (!empty($data['project_id']) && is_numeric($data['project_id'])) {
            redirect('admin/projects/project_details/' . $data['project_id'] . '/' . '10');
        } else {
            redirect('admin/transactions/expense');
        }

    }

    public function categories($type = null)
    {
        $data['title'] = lang('categories');
        if ($type == 'income') {
            $data['category'] = 'income_category';
        } else {
            $data['category'] = 'expense_category';
        }
        $data['type'] = $type;
        $data['subview'] = $this->load->view('admin/transactions/categories', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function update_categories($type)
    {
        if ($type == 'income') {
            $category = 'income_category';
        } else {
            $category = 'expense_category';
        }
        $this->transactions_model->_table_name = 'tbl_' . $category;
        $this->transactions_model->_primary_key = $category . '_id';


        $cate_data[$category] = $this->input->post('categories', TRUE);
        $cate_data['description'] = $this->input->post('description', TRUE);

        // update root category
        $where = array($category => $cate_data[$category]);
        // duplicate value check in DB
        if (!empty($id)) { // if id exist in db update data
            $expense_category_id = array($category . '_id !=' => $id);
        } else { // if id is not exist then set id as null
            $expense_category_id = null;
        }
        // check whether this input data already exist or not
        $check_category = $this->transactions_model->check_update('tbl_' . $category, $where, $expense_category_id);
        if (!empty($check_category)) { // if input data already exist show error alert
            // massage for user
            $type = 'error';
            $msg = "<strong style='color:#000'>" . $cate_data[$category] . '</strong>  ' . lang('already_exist');
        } else { // save and update query
            $id = $this->transactions_model->save($cate_data);

            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'settings',
                'module_field_id' => $id,
                'activity' => ('activity_added_a_' . $category),
                'value1' => $cate_data[$category]
            );
            $this->transactions_model->_table_name = 'tbl_activities';
            $this->transactions_model->_primary_key = 'activities_id';
            $this->transactions_model->save($activity);

            // messages for user
            $type = "success";
            $msg = lang($category . '_added');
        }
        if (!empty($id)) {
            $result = array(
                'id' => $id,
                'categories' => $cate_data[$category],
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
    }

    public function view_expense($id)
    {
        $data['expense_info'] = $this->db->where('transactions_id', $id)->get('tbl_transactions')->row();
        if (!empty($data['expense_info']) && $data['expense_info']->type == 'Income') {
            $type = lang('deposit');
            $data['permission_user'] = $this->transactions_model->all_permission_user('30');
            $subview = 'view_deposit';
            $data['title'] = $type . ' ' . lang('details');
            $data['subview'] = $this->load->view('admin/transactions/' . $subview, $data, FALSE);
            $this->load->view('admin/_layout_modal', $data);
        } else {
            $type = lang('expense');
            $data['permission_user'] = $this->transactions_model->all_permission_user('31');
            $subview = 'view_expense';
            $data['title'] = $type . ' ' . lang('details');
            $data['subview'] = $this->load->view('admin/transactions/' . $subview, $data, true);
            $this->load->view('admin/_layout_main', $data);
        }
    }

    public function view_details($id)
    {
        $data['expense_info'] = $this->db->where('transactions_id', $id)->get('tbl_transactions')->row();
        if (!empty($data['expense_info']) && $data['expense_info']->type == 'Income') {
            $type = lang('deposit');
            $data['permission_user'] = $this->transactions_model->all_permission_user('30');
            $subview = 'view_deposit';
        } else {
            $type = lang('expense');
            $data['permission_user'] = $this->transactions_model->all_permission_user('31');
            $subview = 'view_expense';
        }
        $data['title'] = $type . ' ' . lang('details');
        $data['subview'] = $this->load->view('admin/transactions/' . $subview, $data, true);
        $this->load->view('admin/_layout_main', $data);
    }

    function deposit_email($data, $id)
    {
        // get departments head user id
        $designation_info = $this->transactions_model->check_by(array('designations_id' => $this->session->userdata('designations_id')), 'tbl_designations');
        // get departments head by departments id
        $dept_head = $this->transactions_model->check_by(array('departments_id' => $designation_info->departments_id), 'tbl_departments');
        $all_admin = $this->db->where('role_id', 1)->get('tbl_users')->result();
        $head = $this->db->where('user_id', $dept_head->department_head_id)->get('tbl_users')->row();
        $account_info = $this->transactions_model->check_by(array('account_id' => $data['account_id']), 'tbl_accounts');

        if (!empty($dept_head->department_head_id) || !empty($all_admin)) {
            $deposit_email = config_item('deposit_email');
            if (!empty($deposit_email) && $deposit_email == 1) {
                $email_template = email_templates(array('email_group' => 'deposit_email'));
                if (!empty($email_template)) {
                    $message = $email_template->template_body;
                    $subject = $email_template->subject;
                    $username = str_replace("{NAME}", $this->session->userdata('name'), $message);
                    $amount = str_replace("{AMOUNT}", $data['amount'], $username);
                    $account = str_replace("{ACCOUNT}", $account_info->account_name, $amount);
                    $balance = str_replace("{BALANCE}", $account_info->balance, $account);
                    $Link = str_replace("{URL}", base_url() . 'admin/transactions/view_details/' . $id, $balance);
                    $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);
                    $data['message'] = $message;
                    $message = $this->load->view('email_template', $data, TRUE);

                    $params['subject'] = $subject;
                    $params['message'] = $message;
                    $params['resourceed_file'] = '';
                    if (!empty($all_admin)) {
                        foreach ($all_admin as $v_admin) {
                            if (!empty($v_admin)) {
                                $params['recipient'] = $v_admin->email;
                                $this->transactions_model->send_email($params);
                                if (!empty($dept_head->department_head_id)) {
                                    if ($dept_head->department_head_id == $v_admin->user_id) {
                                        $already_send = 1;
                                    }
                                }
                            }
                        }
                    }
                    if (empty($already_send) && !empty($head->email)) {
                        $params['recipient'] = $head->email;
                        $this->transactions_model->send_email($params);
                    }

                }
            } else {
                return true;
            }
        }
    }

    function expense_request_email($data, $id)
    {
        // get departments head user id
        $designation_info = $this->transactions_model->check_by(array('designations_id' => $this->session->userdata('designations_id')), 'tbl_designations');
        // get departments head by departments id
        if (!empty($designation_info)) {
            $dept_head = $this->transactions_model->check_by(array('departments_id' => $designation_info->departments_id), 'tbl_departments');
            $head = $this->db->where('user_id', $dept_head->department_head_id)->get('tbl_users')->row();
        }
        $all_admin = $this->db->where('role_id', 1)->get('tbl_users')->result();

        if (!empty($dept_head->department_head_id) || !empty($all_admin)) {
            $expense_email = config_item('expense_email');
            if (!empty($expense_email) && $expense_email == 1) {
                $email_template = email_templates(array('email_group' => 'expense_request_email'));
                if (!empty($email_template)) {
                    $message = $email_template->template_body;
                    $subject = $email_template->subject;
                    $username = str_replace("{NAME}", $this->session->userdata('name'), $message);
                    $amount = str_replace("{AMOUNT}", $data['amount'], $username);
                    $Link = str_replace("{URL}", base_url() . 'admin/transactions/view_details/' . $id, $amount);
                    $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);
                    $data['message'] = $message;
                    $message = $this->load->view('email_template', $data, TRUE);

                    $params['subject'] = $subject;
                    $params['message'] = $message;
                    $params['resourceed_file'] = '';
                    if (!empty($all_admin)) {
                        foreach ($all_admin as $v_admin) {
                            if (!empty($v_admin)) {
                                $params['recipient'] = $v_admin->email;
                                $this->transactions_model->send_email($params);
                                if (!empty($dept_head->department_head_id)) {
                                    if ($dept_head->department_head_id == $v_admin->user_id) {
                                        $already_send = 1;
                                    }
                                }
                            }
                        }
                    }
                    if (empty($already_send) && !empty($head->email)) {
                        $params['recipient'] = $head->email;
                        $this->transactions_model->send_email($params);
                    }

                }
            } else {
                return true;
            }
        }
    }

    public function bulk_delete_expense()
    {
        $selected_id = $this->input->post('ids', true);
        if (!empty($selected_id)) {
            foreach ($selected_id as $id) {
                $result[] = $this->delete_expense($id, true);
            }
            echo json_encode($result);
            exit();
        } else {
            $type = "error";
            $message = lang('you_need_select_to_delete');
            echo json_encode(array("status" => $type, 'message' => $message));
            exit();
        }
    }

    public function delete_expense($id, $bulk = null)
    {
        $deleted = can_action('31', 'deleted');
        $expense_info = $this->transactions_model->check_by(array('transactions_id' => $id), 'tbl_transactions');
        if (!empty($expense_info))
            $can_delete = $this->transactions_model->can_action('tbl_transactions', 'delete', array('transactions_id' => $id));
        if (!empty($deleted) && !empty($can_delete)) {
            $account_info = $this->transactions_model->check_by(array('account_id' => $expense_info->account_id), 'tbl_accounts');

            $ac_data['balance'] = $account_info->balance + $expense_info->amount;
            $this->transactions_model->_table_name = "tbl_accounts"; //table name
            $this->transactions_model->_primary_key = "account_id";
            $this->transactions_model->save($ac_data, $account_info->account_id);

            $activity = ('activity_delete_expense');
            $msg = lang('delete_expense');
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'transactions',
                'module_field_id' => $id,
                'activity' => $activity,
                'icon' => 'fa-building-o',
                'link' => 'admin/transactions/view_details/' . $id,
                'value1' => $account_info->account_name,
                'value2' => $expense_info->amount,
            );
            // Update into tbl_project
            $this->transactions_model->_table_name = "tbl_activities"; //table name
            $this->transactions_model->_primary_key = "activities_id";
            $this->transactions_model->save($activities);

            $comments_info = $this->transactions_model->check_by(array('transactions_id' => $id), 'tbl_transactions');
            if (!empty($comments_info->attachment)) {
                $attachment = json_decode($comments_info->attachment);
                foreach ($attachment as $v_file) {
                    remove_files($v_file->fileName);
                }
            }
            $this->transactions_model->_table_name = "tbl_transactions"; //table name
            $this->transactions_model->_primary_key = "transactions_id";
            $this->transactions_model->delete($id);


            $type = 'success';
        } else {
            $type = 'error';
            $msg = lang('there_in_no_value');
        }
        if (!empty($bulk)) {
            return (array("status" => $type, 'message' => $msg));
        }
        echo json_encode(array("status" => $type, 'message' => $msg));
        exit();
    }

    public function transfer($id = NULL)
    {
        $data['title'] = lang('transfer');
        // get permission user by menu id
        $data['permission_user'] = $this->transactions_model->all_permission_user('32');

        $data['all_transfer_info'] = $this->transactions_model->get_permission('tbl_transfer');
        if (!empty($id)) {
            $transfer_info = $this->transactions_model->check_by(array('transfer_id' => $id), 'tbl_transfer');
            if (!empty($transfer_info))
                $can_edit = $this->transactions_model->can_action('tbl_transfer', 'edit', array('transfer_id' => $id));
            $data['active'] = 2;
            if (!empty($can_edit)) {
                $data['transfer_info'] = $transfer_info;
            }
        } else {
            $data['active'] = 1;
        }
        $data['dropzone'] = true;
        $data['subview'] = $this->load->view('admin/transactions/transfer', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function transferList($filterBy = null, $type = null)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_transfer';
            $this->datatables->join_table = array('tbl_accounts');
            $this->datatables->join_where = array('tbl_accounts.account_id=tbl_transfer.from_account_id');
            $this->datatables->column_order = array('tbl_accounts.account_name', 'date', 'amount', 'payment_methods_id');
            $this->datatables->column_search = array('tbl_accounts.account_name', 'date', 'amount', 'payment_methods_id');
            $this->datatables->order = array('transfer_id' => 'desc');

            $where = null;
            if ($type == 'to_account') {
                $where = array('to_account_id' => $filterBy);
            } else if ($type == 'from_account') {
                $where = array('from_account_id' => $filterBy);
            }
            $fetch_data = $this->datatables->get_transfer($filterBy, $type);

            $data = array();

            $edited = can_action('30', 'edited');
            $deleted = can_action('30', 'deleted');
            foreach ($fetch_data as $_key => $v_transfer) {
                $action = null;
                $can_edit = $this->transactions_model->can_action('tbl_transfer', 'edit', array('transfer_id' => $v_transfer->transfer_id));
                $can_delete = $this->transactions_model->can_action('tbl_transfer', 'delete', array('transfer_id' => $v_transfer->transfer_id));

                $to_account_info = $this->transactions_model->check_by(array('account_id' => $v_transfer->to_account_id), 'tbl_accounts');
                $from_account_info = $this->transactions_model->check_by(array('account_id' => $v_transfer->from_account_id), 'tbl_accounts');

                $sub_array = array();
                $sub_array[] = (!empty($from_account_info->account_name) ? $from_account_info->account_name : '-');
                $sub_array[] = (!empty($to_account_info->account_name) ? $to_account_info->account_name : '-');

                $sub_array[] = display_money($v_transfer->amount, default_currency());

                $sub_array[] = strftime(config_item('date_format'), strtotime($v_transfer->date));

                $attachment = null;
                $attachment_info = json_decode($v_transfer->attachement);
                if (!empty($attachment_info)) {
                    $attachment = '<a href="' . base_url() . 'admin/transactions/download_transfer/' . $v_transfer->transfer_id . '">' . lang('download') . '</a>';
                }
                $sub_array[] = $attachment;

                if (!empty($can_edit) && !empty($edited)) {
                    $action .= btn_edit('admin/transactions/transfer/' . $v_transfer->transfer_id) . ' ';
                }
                if (!empty($can_delete) && !empty($deleted)) {
                    $action .= ajax_anchor(base_url("admin/transactions/delete_transfer/$v_transfer->transfer_id"), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                }
                $sub_array[] = $action;
                $data[] = $sub_array;

            }
            render_table($data, $where);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function save_transfer($id = NULL)
    {
        $created = can_action('32', 'created');
        $edited = can_action('32', 'edited');
        if (!empty($created) || !empty($edited) && !empty($id)) {
            if (!empty($id)) {
                $to_account_id = $this->input->post('old_to_account_id', TRUE);
                $from_account_id = $this->input->post('old_from_account_id', TRUE);
                $amount = $this->input->post('old_amount', TRUE);
                $transaction_id = $this->db->select('transactions_id')->where(array('transfer_id' => $id))->get('tbl_transactions')->result();
            } else {
                $to_account_id = $this->input->post('to_account_id', TRUE);
                $from_account_id = $this->input->post('from_account_id', TRUE);
                $amount = $this->input->post('amount', TRUE);
            }
            if (!empty($transaction_id[0]->transactions_id)) {
                $transaction_id_1 = $transaction_id[0]->transactions_id;
            } else {
                $transaction_id_1 = null;
            }
            if (!empty($transaction_id[1]->transactions_id)) {
                $transaction_id_2 = $transaction_id[1]->transactions_id;
            } else {
                $transaction_id_2 = null;
            }

            if ($to_account_id == $from_account_id) {
                $type = 'error';
                $msg = lang('same_account_error');
            } else {
                $from_acc_info = $this->transactions_model->check_by(array('account_id' => $from_account_id), 'tbl_accounts');
                $to_acc_info = $this->transactions_model->check_by(array('account_id' => $to_account_id), 'tbl_accounts');
                if ($amount > $from_acc_info->balance) {
                    $type = 'error';
                    $msg = lang('amount_exceed_error') . ' <strong style=color:#000> ' . $from_acc_info->balance . '</strong> !';
                } else {

                    $ac_data['balance'] = $from_acc_info->balance - $amount;
                    $this->transactions_model->_table_name = "tbl_accounts"; //table name
                    $this->transactions_model->_primary_key = "account_id";
                    $this->transactions_model->save($ac_data, $from_acc_info->account_id);

                    $froma_data['balance'] = $to_acc_info->balance + $amount;
                    $this->transactions_model->_table_name = "tbl_accounts"; //table name
                    $this->transactions_model->_primary_key = "account_id";
                    $this->transactions_model->save($froma_data, $to_acc_info->account_id);


                    // save into tbl_transfer
                    $transfer_data = $this->transactions_model->array_from_post(array('date', 'notes', 'payment_methods_id', 'reference'));
                    $transfer_data['type'] = 'Transfer';
                    $transfer_data['to_account_id'] = $to_account_id;
                    $transfer_data['from_account_id'] = $from_account_id;
                    $transfer_data['amount'] = $amount;

                    $upload_file = array();
                    $files = $this->input->post("files", true);
                    $target_path = getcwd() . "/uploads/";
                    //process the fiiles which has been uploaded by dropzone
                    if (!empty($files) && is_array($files)) {
                        foreach ($files as $key => $file) {
                            if (!empty($file)) {
                                $file_name = $this->input->post('file_name_' . $file, true);
                                $new_file_name = move_temp_file($file_name, $target_path);
                                $file_ext = explode(".", $new_file_name);
                                $is_image = check_image_extension($new_file_name);
                                $size = $this->input->post('file_size_' . $file, true) / 1000;
                                if ($new_file_name) {
                                    $up_data = array(
                                        "fileName" => $new_file_name,
                                        "path" => "uploads/" . $new_file_name,
                                        "fullPath" => getcwd() . "/uploads/" . $new_file_name,
                                        "ext" => '.' . end($file_ext),
                                        "size" => round($size, 2),
                                        "is_image" => $is_image,
                                    );
                                    array_push($upload_file, $up_data);
                                }
                            }
                        }
                    }

                    $fileName = $this->input->post('fileName', true);
                    $path = $this->input->post('path', true);
                    $fullPath = $this->input->post('fullPath', true);
                    $size = $this->input->post('size', true);
                    $is_image = $this->input->post('is_image', true);

                    if (!empty($fileName)) {
                        foreach ($fileName as $key => $name) {
                            $old['fileName'] = $name;
                            $old['path'] = $path[$key];
                            $old['fullPath'] = $fullPath[$key];
                            $old['size'] = $size[$key];
                            $old['is_image'] = $is_image[$key];

                            array_push($upload_file, $old);
                        }
                    }
                    if (!empty($upload_file)) {
                        $data['attachement'] = json_encode($upload_file);
                    } else {
                        $data['attachement'] = null;
                    }

                    $permission = $this->input->post('permission', true);
                    if (!empty($permission)) {
                        if ($permission == 'everyone') {
                            $assigned = 'all';
                        } else {
                            $assigned_to = $this->transactions_model->array_from_post(array('assigned_to'));
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
                        $transfer_data['permission'] = $assigned;
                    } else {
                        set_message('error', lang('assigned_to') . ' Field is required');
                        if (empty($_SERVER['HTTP_REFERER'])) {
                            redirect('admin/transactions/transfer');
                        } else {
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                    }

                    $this->transactions_model->_table_name = "tbl_transfer"; //table name
                    $this->transactions_model->_primary_key = "transfer_id";
                    $transfer_id = $this->transactions_model->save($transfer_data, $id);

                    $from_acc_info = $this->transactions_model->check_by(array('account_id' => $from_account_id), 'tbl_accounts');
                    $to_acc_info = $this->transactions_model->check_by(array('account_id' => $to_account_id), 'tbl_accounts');

                    // save into tbl_tansactions
                    $to_data = $this->transactions_model->array_from_post(array('date', 'notes', 'payment_methods_id', 'reference'));
                    $to_data['type'] = 'Transfer';
                    $to_data['account_id'] = $to_account_id;
                    $to_data['amount'] = $amount;
                    $to_data['credit'] = $amount;
                    $to_data['total_balance'] = $to_acc_info->balance;
                    $to_data['transfer_id'] = $transfer_id;

                    $this->transactions_model->_table_name = "tbl_transactions"; //table name
                    $this->transactions_model->_primary_key = "transactions_id";
                    $this->transactions_model->save($to_data, $transaction_id_1);

                    // save into tbl_tansactions
                    $from_data = $this->transactions_model->array_from_post(array('date', 'notes', 'payment_methods_id', 'reference'));
                    $from_data['type'] = 'Transfer';
                    $from_data['account_id'] = $from_account_id;
                    $from_data['amount'] = $amount;
                    $from_data['debit'] = $amount;
                    $from_data['total_balance'] = $from_acc_info->balance;
                    $from_data['transfer_id'] = $transfer_id;

                    $this->transactions_model->_table_name = "tbl_transactions"; //table name
                    $this->transactions_model->_primary_key = "transactions_id";
                    $this->transactions_model->save($from_data, $transaction_id_2);

                    $type = 'success';
                    if (!empty($id)) {
                        $activity = ('activity_update_transfer');
                        $msg = lang('update_a_transfer');
                        $description = 'not_update_transfer';

                    } else {
                        // send sms
                        $this->send_transactions_sms('transfer', $id);
                        $activity = ('activity_new_transfer');
                        $msg = lang('save_new_transfer');
                        $description = 'not_new_transfer';
                    }
                    // save into activities
                    $activities = array(
                        'user' => $this->session->userdata('user_id'),
                        'module' => 'transactions',
                        'module_field_id' => $id,
                        'activity' => $activity,
                        'icon' => 'fa-building-o',
                        'link' => 'admin/transactions/transfer',
                        'value1' => $from_acc_info->account_name,
                        'value2' => $to_acc_info->account_name,
                    );
                    // Update into tbl_project
                    $this->transactions_model->_table_name = "tbl_activities"; //table name
                    $this->transactions_model->_primary_key = "activities_id";
                    $this->transactions_model->save($activities);


                    // get departments head user id
                    $designation_info = $this->transactions_model->check_by(array('designations_id' => $this->session->userdata('designations_id')), 'tbl_designations');
                    if (!empty($designation_info)) {
                        $dept_head = $this->transactions_model->check_by(array('departments_id' => $designation_info->departments_id), 'tbl_departments');
                    }
                    // get departments head by departments id
                    $all_admin = $this->db->where('role_id', 1)->get('tbl_users')->result();
                    if (!empty($dept_head)) {
                        $head = $this->db->where('user_id', $dept_head->department_head_id)->get('tbl_users')->row();
                        array_push($all_admin, $head);
                    }
                    $notifyUser = array();
                    if (!empty($all_admin)) {
                        foreach ($all_admin as $v_user) {
                            if (!empty($v_user)) {
                                if ($v_user->user_id != $this->session->userdata('user_id')) {
                                    array_push($notifyUser, $v_user->user_id);
                                    add_notification(array(
                                        'to_user_id' => $v_user->user_id,
                                        'icon' => 'building-o',
                                        'description' => $description,
                                        'link' => 'admin/transactions/transfer',
                                        'value' => lang('amount') . ' ' . display_money($amount) . ' ' . lang('from_account') . $from_acc_info->account_name . ' ' . lang('to_account') . ' ' . $to_acc_info->account_name,
                                    ));
                                }
                            }
                        }
                    }
                    if (!empty($notifyUser)) {
                        show_notification($notifyUser);
                    }

                }
            }

            $message = $msg;
            set_message($type, $message);
        }
        redirect('admin/transactions/transfer');
    }

    public function delete_transfer($id)
    {
        $deleted = can_action('31', 'deleted');
        $can_delete = $this->transactions_model->can_action('tbl_transfer', 'delete', array('transfer_id' => $id));
        if (!empty($deleted) && !empty($can_delete)) {
            $transfer_info = $this->transactions_model->check_by(array('transfer_id' => $id), 'tbl_transfer');
            $from_acc_info = $this->transactions_model->check_by(array('account_id' => $transfer_info->to_account_id), 'tbl_accounts');
            $to_acc_info = $this->transactions_model->check_by(array('account_id' => $transfer_info->from_account_id), 'tbl_accounts');

            $ac_data['balance'] = $from_acc_info->balance + $transfer_info->amount;
            $this->transactions_model->_table_name = "tbl_accounts"; //table name
            $this->transactions_model->_primary_key = "account_id";
            $this->transactions_model->save($ac_data, $from_acc_info->account_id);

            $froma_data['balance'] = $to_acc_info->balance - $transfer_info->amount;
            $this->transactions_model->_table_name = "tbl_accounts"; //table name
            $this->transactions_model->_primary_key = "account_id";
            $this->transactions_model->save($froma_data, $to_acc_info->account_id);

            $comments_info = $this->transactions_model->check_by(array('transactions_id' => $id), 'tbl_transactions');
            if (!empty($comments_info->attachment)) {
                $attachment = json_decode($comments_info->attachment);
                foreach ($attachment as $v_file) {
                    remove_files($v_file->fileName);
                }
            }

            $this->transactions_model->_table_name = "tbl_transfer"; //table name
            $this->transactions_model->_primary_key = "transfer_id";
            $this->transactions_model->delete($id);

            $this->transactions_model->_table_name = "tbl_transactions"; //table name
            $this->transactions_model->delete_multiple(array('transfer_id' => $id));

            $activity = ('activity_delete_transfer');
            $msg = lang('delete_transfer');

            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'transactions',
                'module_field_id' => $id,
                'activity' => $activity,
                'icon' => 'fa-building-o',
                'value1' => $from_acc_info->account_name,
                'value2' => $to_acc_info->account_name,
            );
            // Update into tbl_project
            $this->transactions_model->_table_name = "tbl_activities"; //table name
            $this->transactions_model->_primary_key = "activities_id";
            $this->transactions_model->save($activities);

            $type = 'success';
        } else {
            $type = 'error';
            $msg = lang('there_in_no_value');
        }
        echo json_encode(array("status" => $type, 'message' => $msg));
        exit();
//        $message = $msg;
//        set_message($type, $message);
//        redirect('admin/transactions/transfer/');
    }

    public function transactions_report($id = null)
    {
        $data['title'] = lang('transactions_report');
        $data['transactions_report'] = $this->get_transactions_report();
        $data['subview'] = $this->load->view('admin/transactions/transactions_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function transactions_reportList($filterBy = null)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_transactions';
            $this->datatables->join_table = array('tbl_accounts');
            $this->datatables->join_where = array('tbl_accounts.account_id=tbl_transactions.account_id');
            $this->datatables->column_order = array('name', 'type', 'date', 'amount', 'tbl_accounts.account_name');
            $this->datatables->column_search = array('name', 'type', 'date', 'amount', 'tbl_accounts.account_name');
            $this->datatables->order = array('transactions_id' => 'desc');
            $where = null;
            if (!empty($filterBy)) {
                $where = array('tbl_transactions.account_id' => $filterBy);
            }
            // get all invoice
            $fetch_data = $this->datatables->get_transactions_report($filterBy);

            $data = array();

            $total_amount = 0;
            $total_debit = 0;
            $total_credit = 0;
            $total_balance = 0;
            foreach ($fetch_data as $_key => $v_transaction) {
                $action = null;
                $account_info = $this->transactions_model->check_by(array('account_id' => $v_transaction->account_id), 'tbl_accounts');

                $sub_array = array();
                $sub_array[] = strftime(config_item('date_format'), strtotime($v_transaction->date));
                $sub_array[] = (!empty($account_info->account_name) ? $account_info->account_name : '-');
                $sub_array[] = lang($v_transaction->type);
                $sub_array[] = (!empty($v_transaction->name) ? $v_transaction->name : '-');

                $sub_array[] = display_money($v_transaction->amount, default_currency());
                $sub_array[] = display_money($v_transaction->credit, default_currency());
                $sub_array[] = display_money($v_transaction->debit, default_currency());
                $sub_array[] = display_money($v_transaction->total_balance, default_currency());

                $data[] = $sub_array;

                $total_amount += $v_transaction->amount;
                $total_credit += $v_transaction->credit;
                $total_debit += $v_transaction->debit;


            }
            render_table($data, $where);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function get_transactions_report()
    {// this function is to create get monthy recap report
        $m = date('n');
        $year = date('Y');
        $num = cal_days_in_month(CAL_GREGORIAN, $m, $year);
        for ($i = 1; $i <= $num; $i++) {
            if ($m >= 1 && $m <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                $date = $year . "-" . '0' . $m;
            } else {
                $date = $year . "-" . $m;
            }
            $date = $date . '-' . $i;
            $transaction_report[$i] = $this->db->where('date', $date)->order_by('transactions_id', 'DESC')->get('tbl_transactions')->result();
        }
        return $transaction_report; // return the result
    }

    public function transactions_report_pdf()
    {
        $data['title'] = lang('transactions_report');
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/transactions/transactions_report_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it(lang('transactions_report')));
    }

    public function transfer_report()
    {
        $data['title'] = lang('transfer_report');
        $data['subview'] = $this->load->view('admin/transactions/transfer_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function transfer_reportList($filterBy = null, $type = null)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_transfer';
            $this->datatables->join_table = array('tbl_accounts');
            $this->datatables->join_where = array('tbl_accounts.account_id=tbl_transfer.from_account_id');
            $this->datatables->column_order = array('tbl_accounts.account_name', 'date', 'amount', 'payment_methods_id');
            $this->datatables->column_search = array('tbl_accounts.account_name', 'date', 'amount', 'payment_methods_id');
            $this->datatables->order = array('transfer_id' => 'desc');
            $where = null;
            if ($type == 'to_account') {
                $where = array('to_account_id' => $filterBy);
            } else if ($type == 'from_account') {
                $where = array('from_account_id' => $filterBy);
            }
            // get all invoice
            $fetch_data = $this->datatables->get_transfer($filterBy, $type);

            $data = array();

            $edited = can_action('30', 'edited');
            $deleted = can_action('30', 'deleted');
            foreach ($fetch_data as $_key => $v_transfer) {
                $action = null;
                $can_edit = $this->transactions_model->can_action('tbl_transfer', 'edit', array('transfer_id' => $v_transfer->transfer_id));
                $can_delete = $this->transactions_model->can_action('tbl_transfer', 'delete', array('transfer_id' => $v_transfer->transfer_id));

                $to_account_info = $this->transactions_model->check_by(array('account_id' => $v_transfer->to_account_id), 'tbl_accounts');
                $from_account_info = $this->transactions_model->check_by(array('account_id' => $v_transfer->from_account_id), 'tbl_accounts');

                $sub_array = array();
                $sub_array[] = strftime(config_item('date_format'), strtotime($v_transfer->date));
                $sub_array[] = (!empty($from_account_info->account_name) ? $from_account_info->account_name : '-');
                $sub_array[] = (!empty($from_account_info->account_name) ? $to_account_info->account_name : '-');
                $sub_array[] = $v_transfer->type;

                $sub_array[] = display_money($v_transfer->amount, default_currency());

                $data[] = $sub_array;

            }
            render_table($data, $where);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function transfer_report_pdf()
    {
        $data['title'] = lang('transfer_report');
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/transactions/transfer_report_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it(lang('transfer_report')));
    }

    public function balance_sheet()
    {
        $data['title'] = lang('balance_sheet');
        $data['subview'] = $this->load->view('admin/transactions/balance_sheet', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function balance_sheet_pdf()
    {
        $data['title'] = lang('balance_sheet') . ' ' . lang('pdf');
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/transactions/balance_sheet_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it(lang('balance_sheet')));
    }

    public function download($id, $fileName = null)
    {
        if (!empty($fileName)) {
            $this->load->helper('download');
            if ($id) {
                $down_data = file_get_contents('uploads/' . $fileName); // Read the file's contents
                force_download($fileName, $down_data);
            } else {
                $type = "error";
                $message = 'Operation Fieled !';
                set_message($type, $message);
                if (empty($_SERVER['HTTP_REFERER'])) {
                    redirect('admin/dashboard');
                } else {
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }
        } else {
            $this->load->library('zip');

            $file_info = $this->transactions_model->check_by(array('transactions_id' => $id), 'tbl_transactions');

            $attachement_info = json_decode($file_info->attachement);
            if (!empty($attachement_info)) {
                $total = count($attachement_info);
                foreach ($attachement_info as $attachement) {
                    if ($total == 1) {
                        $this->load->helper('download');
                        $down_data = file_get_contents('uploads/' . $attachement->fileName); // Read the file's contents
                        force_download($attachement->fileName, $down_data);
                    } else {
                        $multiple = true;
                        $down_data = ('uploads/' . $attachement->fileName); // Read the file's contents
                        $this->zip->read_file($down_data);
                    }
                }
                if (!empty($multiple)) {
                    $file_name = $file_info->date . ' ' . $file_info->type;
                    $this->zip->download($file_name . '.zip');
                }

            } else {
                $type = "error";
                $message = 'Operation Fieled !';
                set_message($type, $message);
                if (empty($_SERVER['HTTP_REFERER'])) {
                    redirect('admin/dashboard');
                } else {
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }
        }
    }

    public function download_transfer($id)
    {
        $this->load->library('zip');

        $file_info = $this->transactions_model->check_by(array('transfer_id' => $id), 'tbl_transfer');

        $attachement_info = json_decode($file_info->attachement);
        if (!empty($attachement_info)) {
            $total = count($attachement_info);
            foreach ($attachement_info as $attachement) {
                if ($total == 1) {
                    $this->load->helper('download');
                    $down_data = file_get_contents('uploads/' . $attachement->fileName); // Read the file's contents
                    force_download($attachement->fileName, $down_data);
                } else {
                    $multiple = true;
                    $down_data = ('uploads/' . $attachement->fileName); // Read the file's contents
                    $this->zip->read_file($down_data);
                }
            }
            if (!empty($multiple)) {
                $file_name = $file_info->date . ' ' . $file_info->type;
                $this->zip->download($file_name . '.zip');
            }

        } else {
            $type = "error";
            $message = 'Operation Fieled !';
            set_message($type, $message);
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/dashboard');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public function set_status($action, $id)
    {
        $transaction_info = $this->db->where('transactions_id', $id)->get('tbl_transactions')->row();
        $account_info = $this->transactions_model->check_by(array('account_id' => $transaction_info->account_id), 'tbl_accounts');

        if ($action == 'approved') {
            $status = 'unpaid';
            $activity = 'activity_approved_expense';
        }

        if ($action == 'paid') {
            $status = 'paid';

            $data['amount'] = $transaction_info->amount;
            $data['debit'] = $transaction_info->amount;
            $ac_data['balance'] = $account_info->balance - $data['amount'];

            $this->transactions_model->_table_name = "tbl_accounts"; //table name
            $this->transactions_model->_primary_key = "account_id";
            $this->transactions_model->save($ac_data, $transaction_info->account_id);


            $data['total_balance'] = $account_info->balance;
            $activity = 'activity_paid_expense';
        }
        $data['status'] = $status;
        $this->transactions_model->_table_name = "tbl_transactions"; //table name
        $this->transactions_model->_primary_key = "transactions_id";

        $this->transactions_model->save($data, $id);

        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'transactions',
            'module_field_id' => $id,
            'activity' => $activity,
            'icon' => 'fa-building-o',
            'link' => 'admin/transactions/view_details/' . $id,
            'value1' => $account_info->account_name . ' ' . lang('amount') . $transaction_info->amount,
            'value2' => 'By ' . $this->session->userdata('name'),
        );
        // Update into tbl_project
        $this->transactions_model->_table_name = "tbl_activities"; //table name
        $this->transactions_model->_primary_key = "activities_id";
        $this->transactions_model->save($activities);
        $type = 'success';

        $this->expense_confirmation_email($id, $action);

        $message = lang('update_a_expense');
        set_message($type, $message);
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('admin/dashboard');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    function expense_confirmation_email($id, $action)
    {
        $transaction_info = $this->db->where('transactions_id', $id)->get('tbl_transactions')->row();
        $added_info = $this->db->where('user_id', $transaction_info->added_by)->get('tbl_users')->row();

        // send confirmation to this employee
        if ($action == 'approved') {

            $expense_email = config_item('expense_email');
            if (!empty($expense_email) && $expense_email == 1) {
                $email_template = email_templates(array('email_group' => 'expense_approved_email'));

                $message = $email_template->template_body;
                $subject = $email_template->subject;
                $username = str_replace("{NAME}", $added_info->username, $message);
                $amount = str_replace("{AMOUNT}", $transaction_info->amount, $username);
                $message = str_replace("{SITE_NAME}", config_item('company_name'), $amount);
                $data['message'] = $message;
                $message = $this->load->view('email_template', $data, TRUE);

                $params['subject'] = $subject;
                $params['message'] = $message;
                $params['resourceed_file'] = '';
                $params['recipient'] = $added_info->email;
                $this->transactions_model->send_email($params);


                if (!empty($added_info->user_id)) {
                    $notifiedUsers = array($added_info->user_id);
                    foreach ($notifiedUsers as $users) {
                        if ($users != $this->session->userdata('user_id')) {
                            add_notification(array(
                                'to_user_id' => $users,
                                'description' => 'not_expense_approve',
                                'icon' => 'building-o',
                                'link' => 'admin/transactions/view_details/' . $id,
                                'value' => lang('name') . ' ' . $transaction_info->name . ' ' . lang('amount') . display_money($transaction_info->amount),
                            ));
                        }
                    }
                    show_notification($notifiedUsers);
                }

            }
        }
        if ($action == 'paid') {
            // get departments head user id
            $designation_info = $this->transactions_model->check_by(array('designations_id' => $this->session->userdata('designations_id')), 'tbl_designations');
            $all_admin = $this->db->where('role_id', 1)->get('tbl_users')->result();
            if (!empty($designation_info)) {
                // get departments head by departments id
                $dept_head = $this->transactions_model->check_by(array('departments_id' => $designation_info->departments_id), 'tbl_departments');
                $head = $this->db->where('user_id', $dept_head->department_head_id)->get('tbl_users')->row();
                array_push($all_admin, $head);
            }

            if (!empty($dept_head->department_head_id) || !empty($all_admin)) {
                $expense_email = config_item('expense_email');
                if (!empty($expense_email) && $expense_email == 1) {
                    $email_template = email_templates(array('email_group' => 'expense_paid_email'));

                    $message = $email_template->template_body;
                    $subject = $email_template->subject;
                    $username = str_replace("{NAME}", $added_info->username, $message);
                    $amount = str_replace("{AMOUNT}", $transaction_info->amount, $username);
                    $PAID_BY = str_replace("{PAID_BY}", $this->session->userdata('name'), $amount);
                    $Link = str_replace("{URL}", base_url() . 'admin/transactions/view_details/' . $id, $PAID_BY);
                    $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);
                    $data['message'] = $message;
                    $message = $this->load->view('email_template', $data, TRUE);

                    $params['subject'] = $subject;
                    $params['message'] = $message;
                    $params['resourceed_file'] = '';
                    $notifyUser = array();
                    if (!empty($all_admin)) {
                        foreach ($all_admin as $v_admin) {
                            if (!empty($v_admin)) {
                                array_push($notifyUser, $v_admin->user_id);
                                $params['recipient'] = $v_admin->email;
                                $this->transactions_model->send_email($params);
                                if (!empty($dept_head->department_head_id)) {
                                    if ($dept_head->department_head_id == $v_admin->user_id) {
                                        $already_send = 1;
                                    }
                                }
                                if ($v_admin->user_id != $this->session->userdata('user_id')) {
                                    add_notification(array(
                                        'to_user_id' => $v_admin->user_id,
                                        'description' => 'not_expense_paid',
                                        'icon' => 'building-o',
                                        'link' => 'admin/transactions/view_details/' . $id,
                                        'value' => lang('name') . ' ' . $transaction_info->name . ' ' . lang('amount') . display_money($transaction_info->amount),
                                    ));
                                }
                            }
                        }
                    }

                    if (!empty($notifyUser)) {
                        show_notification($notifyUser);
                    }

                    if (empty($already_send)) {
                        $params['recipient'] = $head->email;
                        $this->transactions_model->send_email($params);
                    }


                }
            }
        }
        return true;
    }

    public function download_pdf($id)
    {
        $this->load->helper('dompdf');
        $data['expense_info'] = $this->db->where('transactions_id', $id)->get('tbl_transactions')->row();
        if (!empty($data['expense_info']) && $data['expense_info']->type == 'Income') {
            $type = lang('deposit');
        } else {
            $type = lang('expense');
        }
        $data['title'] = $type . ' ' . lang('details');
        $viewfile = $this->load->view('admin/transactions/download_pdf', $data, TRUE);
        if (!empty($data['expense_info']->reference)) {
            $reference = $data['expense_info']->reference;
        } else {
            $reference = null;
        }
        pdf_create($viewfile, slug_it($type . ' ' . lang('details') . $reference));
    }

    public function convert($id)
    {
        // get permission user
        $data['expense_info'] = $this->db->where('transactions_id', $id)->get('tbl_transactions')->row();
        if ($data['expense_info']->project_id != 0 && $data['expense_info']->invoices_id == 0) {
            $data['title'] = lang('convert_to_invoice');
            $data['permission_user'] = $this->invoice_model->all_permission_user('13');
            $data['subview'] = $this->load->view('admin/transactions/invoice_preview', $data, TRUE);
            $this->load->view('admin/_layout_main', $data); //page load
        } else {
            set_message('error', lang('there_in_no_value'));
            redirect('admin/transactions/expense');
        }
    }

}
