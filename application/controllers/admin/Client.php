<?php

/**
 * Description of client
 *
 * @author NaYeM
 */
class Client extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('client_model');
        $this->load->model('invoice_model');
        $this->load->model('estimates_model');
    }

    public function manage_client($id = NULL)
    {
        if (!empty($id)) {
            if (is_numeric($id)) {
                $data['active'] = 2;
                // get all Client info by client id
                $this->client_model->_table_name = "tbl_client"; //table name
                $this->client_model->_order_by = "client_id";
                $data['client_info'] = $this->client_model->get_by(array('client_id' => $id), TRUE);
                $edited = can_action('4', 'edited');
                if (empty($data['client_info']) || empty($edited)) {
                    $type = "error";
                    $message = "No Record Found";
                    set_message($type, $message);
                    redirect('admin/client/manage_client');
                }
            } else {
                $data['active'] = 1;
            }
        } else {
            $data['active'] = 1;
        }
        $data['title'] = lang('manage_client'); //Page title
        $data['page'] = lang('client');

        // get all country
        $this->client_model->_table_name = "tbl_countries"; //table name
        $this->client_model->_order_by = "id";
        $data['countries'] = $this->client_model->get();

        // get all currencies
        $this->client_model->_table_name = 'tbl_currencies';
        $this->client_model->_order_by = 'name';
        $data['currencies'] = $this->client_model->get();
        // get all language
        $data['languages'] = $this->db->where('active', 1)->order_by('name', 'ASC')->get('tbl_languages')->result();

        $data['subview'] = $this->load->view('admin/client/manage_client', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function clientList($type = null)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_client';
            $this->datatables->join_table = array('tbl_customer_group');
            $this->datatables->join_where = array('tbl_customer_group.customer_group_id=tbl_client.customer_group_id');
            $this->datatables->column_search = array('tbl_client.name', 'tbl_client.email', 'short_note', 'website', 'tbl_customer_group.customer_group');
            $this->datatables->column_order = array(' ', 'tbl_client.name', 'tbl_client.email', 'short_note', 'website', 'tbl_customer_group.customer_group');
            $this->datatables->order = array('client_id' => 'desc');
            // get all invoice
            if (!empty($type)) {
                $where = array('tbl_client.customer_group_id' => $type);
            } else {
                $where = null;
            }

            $fetch_data = make_datatables($where);

            $data = array();
            $edited = can_action('4', 'edited');
            $deleted = can_action('4', 'deleted');
            foreach ($fetch_data as $_key => $client_details) {
                $action = null;
                $client_transactions = $this->db->select_sum('amount')->where(array('paid_by' => $client_details->client_id))->get('tbl_transactions')->result();
                $customer_group = $this->db->where('customer_group_id', $client_details->customer_group_id)->get('tbl_customer_group')->row();

                $client_outstanding = $this->invoice_model->client_outstanding($client_details->client_id);

                $sub_array = array();
                if (!empty($deleted)) {
                    $sub_array[] = '<div class="checkbox c-checkbox" ><label class="needsclick"> <input value="' . $client_details->client_id . '" type="checkbox"><span class="fa fa-check"></span></label></div>';
                }
                $name = null;
                $name .= '<a class="text-info" href="' . base_url() . 'admin/client/client_details/' . $client_details->client_id . '">' . (!empty($client_details->name) ? $client_details->name : '-') . '</a>';
                $sub_array[] = $name;

                $contacts = null;
                $contacts .= '<span class="label label-success" data-toggle="tooltip" data-palcement="top" title="' . lang('contacts') . '" >' . $this->client_model->count_rows('tbl_account_details', array('company' => $client_details->client_id)) . '</a>';
                $sub_array[] = $contacts;
                $sub_array[] = fullname($client_details->primary_contact);

                $sub_array[] = count($this->db->where('client_id', $client_details->client_id)->get('tbl_project')->result());
                if ($client_outstanding > 0) {
                    $due_amount = display_money($client_outstanding, client_currency($client_details->client_id));
                } else {
                    $due_amount = '0.00';
                }
                $sub_array[] = $due_amount;
                $sub_array[] = display_money($this->client_model->client_paid($client_details->client_id), client_currency($client_details->client_id));
                if ($client_transactions[0]->amount > 0) {
                    $paid_amount = display_money($client_transactions[0]->amount, client_currency($client_details->client_id));
                } else {
                    $paid_amount = '0.00';
                }
                $sub_array[] = $paid_amount;
                $sub_array[] = (!empty($customer_group->customer_group) ? $customer_group->customer_group : '-');

                $custom_form_table = custom_form_table(12, $client_details->client_id);
                if (!empty($custom_form_table)) {
                    foreach ($custom_form_table as $c_label => $v_fields) {
                        $sub_array[] = $v_fields;
                    }
                }

                if (!empty($edited)) {
                    $action .= btn_edit('admin/client/manage_client/' . $client_details->client_id) . ' ';
                }
                if (!empty($deleted)) {
                    $action .= '<a data-toggle="tooltip" data-placement="top" class="btn btn-danger btn-xs" title="Click to ' . lang("delete") . ' " href="' . base_url() . 'admin/client/delete_client/' . $client_details->client_id . '"><span class="fa fa-trash-o"></span></a>' . ' ';
                }
                $action .= btn_view('admin/client/client_details/' . $client_details->client_id) . ' ';

                $sub_array[] = $action;
                $data[] = $sub_array;

            }

            render_table($data, $where);
        } else {
            redirect('admin/dashboard');
        }
    }


    public function change_client_status($id = null)
    {
        $edited = can_action('4', 'edited');
        if (empty($edited)) {
            $type = "error";
            $message = "No Record Found";
            echo json_encode(array("status" => $type, "message" => $message));
            exit();
        }
        $data['active'] = $this->input->post('active', true);
        $this->client_model->_table_name = 'tbl_client';
        $this->client_model->_primary_key = "client_id";
        $this->client_model->save($data, $id);
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'client',
            'module_field_id' => $id,
            'activity' => 'activity_change_status',
            'icon' => 'fa-ticket',
            'value1' => $data['active'],
        );
        // Update into tbl_project
        $this->client_model->_table_name = "tbl_activities"; //table name
        $this->client_model->_primary_key = "activities_id";
        $this->client_model->save($activities);
        $type = "success";
        $message = lang('update_client_status');
        echo json_encode(array("status" => $type, "message" => $message));
        exit();
    }

    public function searchByGroup($id = NULL)
    {
        $data['active'] = 1;
        $data['title'] = lang('manage_client'); //Page title
        $data['page'] = lang('client');

        // get all country
        $this->client_model->_table_name = "tbl_countries"; //table name
        $this->client_model->_order_by = "id";
        $data['countries'] = $this->client_model->get();

        // get all currencies
        $this->client_model->_table_name = 'tbl_currencies';
        $this->client_model->_order_by = 'name';
        $data['currencies'] = $this->client_model->get();
        // get all language
        $data['languages'] = $this->db->where('active', 1)->order_by('name', 'ASC')->get('tbl_languages')->result();

        $data['all_client_info'] = $this->db->where('customer_group_id', $id)->get('tbl_client')->result();

        $data['subview'] = $this->load->view('admin/client/manage_client', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function import()
    {
        $data['title'] = lang('import') . ' ' . lang('client');
        // get all country
        $this->client_model->_table_name = "tbl_countries"; //table name
        $this->client_model->_order_by = "id";
        $data['countries'] = $this->client_model->get();

        // get all currencies
        $this->client_model->_table_name = 'tbl_currencies';
        $this->client_model->_order_by = 'name';
        $data['currencies'] = $this->client_model->get();
        // get all language
        $data['languages'] = $this->db->where('active', 1)->order_by('name', 'ASC')->get('tbl_languages')->result();

        $data['subview'] = $this->load->view('admin/client/import_client', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function save_imported()
    {
        $created = can_action('4', 'created');
        if (!empty($created)) {
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
                        $data = $this->client_model->array_from_post(array('customer_group_id', 'vat', 'language', 'currency', 'country'));

                        $data['name'] = trim($sheetData[$x]["A"]);
                        $data['email'] = trim($sheetData[$x]["B"]);
                        $data['short_note'] = trim($sheetData[$x]["C"]);
                        $data['phone'] = trim($sheetData[$x]["D"]);
                        $data['mobile'] = trim($sheetData[$x]["E"]);
                        $data['fax'] = trim($sheetData[$x]["F"]);
                        $data['city'] = trim($sheetData[$x]["G"]);
                        $data['zipcode'] = trim($sheetData[$x]["H"]);
                        $data['address'] = trim($sheetData[$x]["I"]);
                        $data['skype_id'] = trim($sheetData[$x]["J"]);
                        $data['twitter'] = trim($sheetData[$x]["K"]);
                        $data['facebook'] = trim($sheetData[$x]["L"]);
                        $data['linkedin'] = trim($sheetData[$x]["M"]);
                        $data['hosting_company'] = trim($sheetData[$x]["N"]);
                        $data['hostname'] = trim($sheetData[$x]["O"]);
                        $data['username'] = trim($sheetData[$x]["P"]);
                        $data['password'] = trim($sheetData[$x]["Q"]);
                        $data['port'] = trim($sheetData[$x]["R"]);

                        $this->client_model->_table_name = 'tbl_client';
                        $this->client_model->_primary_key = "client_id";
                        $id = $this->client_model->save($data);

                        $action = ('activity_update_company');
                        $activities = array(
                            'user' => $this->session->userdata('user_id'),
                            'module' => 'client',
                            'module_field_id' => $id,
                            'activity' => $action,
                            'icon' => 'fa-user',
                            'value1' => $data['name']
                        );
                        $this->client_model->_table_name = 'tbl_activities';
                        $this->client_model->_primary_key = "activities_id";
                        $this->client_model->save($activities);
                    }
                } else {
                    $type = 'error';
                    $message = "Sorry your uploaded file type not allowed ! please upload XLS/CSV File ";
                }
            } else {
                $type = 'error';
                $message = "You did not Select File! please upload XLS/CSV File ";
            }
        } else {
            $type = 'error';
            $message = "You is no permission to access it ";
        }
        set_message($type, $message);
        redirect('admin/client/manage_client');

    }


    public function save_client($id = NULL)
    {
        $created = can_action('4', 'created');
        $edited = can_action('4', 'edited');
        if (!empty($created) || !empty($edited) && !empty($id)) {
            $data = $this->client_model->array_from_post(array('name', 'email', 'short_note', 'website', 'phone', 'mobile', 'fax', 'address', 'city', 'zipcode', 'currency', 'sms_notification',
                'skype_id', 'linkedin', 'facebook', 'twitter', 'language', 'country', 'vat', 'hosting_company', 'hostname', 'port', 'username', 'latitude', 'longitude', 'customer_group_id'));

            $password = $this->input->post('password', true);
            if (!empty($password)) {
                $data['password'] = encrypt($password);
            }

            if (!empty($_FILES['profile_photo']['name'])) {
                $val = $this->client_model->uploadImage('profile_photo');
                $val == TRUE || redirect('admin/client/manage_client');
                $data['profile_photo'] = $val['path'];
            }

            $this->client_model->_table_name = 'tbl_client';
            $this->client_model->_primary_key = "client_id";
            $return_id = $this->client_model->save($data, $id);
            if (!empty($id)) {
                $id = $id;
                $action = ('activity_added_new_company');
            } else {
                $id = $return_id;
                $action = ('activity_update_company');
            }
            save_custom_field(12, $id);

            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'client',
                'module_field_id' => $id,
                'activity' => $action,
                'icon' => 'fa-user',
                'value1' => $data['name']
            );
            $this->client_model->_table_name = 'tbl_activities';
            $this->client_model->_primary_key = "activities_id";
            $this->client_model->save($activities);
            // messages for user
            $type = "success";
            $message = lang('client_updated');
            set_message($type, $message);
        }
        $save_and_create_contact = $this->input->post('save_and_create_contact', true);
        if (!empty($save_and_create_contact)) {
            redirect('admin/client/client_details/' . $id . '/add_contacts');
        } else {
            redirect('admin/client/manage_client');
        }
    }

    public function see_password($type = null)
    {
        $data['title'] = lang('see_password');
        $data['ids'] = null;
        $data['password'] = null;
        if (!empty($type) && !is_numeric($type)) {
            $ex = explode('_', $type);
            if ($ex[0] == 'c') {
                $data['password'] = get_row('tbl_client', array('client_id' => $ex[1]), 'password');
            } elseif ($ex[0] == 'smtp') {
                $data['password'] = config_item('smtp_pass');
            } elseif ($ex[0] == 'emin') {
                $data['password'] = config_item('config_password');
            } elseif ($ex[0] == 'timap') {
                $data['password'] = get_row('tbl_departments', array('departments_id' => $ex[1]), 'password');
                $data['ids'] = $ex[1];
            } elseif ($ex[0] == 'paypalpassword') {
                $data['password'] = config_item('paypal_api_password');
            }
        }
        $data['subview'] = $this->load->view('admin/settings/see_password', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function customer_group()
    {
        $data['title'] = lang('customer_group');
        $data['subview'] = $this->load->view('admin/client/customer_group', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function update_customer_group($id = null)
    {
        $this->client_model->_table_name = 'tbl_customer_group';
        $this->client_model->_primary_key = 'customer_group_id';

        $cate_data['customer_group'] = $this->input->post('customer_group', TRUE);
        $cate_data['description'] = $this->input->post('description', TRUE);
        $cate_data['type'] = 'client';

        // update root category
        $where = array('type' => 'client', 'customer_group' => $cate_data['customer_group']);
        // duplicate value check in DB
        if (!empty($id)) { // if id exist in db update data
            $customer_group_id = array('customer_group_id !=' => $id);
        } else { // if id is not exist then set id as null
            $customer_group_id = null;
        }
        // check whether this input data already exist or not
        $check_category = $this->client_model->check_update('tbl_customer_group', $where, $customer_group_id);
        if (!empty($check_category)) { // if input data already exist show error alert
            // massage for user
            $type = 'error';
            $msg = "<strong style='color:#000'>" . $cate_data['customer_group'] . '</strong>  ' . lang('already_exist');
        } else { // save and update query
            $id = $this->client_model->save($cate_data, $id);

            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'settings',
                'module_field_id' => $id,
                'activity' => ('customer_group_added'),
                'value1' => $cate_data['customer_group']
            );
            $this->client_model->_table_name = 'tbl_activities';
            $this->client_model->_primary_key = 'activities_id';
            $this->client_model->save($activity);

            // messages for user
            $type = "success";
            $msg = lang('customer_group_added');
        }
        if (!empty($id)) {
            $result = array(
                'id' => $id,
                'group' => $cate_data['customer_group'],
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


    public function client_details($id, $action = null)
    {
        if ($action == 'add_contacts') {
            // get all language
            $data['languages'] = $this->db->where('active', 1)->order_by('name', 'ASC')->get('tbl_languages')->result();
            // get all location
            $this->client_model->_table_name = 'tbl_locales';
            $this->client_model->_order_by = 'name';
            $data['locales'] = $this->client_model->get();
            $data['company'] = $id;
            $user_id = $this->uri->segment(6);
            if (!empty($user_id)) {
                // get all user_info by user id
                $data['account_details'] = $this->client_model->check_by(array('user_id' => $user_id), 'tbl_account_details');

                $data['user_info'] = $this->client_model->check_by(array('user_id' => $user_id), 'tbl_users');
            }

        }

        $data['title'] = "View Client Details"; //Page title
        // get all client details
        $this->client_model->_table_name = "tbl_client"; //table name
        $this->client_model->_order_by = "client_id";
        $data['client_details'] = $this->client_model->get_by(array('client_id' => $id), TRUE);
        if (empty($data['client_details'])) {
            $type = "error";
            $message = "No Record Found";
            set_message($type, $message);
            redirect('admin/client/manage_client');
        }
        // get all invoice by client id
        $this->client_model->_table_name = "tbl_invoices"; //table name
        $this->client_model->_order_by = "client_id";
        $data['client_invoices'] = $this->client_model->get_by(array('client_id' => $id), FALSE);

        // get all estimates by client id
        $this->client_model->_table_name = "tbl_estimates"; //table name
        $this->client_model->_order_by = "client_id";
        $data['client_estimates'] = $this->client_model->get_by(array('client_id' => $id), FALSE);

        // get client contatc by client id
        $data['client_contacts'] = $this->client_model->get_client_contacts($id);

        $data['subview'] = $this->load->view('admin/client/client_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function contactList($id)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_account_details';
            $this->datatables->column_order = array('fullname', 'email', 'phone', 'mobile', 'address', 'city', 'zipcode');
            $this->datatables->column_search = array('fullname', 'email', 'phone', 'mobile', 'address', 'city', 'zipcode');
            $this->datatables->order = array('user_id' => 'desc');
            // get all invoice
            $fetch_data = $this->client_model->get_client_contacts($id);

            $client_details = get_row('tbl_client', array('client_id' => $id));

            $data = array();
            foreach ($fetch_data as $_key => $contact) {
                $action = null;
                $sub_array = array();
                $name = null;
                $name .= '<a class="text-info" href="' . base_url() . 'admin/user/user_details/' . $contact->user_id . '">' . $contact->fullname . '</a>';
                $sub_array[] = $name;

                $sub_array[] = $contact->email;
                $sub_array[] = '<a href="tel:' . $contact->phone . '">' . $contact->phone . '</a>';
                $sub_array[] = '<a href="tel:' . $contact->mobile . '">' . $contact->mobile . '</a>';
                $sub_array[] = '<a href="skype:' . $contact->skype . '?call">' . $contact->skype . '</a>';

                if (!empty($contact->online_time)) {
                    $login_time = time_ago($contact->online_time);
                } else {
                    $login_time = lang('never');
                }
                $sub_array[] = $login_time;

                $action .= '<a href="' . base_url() . 'admin/client/make_primary/' . $contact->user_id . '/' . $id . '" data-toggle="tooltip" class="btn ' . (($client_details->primary_contact == $contact->user_id) ? "btn-success" : "btn-default") . ' btn-xs " title="' . lang('primary_contact') . '"><i class="fa fa-chain"></i> </a>' . ' ';
                $action .= btn_edit('admin/client/client_details/' . $client_details->client_id . '/add_contacts/' . $contact->user_id) . ' ';
                $action .= ajax_anchor(base_url("admin/client/delete_contacts/$client_details->client_id . '/' . $contact->user_id"), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';

                $sub_array[] = $action;
                $data[] = $sub_array;

            }

            render_table($data);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function invoiceList($id)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_invoices';
            $this->datatables->column_order = array('reference_no', 'date_saved', 'due_date');
            $this->datatables->column_search = array('reference_no', 'date_saved', 'due_date');
            $this->datatables->order = array('user_id' => 'desc');
            // get all invoice
            $fetch_data = get_result('tbl_invoices', array('client_id' => $id));

            $data = array();
            foreach ($fetch_data as $_key => $invoice) {
                $action = null;
                $sub_array = array();
                $name = null;
                $name .= '<a class="text-info" href="' . base_url() . 'admin/invoice/manage_invoice/invoice_details/' . $invoice->invoices_id . '">' . $invoice->reference_no . '</a>';
                $sub_array[] = $name;

                $sub_array[] = display_date($invoice->date_saved);
                $sub_array[] = display_date($invoice->due_date);

                $sub_array[] = display_money($this->invoice_model->invoice_payable($invoice->invoices_id), client_currency($id));
                $data[] = $sub_array;

            }
            render_table($data);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function notesList($id)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_notes';
            $this->datatables->column_order = array('notes', 'added_by', 'added_date');
            $this->datatables->column_search = array('notes', 'added_by', 'added_date');
            $this->datatables->order = array('notes_id' => 'desc');
            // get all invoice
            $fetch_data = $this->db->where(array('user_id' => $id, 'is_client' => 'Yes'))->get('tbl_notes')->result();

            $data = array();
            foreach ($fetch_data as $_key => $v_notes) {
                $n_user = $this->db->where('user_id', $v_notes->added_by)->get('tbl_users')->row();
                if (empty($n_user)) {
                    $n_user->fullname = '-';
                    $n_url = '#';
                } else {
                    $n_url = base_url() . 'admin/user/user_details/' . $n_user->user_id;
                }

                $action = null;
                $sub_array = array();
                $name = null;
                $name .= '<a class="text-info" href="' . base_url() . 'admin/client/client_details/' . $id . '/notes/' . $v_notes->notes_id . '">' . $v_notes->notes . '</a>';
                $sub_array[] = $name;

                $sub_array[] = '<a href="' . $n_url . '">' . $n_user->username . '</a>';
                $sub_array[] = strftime(config_item('date_format'), strtotime($v_notes->added_date)) . ' ' . display_time($v_notes->added_date);

                $action .= btn_edit('admin/client/client_details/' . $id . '/notes/' . $v_notes->notes_id) . ' ';
                $action .= ajax_anchor(base_url("admin/client/delete_notes/$v_notes->notes_id . '/' . $id"), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                $sub_array[] = $action;
                $data[] = $sub_array;

            }

            render_table($data);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function paymentsList($id)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_payments';
            $this->datatables->column_order = array('payment_date', 'invoices_id', 'amount');
            $this->datatables->column_search = array('payment_date', 'invoices_id', 'amount');
            $this->datatables->order = array('payments_id' => 'desc');
            // get all invoice
            $fetch_data = $this->db
                ->where('paid_by', $id)
                ->order_by('created_date', 'desc')
                ->get('tbl_payments')
                ->result();;

            $data = array();
            foreach ($fetch_data as $_key => $v_paid) {

                $invoice_info = $this->db->where(array('invoices_id' => $v_paid->invoices_id))->get('tbl_invoices')->row();
                $payment_method = $this->db->where(array('payment_methods_id' => $v_paid->payment_method))->get('tbl_payment_methods')->row();

                if ($v_paid->payment_method == '1') {
                    $label = 'success';
                } elseif ($v_paid->payment_method == '2') {
                    $label = 'danger';
                } else {
                    $label = 'dark';
                }
                $action = null;
                $sub_array = array();
                $name = null;
                $name .= '<a class="text-info" href="' . base_url() . 'admin/invoice/manage_invoice/payments_details/' . $v_paid->payments_id . '">' . display_date($v_paid->payment_date) . '</a>';
                $sub_array[] = $name;

                $sub_array[] = display_date($invoice_info->date_saved);
                $invoice = null;
                $invoice .= '<a class="text-info" href="' . base_url() . 'admin/invoice/manage_invoice/invoice_details/' . $v_paid->invoices_id . '">' . $invoice_info->reference_no . '</a>';
                $sub_array[] = $invoice;

                $sub_array[] = display_money($v_paid->amount, client_currency($id));
                $sub_array[] = '<span class="label label-' . $label . '">' . !empty($payment_method->method_name) ? $payment_method->method_name : '-' . '</span>';

                $action .= btn_edit('admin/invoice/all_payments/' . $v_paid->payments_id) . ' ';
                $action .= btn_view('admin/invoice/manage_invoice/payments_details/' . $v_paid->payments_id) . ' ';
                $action .= ajax_anchor(base_url("admin/invoice/delete/delete_payment/$v_paid->payments_id"), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                $action .= '<a data-toggle="tooltip" data-placement="top"
                                                   href="' . base_url() . 'admin/invoice/send_payment/' . $v_paid->payments_id . '/' . $v_paid->amount . '"
                                                   title="' . lang('send_email') . '"
                                                   class="btn btn-xs btn-success">
                                                    <i class="fa fa-envelope"></i> </a>' . ' ';

                $sub_array[] = $action;
                $data[] = $sub_array;

            }

            render_table($data);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function elfinder_init($client_id)
    {
        $this->load->helper('path');
        $_allowed_files = explode('|', config_item('allowed_files'));
        $config_allowed_files = array();
        if (is_array($_allowed_files)) {
            foreach ($_allowed_files as $v_extension) {
                array_push($config_allowed_files, '.' . $v_extension);
            }
        }
        $allowed_files = array();
        if (is_array($config_allowed_files)) {
            foreach ($config_allowed_files as $extension) {
                $_mime = get_mime_by_extension($extension);
                if ($_mime == 'application/x-zip') {
                    array_push($allowed_files, 'application/zip');
                }
                if ($extension == '.exe') {
                    array_push($allowed_files, 'application/x-executable');
                    array_push($allowed_files, 'application/x-msdownload');
                    array_push($allowed_files, 'application/x-ms-dos-executable');
                }
                array_push($allowed_files, $_mime);
            }
        }
        $client_info = $this->db->where('client_id', $client_id)->get('tbl_client')->row();
        $c_slug = slug_it($client_info->name);
        $path = set_realpath('filemanager/' . $c_slug);
        $root_options = array(
            'driver' => 'LocalFileSystem',
//            'path' => $path,
//            'URL' => site_url('-') . '/' . $c_slug . '/',
            'uploadMaxSize' => config_item('max_file_size') . 'M',
            'accessControl' => 'access',
            'uploadAllow' => $allowed_files,
            'uploadDeny' => [
                'application/x-httpd-php',
                'application/php',
                'application/x-php',
                'text/php',
                'text/x-php',
                'application/x-httpd-php-source',
                'application/perl',
                'application/x-perl',
                'application/x-python',
                'application/python',
                'application/x-bytecode.python',
                'application/x-python-bytecode',
                'application/x-python-code',
                'wwwserver/shellcgi', // CGI
            ],
            'uploadOrder' => array(
                'allow',
                'deny'
            ),
            'attributes' => array(
                array(
                    'pattern' => '/.tmb/',
                    'hidden' => true
                ),
                array(
                    'pattern' => '/.quarantine/',
                    'hidden' => true
                )
            )
        );
        $client_contacts = $this->client_model->get_client_contacts($client_id);
        if (!empty($client_contacts)) {
            foreach ($client_contacts as $contact) {
                $c_slug = slug_it($client_info->name);
                $path = set_realpath('filemanager/' . $c_slug);
                if (!is_dir($path)) {
                    mkdir($path);
                }
                $c_path = set_realpath('filemanager/' . $c_slug . '/' . $contact->media_path_slug);
                if (empty($contact->media_path_slug)) {
                    $this->db->where('user_id', $contact->user_id);
                    $slug = slug_it($contact->username);
                    $this->db->update('tbl_users', array(
                        'media_path_slug' => $slug
                    ));
                    $contact->media_path_slug = $slug;
                    $c_path = set_realpath('filemanager/' . $c_slug . '/' . $contact->media_path_slug);
                }
                if (!is_dir($c_path)) {
                    mkdir($c_path);
                }
                if (!file_exists($c_path . '/index.html')) {
                    fopen($c_path . '/index.html', 'w');
                }
                array_push($root_options['attributes'], array(
                    'pattern' => '/.(' . $contact->media_path_slug . '+)/', // Prevent deleting/renaming folder
                    'read' => true,
                    'write' => true,
                ));
                $root_options['path'] = $path;
                $root_options['URL'] = site_url('filemanager/' . $c_slug . '/' . $user->media_path_slug) . '/';

                $opts = array(
                    'roots' => array(
                        $root_options
                    )
                );

                $this->load->library('elfinder_lib', $opts);
            }
        }
    }

    public function save_contact($id = NULL)
    {
        $data = $this->client_model->array_from_post(array('fullname', 'company', 'phone', 'mobile', 'skype', 'language', 'locale', 'direction'));
        if (!empty($id)) {
            $u_data['email'] = $this->input->post('email', TRUE);
            $u_data['last_ip'] = $this->input->ip_address();
            $this->client_model->_table_name = 'tbl_users';
            $this->client_model->_primary_key = 'user_id';
            $user_id = $this->client_model->save($u_data, $id);
            $data['user_id'] = $user_id;
            $acount_info = $this->client_model->check_by(array('user_id' => $id), 'tbl_account_details');

            $this->client_model->_table_name = 'tbl_account_details';
            $this->client_model->_primary_key = 'account_details_id';
            $return_id = $this->client_model->save($data, $acount_info->account_details_id);

            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'client',
                'module_field_id' => $id,
                'activity' => ('activity_update_contact'),
                'icon' => 'fa-user',
                'value1' => $data['fullname']
            );
            $this->client_model->_table_name = 'tbl_activities';
            $this->client_model->_primary_key = "activities_id";
            $this->client_model->save($activities);
        } else {
            $user_data = $this->client_model->array_from_post(array('email', 'username', 'password'));
            $u_data['last_ip'] = $this->input->ip_address();
            $check_email = $this->client_model->check_by(array('email' => $user_data['email']), 'tbl_users');
            $check_username = $this->client_model->check_by(array('username' => $user_data['username']), 'tbl_users');

            if ($user_data['password'] == $this->input->post('confirm_password', TRUE)) {
                $u_data['password'] = $this->hash($user_data['password']);

                if (!empty($check_username)) {
                    $message['error'][] = lang('this_username_already_exist');
                } else {
                    $u_data['username'] = $user_data['username'];
                }
                if (!empty($check_email)) {
                    $message['error'][] = lang('this_email_already_exist');
                } else {
                    $u_data['email'] = $user_data['email'];
                }
            } else {
                $message['error'][] = lang('password_does_not_macth');
            }

            if (!empty($u_data['password']) && !empty($u_data['username']) && !empty($u_data['email'])) {
                $u_data['role_id'] = $this->input->post('role_id', true);
                $u_data['activated'] = '1';

                $this->client_model->_table_name = 'tbl_users';
                $this->client_model->_primary_key = 'user_id';
                $user_id = $this->client_model->save($u_data, $id);

                $data['user_id'] = $user_id;

                $this->client_model->_table_name = 'tbl_account_details';
                $this->client_model->_primary_key = 'account_details_id';
                $return_id = $this->client_model->save($data, $id);
                // check primary contact
                $primary_contact = $this->client_model->check_by(array('client_id' => $data['company']), 'tbl_client');

                if ($primary_contact->primary_contact == 0) {
                    $c_data['primary_contact'] = $return_id;
                    $this->client_model->_table_name = 'tbl_client';
                    $this->client_model->_primary_key = 'client_id';
                    $this->client_model->save($c_data, $data['company']);
                }
                if ($this->input->post('send_email_password', true) == 'on') {
                    $this->send_confirmation_email($u_data, $user_data['password']); //send thank you email
                }
//                $send_email_password = $this->input->post('send_email_password', true);
//                if (!empty($send_email_password)) {
//
//                    $email_template = $this->client_model->check_by(array('email_group' => 'registration'), 'tbl_email_templates');
//                    $SITE_URL = str_replace("{SITE_URL}", base_url(), $email_template->template_body);
//                    $username = str_replace("{USERNAME}", $u_data['username'], $SITE_URL);
//                    $user_email = str_replace("{EMAIL}", $u_data['email'], $username);
//
//                    $user_password = str_replace("{PASSWORD}", $user_data['password'], $user_email);
//                    $message = str_replace("{SITE_NAME}", config_item('company_name'), $user_password);
//
//                    $params['recipient'] = $u_data['email'];
//                    $params['subject'] = '[ ' . config_item('company_name') . ' ]' . ' ' . $email_template->subject;
//                    $params['message'] = $message;
//                    $params['resourceed_file'] = '';
//
//                    $this->client_model->send_email($params);
//                }
                $activities = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'client',
                    'module_field_id' => $id,
                    'activity' => ('activity_added_new_contact'),
                    'icon' => 'fa-user',
                    'value1' => $data['fullname']
                );
                $this->client_model->_table_name = 'tbl_activities';
                $this->client_model->_primary_key = "activities_id";
                $this->client_model->save($activities);
            }
        }
        if (!empty($user_id)) {
            $this->client_model->_table_name = 'tbl_client_role'; //table name
            $this->client_model->delete_multiple(array('user_id' => $user_id));

            $all_client_menu = $this->db->get('tbl_client_menu')->result();

            foreach ($all_client_menu as $v_client_menu) {
                $client_role_data['menu_id'] = $this->input->post($v_client_menu->label, true);
                if (!empty($client_role_data['menu_id'])) {
                    $client_role_data['user_id'] = $user_id;
                    $this->client_model->_table_name = 'tbl_client_role';
                    $this->client_model->_primary_key = 'client_role_id';
                    $this->client_model->save($client_role_data);
                }
            }
        }
        // messages for user
        $message['success'] = lang('contact_information_successfully_update');
        if (!empty($message['error'])) {
            $this->session->set_userdata($message);
        } else {
            set_message('success', lang('contact_information_successfully_update'));
        }
        if (!empty($data['company'])) {
            redirect('admin/client/client_details/' . $data['company']);
        } else {
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/client/manage_client');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }

    }

    public function update_latitude($id)
    {
        $data = $this->client_model->array_from_post(array('latitude', 'longitude'));
        $this->client_model->_table_name = 'tbl_client';
        $this->client_model->_primary_key = "client_id";
        $this->client_model->save($data, $id);
        redirect('admin/client/client_details/' . $id . '/map');
    }

    function send_confirmation_email($u_data, $password)
    {
        $email_template = email_templates(array('email_group' => 'registration'));
        $SITE_URL = str_replace("{SITE_URL}", base_url(), $email_template->template_body);
        $username = str_replace("{USERNAME}", $u_data['username'], $SITE_URL);
        $user_email = str_replace("{EMAIL}", $u_data['email'], $username);

        $user_password = str_replace("{PASSWORD}", $password, $user_email);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $user_password);

        $params['recipient'] = $u_data['email'];
        $params['subject'] = '[ ' . config_item('company_name') . ' ]' . ' ' . $email_template->subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';

        $this->client_model->send_email($params);
    }

    public function make_primary($user_id, $client_id)
    {
        $user_info = $this->client_model->check_by(array('user_id' => $user_id), 'tbl_account_details');

        $this->db->set('primary_contact', $user_id);
        $this->db->where('client_id', $client_id)->update('tbl_client');
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'client',
            'module_field_id' => $client_id,
            'activity' => ('activity_primary_contact'),
            'icon' => 'fa-user',
            'value1' => $user_info->fullname
        );
        $this->client_model->_table_name = 'tbl_activities';
        $this->client_model->_primary_key = "activities_id";
        $this->client_model->save($activities);

        // messages for user
        $type = "success";
        $message = lang('primary_contact_set');
        set_message($type, $message);
        redirect('admin/client/client_details/' . $client_id);
    }

    public function bulk_delete()
    {
        $selected_id = $this->input->post('ids', true);
        if (!empty($selected_id)) {
            foreach ($selected_id as $client_id) {
                $result[] = $this->delete_client($client_id, true, true);
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

    public function delete_contacts($client_id, $id)
    {
        $sbtn = $this->input->post('submit', true);
        if (!empty($sbtn)) {
            // delete into user table by user id
            $this->client_model->_table_name = 'tbl_client';
            $this->client_model->_order_by = 'primary_contact';
            $primary_contact = $this->client_model->get_by(array('primary_contact' => $id), TRUE);
            if (!empty($primary_contact)) {
                // delete into user table by user id
                $this->client_model->_table_name = 'tbl_account_details';
                $this->client_model->_order_by = 'company';
                $client_info = $this->client_model->get_by(array('company' => $client_id), FALSE);
                $result = count($client_info);
                if ($result != '1') {
                    $data['primary_contact'] = $client_info[1]->account_details_id;
                } else {
                    $data['primary_contact'] = 0;
                }
                $this->client_model->_table_name = 'tbl_client';
                $this->client_model->_primary_key = 'primary_contact';
                $this->client_model->save($data, $client_id);
            }
            $user_info = $this->client_model->check_by(array('user_id' => $id), 'tbl_account_details');
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'client',
                'module_field_id' => $id,
                'activity' => ('activity_deleted_contact'),
                'icon' => 'fa-user',
                'value1' => $user_info->fullname
            );
            $this->client_model->_table_name = 'tbl_account_details';
            $this->client_model->delete_multiple(array('user_id' => $id));

            $this->client_model->_table_name = 'tbl_activities';
            $this->client_model->delete_multiple(array('user' => $id));

            // delete all tbl_quotations by id
            $this->client_model->_table_name = 'tbl_quotations';
            $this->client_model->_order_by = 'user_id';
            $quotations_info = $this->client_model->get_by(array('user_id' => $id), FALSE);

            if (!empty($quotations_info)) {
                foreach ($quotations_info as $v_quotations) {
                    $this->client_model->_table_name = 'tbl_quotation_details';
                    $this->client_model->delete_multiple(array('quotations_id' => $v_quotations->quotations_id));
                }
            }
            $this->client_model->_table_name = 'tbl_quotations';
            $this->client_model->delete_multiple(array('user_id' => $id));

            $this->client_model->_table_name = 'tbl_quotationforms';
            $this->client_model->delete_multiple(array('quotations_created_by_id' => $id));
            $this->client_model->_table_name = 'tbl_users';
            $this->client_model->delete_multiple(array('user_id' => $id));


            $this->client_model->_table_name = 'tbl_inbox';
            $this->client_model->delete_multiple(array('user_id' => $id));

            $this->client_model->_table_name = 'tbl_sent';
            $this->client_model->delete_multiple(array('user_id' => $id));

            $this->client_model->_table_name = 'tbl_draft';
            $this->client_model->delete_multiple(array('user_id' => $id));

            $this->client_model->_table_name = 'tbl_tickets';
            $this->client_model->delete_multiple(array('reporter' => $id));

            $this->client_model->_table_name = 'tbl_tickets_replies';
            $this->client_model->delete_multiple(array('replierid' => $id));

            // messages for user
            $type = "success";
            $message = lang('delete_contact');
            set_message($type, $message);
            redirect('admin/client/client_details/' . $client_id);
        } else {
            $data['title'] = "Delete Client Contact"; //Page title
            $data['user_info'] = $this->db->where('user_id', $id)->get('tbl_account_details')->row();
            $data['client_id'] = $client_id;
            $data['subview'] = $this->load->view('admin/user/delete_user', $data, TRUE);
            $this->load->view('admin/_layout_main', $data); //page load
        }
    }

    public
    function delete_client($client_id, $yes = null, $bulk = null)
    {
        $deleted = can_action('4', 'deleted');
        $sbtn = $this->input->post('submit', true);
        if (!empty($bulk)) {
            $sbtn = true;
        }
        if (!empty($sbtn) && !empty($yes)) {
            if (!empty($deleted)) {
                // delete into user table by user id
                $this->client_model->_table_name = 'tbl_account_details';
                $this->client_model->_order_by = 'company';
                $client_info = $this->client_model->get_by(array('company' => $client_id), FALSE);
                if (!empty($client_info)) {
                    foreach ($client_info as $v_client) {
                        $cwhere = array('user_id' => $v_client->user_id);
                        if ($this->db->table_exists('tbl_private_chat')) {
                            $this->client_model->_table_name = 'tbl_private_chat';
                            $this->client_model->delete_multiple($cwhere);
                        }

                        $this->client_model->_table_name = 'tbl_private_chat_users';
                        $this->client_model->delete_multiple($cwhere);

                        $this->client_model->_table_name = 'tbl_private_chat_messages';
                        $this->client_model->delete_multiple($cwhere);

                        $this->client_model->_table_name = 'tbl_activities';
                        $this->client_model->delete_multiple(array('user' => $v_client->user_id));

                        $this->client_model->_table_name = 'tbl_inbox';
                        $this->client_model->delete_multiple(array('user_id' => $v_client->user_id));

                        $this->client_model->_table_name = 'tbl_sent';
                        $this->client_model->delete_multiple(array('user_id' => $v_client->user_id));

                        $this->client_model->_table_name = 'tbl_draft';
                        $this->client_model->delete_multiple(array('user_id' => $v_client->user_id));


                        $account_info = get_row('tbl_account_details', array('user_id' => $v_client->user_id));
                        if (!empty($account_info)) {

                            $this->client_model->_table_name = 'tbl_client_role';
                            $this->client_model->delete_multiple(array('user_id' => $v_client->user_id));
                            //delete user roll id
                            $this->client_model->_table_name = 'tbl_account_details';
                            $this->client_model->delete_multiple(array('user_id' => $v_client->user_id));//delete user roll id
                        }
                        $this->client_model->_table_name = 'tbl_users';
                        $this->client_model->delete_multiple(array('user_id' => $v_client->user_id));

                    }
                }

                // project
                // delete all leads by id
                $this->client_model->_table_name = 'tbl_project';
                $this->client_model->_order_by = 'client_id';
                $project_info = $this->client_model->get_by(array('client_id' => $client_id), FALSE);
                if (!empty($project_info)) {
                    foreach ($project_info as $v_project) {
                        //delete data into table.
                        $this->client_model->_table_name = "tbl_task_comment"; // table name
                        $this->client_model->delete_multiple(array('project_id' => $v_project->project_id));

                        $this->client_model->_table_name = "tbl_task_attachment"; //table name
                        $this->client_model->_order_by = "task_id";
                        $files_info = $this->client_model->get_by(array('project_id' => $v_project->project_id), FALSE);
                        if (!empty($files_info)) {
                            foreach ($files_info as $v_files) {
                                //save data into table.
                                $this->client_model->_table_name = "tbl_task_uploaded_files"; // table name
                                $this->client_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
                            }
                        }
                        //save data into table.
                        $this->client_model->_table_name = "tbl_task_attachment"; // table name
                        $this->client_model->delete_multiple(array('project_id' => $v_project->project_id));

                        //save data into table.
                        $this->client_model->_table_name = "tbl_milestones"; // table name
                        $this->client_model->delete_multiple(array('project_id' => $v_project->project_id));

                        // tasks
                        $taskss_info = $this->db->where('project_id', $v_project->project_id)->get('tbl_task')->result();
                        if (!empty($taskss_info)) {
                            foreach ($taskss_info as $v_taskss) {

                                $this->client_model->_table_name = "tbl_task_attachment"; //table name
                                $this->client_model->_order_by = "task_id";
                                $files_info = $this->client_model->get_by(array('task_id' => $v_taskss->task_id), FALSE);
                                foreach ($files_info as $v_files) {
                                    $this->client_model->_table_name = "tbl_task_uploaded_files"; //table name
                                    $this->client_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
                                }
                                //delete into table.
                                $this->client_model->_table_name = "tbl_task_attachment"; // table name
                                $this->client_model->delete_multiple(array('task_id' => $v_taskss->task_id));

                                //delete data into table.
                                $this->client_model->_table_name = "tbl_task_comment"; // table name
                                $this->client_model->delete_multiple(array('task_id' => $v_taskss->task_id));

                                $this->client_model->_table_name = "tbl_task"; // table name
                                $this->client_model->_primary_key = "task_id"; // $id
                                $this->client_model->delete($v_taskss->task_id);
                            }

                        }

                        // Bugs
                        $bugs_info = $this->db->where('project_id', $v_project->project_id)->get('tbl_bug')->result();
                        if (!empty($bugs_info)) {
                            foreach ($bugs_info as $v_bugs) {


                                $this->client_model->_table_name = "tbl_task_attachment"; //table name
                                $this->client_model->_order_by = "bug_id";
                                $files_info = $this->client_model->get_by(array('bug_id' => $v_bugs->bug_id), FALSE);
                                foreach ($files_info as $v_files) {
                                    $this->client_model->_table_name = "tbl_task_uploaded_files"; //table name
                                    $this->client_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
                                }
                                //delete into table.
                                $this->client_model->_table_name = "tbl_task_attachment"; // table name
                                $this->client_model->delete_multiple(array('bug_id' => $v_bugs->bug_id));

                                //delete data into table.
                                $this->client_model->_table_name = "tbl_task_comment"; // table name
                                $this->client_model->delete_multiple(array('bug_id' => $v_bugs->bug_id));

                                //delete data into table.
                                $this->client_model->_table_name = "tbl_task"; // table name
                                $this->client_model->delete_multiple(array('bug_id' => $v_bugs->bug_id));

                                $this->client_model->_table_name = "tbl_bug"; // table name
                                $this->client_model->_primary_key = "bug_id"; // $id
                                $this->client_model->delete($v_bugs->bug_id);
                            }

                        }

                        $this->client_model->_table_name = 'tbl_project';
                        $this->client_model->_primary_key = 'project_id';
                        $this->client_model->delete($v_project->project_id);
                    }
                }

                // delete all invoice by id
                $invoice_info = $this->db->where('client_id', $client_id)->get('tbl_invoices')->result();
                if (!empty($invoice_info)) {
                    foreach ($invoice_info as $v_invoice) {
                        // delete all payment info by id
                        $this->client_model->_table_name = 'tbl_payments';
                        $this->client_model->delete_multiple(array('invoices_id' => $v_invoice->invoices_id));
                    }
                }
                $this->client_model->_table_name = 'tbl_invoices';
                $this->client_model->delete_multiple(array('client_id' => $client_id));

                // delete all project by id
                $this->client_model->_table_name = 'tbl_estimates';
                $this->client_model->_order_by = 'client_id';
                $estimates_info = $this->client_model->get_by(array('client_id' => $client_id), FALSE);
                if (!empty($estimates_info)) {
                    foreach ($estimates_info as $v_estimates) {
                        $this->client_model->_table_name = 'tbl_estimate_items';
                        $this->client_model->delete_multiple(array('estimates_id' => $v_estimates->estimates_id));

                    }
                }
                $this->client_model->_table_name = 'tbl_estimates';
                $this->client_model->delete_multiple(array('client_id' => $client_id));
                // delete all tbl_quotations by id
                $this->client_model->_table_name = 'tbl_quotations';
                $this->client_model->_order_by = 'client_id';
                $quotations_info = $this->client_model->get_by(array('client_id' => $client_id), FALSE);

                if (!empty($quotations_info)) {
                    foreach ($quotations_info as $v_quotations) {
                        $this->client_model->_table_name = 'tbl_quotation_details';
                        $this->client_model->delete_multiple(array('quotations_id' => $v_quotations->quotations_id));
                    }
                }
                $this->client_model->_table_name = 'tbl_quotations';
                $this->client_model->delete_multiple(array('client_id' => $client_id));

                $this->client_model->_table_name = 'tbl_transactions';
                $this->client_model->delete_multiple(array('paid_by' => $client_id));

                $this->client_model->_table_name = 'tbl_reminders';
                $this->client_model->delete_multiple(array('module' => 'client', 'module_id' => $client_id));

                $user_info = $this->client_model->check_by(array('client_id' => $client_id), 'tbl_client');
                $activities = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'client',
                    'module_field_id' => $this->session->userdata('user_id'),
                    'activity' => ('activity_deleted_client'),
                    'icon' => 'fa-user',
                    'value1' => $user_info->name
                );
                $this->client_model->_table_name = 'tbl_activities';
                $this->client_model->_primary_key = "activities_id";
                $this->client_model->save($activities);

                // deletre into tbl_account details by user id
                $this->client_model->_table_name = 'tbl_client';
                $this->client_model->_primary_key = 'client_id';
                $this->client_model->delete($client_id);
                // messages for user
                $type = "success";
                $message = lang('delete_client');
            } else {
                $type = "error";
                $message = client_name($client_id) . ' ' . lang('no_permission');
            }
            if (!empty($bulk)) {
                return (array("status" => $type, 'message' => $message));
            } else {
                set_message($type, $message);
                redirect('admin/client/manage_client');
            }
        } else {
            $data['title'] = "Delete Client "; //Page title
            $data['client_info'] = $this->db->where('client_id', $client_id)->get('tbl_client')->row();
            $data['subview'] = $this->load->view('admin/client/delete_client', $data, TRUE);
            $this->load->view('admin/_layout_main', $data); //page load
        }
    }

    function hash($string)
    {
        return hash('sha512', $string . config_item('encryption_key'));
    }

    public function new_notes($id = NULL)
    {
        $data['title'] = lang('give_award');
        $notes = $this->input->post('notes', true);
        $n_data['user_id'] = $this->input->post('client_id', true);
        if (!empty($notes)) {
            $n_data['notes'] = $notes;
            $n_data['is_client'] = 'Yes';
            $n_data['added_by'] = $this->session->userdata('user_id');
            // deletre into tbl_account details by user id
            $this->client_model->_table_name = 'tbl_notes';
            $this->client_model->_primary_key = 'notes_id';
            $this->client_model->save($n_data, $id);
        }
        redirect('admin/client/client_details/' . $n_data['user_id'] . '/notes');
    }

    public function delete_notes($id, $client_id)
    {
        $notes_info = $this->db->where('notes_id', $id)->get('tbl_notes')->row();
        if (empty($notes_info)) {
            $type = "error";
            $message = "No Record Found";
            set_message($type, $message);
            redirect('admin/client/client_details/' . $client_id . '/notes');
        }
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'client',
            'module_field_id' => $this->session->userdata('user_id'),
            'activity' => ('activity_deleted_notes'),
            'icon' => 'fa-user',
            'value1' => $notes_info->notes
        );
        $this->client_model->_table_name = 'tbl_activities';
        $this->client_model->_primary_key = "activities_id";
        $this->client_model->save($activities);

        $this->client_model->_table_name = 'tbl_notes';
        $this->client_model->_primary_key = 'notes_id';
        $this->client_model->delete($id);
        redirect('admin/client/client_details/' . $client_id . '/notes');
    }


    public function new_client()
    {
        $data['title'] = lang('new_client');
        $data['subview'] = $this->load->view('admin/client/new_client', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function update_client()
    {
        $created = can_action('4', 'created');
        if (!empty($created)) {
            $data = $this->client_model->array_from_post(array('name', 'email', 'short_note', 'website', 'phone', 'mobile', 'fax', 'address', 'city', 'zipcode', 'currency',
                'skype_id', 'linkedin', 'facebook', 'twitter', 'language', 'country', 'vat', 'hosting_company', 'hostname', 'port', 'username', 'latitude', 'longitude', 'customer_group_id'));

            $password = $this->input->post('password', true);
            if (!empty($password)) {
                $data['password'] = encrypt($password);
            }

            if (!empty($_FILES['profile_photo']['name'])) {
                $val = $this->client_model->uploadImage('profile_photo');
                $val == TRUE || redirect('admin/client/manage_client');
                $data['profile_photo'] = $val['path'];
            }

            $this->client_model->_table_name = 'tbl_client';
            $this->client_model->_primary_key = "client_id";
            $id = $this->client_model->save($data);
            $action = ('activity_update_company');
            save_custom_field(12, $id);

            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'client',
                'module_field_id' => $id,
                'activity' => $action,
                'icon' => 'fa-user',
                'value1' => $data['name']
            );
            $this->client_model->_table_name = 'tbl_activities';
            $this->client_model->_primary_key = "activities_id";
            $this->client_model->save($activities);
//            messages for user
            $type = "success";
            $message = lang('client_updated');
            set_message($type, $message);
        }
        if (!empty($id)) {
            $result = array(
                'id' => $id,
                'name' => $data['name'],
                'status' => $type,
                'message' => $message,
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
