<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Invoice extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('invoice_model');
        $this->load->library('gst');
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

    public function manage_invoice($action = NULL, $id = NULL, $item_id = NULL)
    {
        $data['page'] = lang('sales');
        if ($action == 'all_payments') {
            $data['sub_active'] = lang('payments_received');
        } else {
            $data['sub_active'] = lang('invoice');
        }
        if (!empty($item_id)) {
            $can_edit = $this->invoice_model->can_action('tbl_invoices', 'edit', array('invoices_id' => $id));

            if (!empty($can_edit)) {
                $data['item_info'] = $this->invoice_model->check_by(array('items_id' => $item_id), 'tbl_items');
            }
        }
        if (!empty($id) && $action != 'payments_details') {
            // get all invoice info by id
            $can_edit = $this->invoice_model->can_action('tbl_invoices', 'edit', array('invoices_id' => $id));

            if (!empty($can_edit)) {
                $data['invoice_info'] = $this->invoice_model->check_by(array('invoices_id' => $id), 'tbl_invoices');
                if (!empty($data['invoice_info']->client_id)) {
                    $data['invoices_to_merge'] = $this->invoice_model->check_for_merge_invoice($data['invoice_info']->client_id, $id);
                }
            }
        }
        if ($action == 'create_invoice') {
            $data['active'] = 2;
        } else {
            $data['active'] = 1;

        }
        // get all client
        $this->invoice_model->_table_name = 'tbl_client';
        $this->invoice_model->_order_by = 'client_id';
        $data['all_client'] = $this->invoice_model->get();

        // get permission user
        $data['permission_user'] = $this->invoice_model->all_permission_user('13');
        $type = $this->uri->segment(5);
        if (empty($type)) {
            $type = '_' . date('Y');
        }
        $filterBy = null;
        if (!empty($type) && !is_numeric($type)) {
            $ex = explode('_', $type);
            if ($ex[0] != 'c') {
                $filterBy = $type;
            }
        }
        // get all invoice
        $data['all_invoices_info'] = $this->invoice_model->get_invoices($filterBy);

        if ($action == 'invoice_details') {
            $data['title'] = "Invoice Details"; //Page title
            $data['invoice_info'] = $this->invoice_model->check_by(array('invoices_id' => $id), 'tbl_invoices');
            if (!empty($data['invoice_info'])) {
                $data['client_info'] = $this->invoice_model->check_by(array('client_id' => $data['invoice_info']->client_id), 'tbl_client');
                $payment_status = $this->invoice_model->get_payment_status($id);
                if ($payment_status != lang('cancelled') && $payment_status != lang('fully_paid')) {
                    $this->load->model('credit_note_model');
                    $data['total_available_credit'] = $this->credit_note_model->get_available_credit_by_client($data['invoice_info']->client_id);
                }
                $lang = $this->invoice_model->all_files();
                foreach ($lang as $file => $altpath) {
                    $shortfile = str_replace("_lang.php", "", $file);
                    //CI will record your lang file is loaded, unset it and then you will able to load another
                    //unset the lang file to allow the loading of another file
                    if (isset($this->lang->is_loaded)) {
                        $loaded = sizeof($this->lang->is_loaded);
                        if ($loaded < 3) {
                            for ($i = 3; $i <= $loaded; $i++) {
                                unset($this->lang->is_loaded[$i]);
                            }
                        } else {
                            for ($i = 0; $i <= $loaded; $i++) {
                                unset($this->lang->is_loaded[$i]);
                            }
                        }
                    }
                    if (!empty($data['client_info']->language)) {
                        $language = $data['client_info']->language;
                    } else {
                        $language = 'english';
                    }
                    $data['language_info'] = $this->lang->load($shortfile, $language, TRUE, TRUE, $altpath);
                }
                $subview = 'invoice_details';
                // get payment info by id
                $this->invoice_model->_table_name = 'tbl_payments';
                $this->invoice_model->_order_by = 'payments_id';
                $data['all_payments_history'] = $this->invoice_model->get_by(array('invoices_id' => $id), FALSE);
            } else {
                set_message('error', 'No data Found');
                redirect('admin/invoice/manage_invoice');
            }
        } elseif ($action == 'payment' || $action == 'payment_history') {
            $data['title'] = lang($action); //Page title
            // get payment info by id
            $this->invoice_model->_table_name = 'tbl_payments';
            $this->invoice_model->_order_by = 'payments_id';
            $data['all_payments_history'] = $this->invoice_model->get_by(array('invoices_id' => $id), FALSE);

            $subview = $action;
        } elseif ($action == 'payments_details') {
            $data['title'] = "Payments Details"; //Page title
            $subview = 'payments_details';
            // get payment info by id
            $this->invoice_model->_table_name = 'tbl_payments';
            $this->invoice_model->_order_by = 'payments_id';
            $data['payments_info'] = $this->invoice_model->get_by(array('payments_id' => $id), TRUE);

        } elseif ($action == 'invoice_history') {
            $data['invoice_info'] = $this->invoice_model->check_by(array('invoices_id' => $id), 'tbl_invoices');
            $data['title'] = "Invoice History"; //Page title
            $subview = 'invoice_history';
        } elseif ($action == 'email_invoice') {
            $data['invoice_info'] = $this->invoice_model->check_by(array('invoices_id' => $id), 'tbl_invoices');
            $data['title'] = "Email Invoice"; //Page title
            $subview = 'email_invoice';
            $data['editor'] = $this->data;
        } elseif ($action == 'send_reminder') {
            $data['invoice_info'] = $this->invoice_model->check_by(array('invoices_id' => $id), 'tbl_invoices');
            $data['title'] = "Send Remainder"; //Page title
            $subview = 'send_reminder';
            $data['editor'] = $this->data;
        } elseif ($action == 'send_overdue') {
            $data['invoice_info'] = $this->invoice_model->check_by(array('invoices_id' => $id), 'tbl_invoices');
            $data['title'] = lang('send_invoice_overdue'); //Page title
            $subview = 'send_overdue';
            $data['editor'] = $this->data;
        } elseif ($action == 'make_payment') {
            $data['all_invoices'] = $this->invoice_model->get_permission('tbl_invoices');
            $data['title'] = lang('make_payment'); //Page title
            $subview = 'make_payment';
        } else {
            $data['title'] = "Manage Invoice"; //Page title
            $subview = 'manage_invoice';
        }
        $data['subview'] = $this->load->view('admin/invoice/' . $subview, $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public
    function invoices_credit($invoices_id)
    {
        $data['invoice_info'] = get_row('tbl_invoices', array('invoices_id' => $invoices_id));
        $payment_status = $this->invoice_model->get_payment_status($invoices_id);
        $this->load->model('credit_note_model');
        $total_available_credit = $this->credit_note_model->get_available_credit_by_client($data['invoice_info']->client_id);
        if ($payment_status != lang('cancelled') && $payment_status != lang('fully_paid') && !empty($total_available_credit)) {
            $data['all_open_credit'] = get_result('tbl_credit_note', array('status' => 'open', 'client_id' => $data['invoice_info']->client_id));
            $data['subview'] = $this->load->view('admin/credit_note/invoices_to_credits', $data, FALSE);
            $this->load->view('admin/_layout_modal', $data);
        } else {
            $type = "error";
            $message = "No Record Found";
            set_message($type, $message);
            redirect('admin/credit_note');
        }
    }


    public
    function apply_invoices_credit($invoices_id)
    {
        $invoice_amount = $this->input->post('amount', true);
        $added_into_payment = $this->input->post('added_into_payment', true);
        if ($invoice_amount) {
            foreach ($invoice_amount as $credit_note_id => $amount) {
                if (!empty($amount)) {
                    $this->load->model('credit_note_model');
                    $credit_remaining = $this->credit_note_model->credit_note_calculation('credit_remaining', $credit_note_id);
                    $credit_info = $this->invoice_model->check_by(array('credit_note_id' => $credit_note_id), 'tbl_credit_note');
                    if ($amount > $credit_remaining) {
                        // messages for user
                        $error[] = lang('overpaid_amount') . ' the ' . $credit_info->reference_no;
                    } else {
                        $this->apply_credits($invoices_id, ['amount' => $amount, 'credit_note_id' => $credit_note_id, 'added_into_payment' => $added_into_payment]);
                    }
                }
            }
        }
        if (!empty($error)) {
            foreach ($error as $show) {
                set_message('error', $show);
            }
        }
        set_message('success', lang('credit_applied_to_invoices'));
        redirect('admin/invoice/manage_invoice/invoice_details/' . $invoices_id);
    }

    public function apply_credits($invoices_id, $input_post)
    {
        $data = array(
            'invoices_id' => $invoices_id,
            'credit_note_id' => $input_post['credit_note_id'],
            'user_id' => my_id(),
            'date' => date('Y-m-d'),
            'date_applied' => date('Y-m-d H:i'),
            'amount' => $input_post['amount'],
        );
        $this->invoice_model->_table_name = 'tbl_credit_used';
        $this->invoice_model->_primary_key = 'credit_used_id';
        $credit_used_id = $this->invoice_model->save($data);
        if (!empty($credit_used_id)) {
            if ($input_post['added_into_payment'] == 'on') {
                $input_post['invoices_id'] = $invoices_id;
                $input_post['credit_used_id'] = $credit_used_id;
                $this->added_into_payment($input_post);
            }
        }

        return true;
    }

    private function added_into_payment($input_post)
    {
        $this->load->model('credit_note_model');
        $this->load->helper('string_helper');
        $invoices_id = $input_post['invoices_id'];
        $paid_amount = $input_post['amount'];
        $due = $this->invoice_model->calculate_to('invoice_due', $invoices_id);
        $credit_notes = $this->db->where('credit_note_id', $input_post['credit_note_id'])->get('tbl_credit_note')->row();
        if ($paid_amount != 0) {
            $trans_id = random_string('nozero', 6);
            $inv_info = $this->invoice_model->check_by(array('invoices_id' => $invoices_id), 'tbl_invoices');
            $data = array(
                'invoices_id' => $invoices_id,
                'paid_by' => $inv_info->client_id,
                'payment_method' => config_item('default_payment_method'),
                'currency' => client_currency($inv_info->client_id),
                'amount' => $paid_amount,
                'payment_date' => date('Y-m-d'),
                'trans_id' => $trans_id,
                'notes' => 'This Payment from Credit notes <a href="' . base_url('admin/credit_note/index/credit_note_details/' . $input_post['credit_note_id']) . '">' . $credit_notes->reference_no . '</a>',
                'month_paid' => date("m"),
                'year_paid' => date("Y"),
            );
            $this->invoice_model->_table_name = 'tbl_payments';
            $this->invoice_model->_primary_key = 'payments_id';
            $payments_id = $this->invoice_model->save($data);

            $this->invoice_model->_table_name = 'tbl_credit_used';
            $this->invoice_model->_primary_key = 'credit_used_id';
            $cu_data['payments_id'] = $payments_id;
            $this->invoice_model->save($cu_data, $input_post['credit_used_id']);


            if ($paid_amount < $due) {
                $status = 'partially_paid';
            }
            if ($paid_amount == $due) {
                $status = 'Paid';
            }
            $invoice_data['status'] = $status;
            update('tbl_invoices', array('invoices_id' => $invoices_id), $invoice_data);

            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'invoice',
                'module_field_id' => $invoices_id,
                'activity' => ('activity_new_payment'),
                'icon' => 'fa-shopping-cart',
                'link' => 'admin/invoice/manage_invoice/invoice_details/' . $invoices_id,
                'value1' => display_money($paid_amount, client_currency($inv_info->client_id)),
                'value2' => $inv_info->reference_no,
            );
            $this->invoice_model->_table_name = 'tbl_activities';
            $this->invoice_model->_primary_key = 'activities_id';
            $this->invoice_model->save($activity);

            if (!empty($inv_info->user_id)) {
                $notifiedUsers = array($inv_info->user_id);
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'description' => 'not_new_invoice_payment',
                            'icon' => 'shopping-cart',
                            'link' => 'admin/invoice/manage_invoice/invoice_details/' . $invoices_id,
                            'value' => lang('invoice') . ' ' . $inv_info->reference_no . ' ' . lang('amount') . display_money($paid_amount, $currency->symbol),
                        ));
                    }
                }
                show_notification($notifiedUsers);
            }
            if ($this->input->post('save_into_account') == 'on') {
                $account_id = config_item('default_account');
                if (!empty($account_id)) {
                    $reference = lang('invoice') . ' ' . lang('reference_no') . ": <a href='" . base_url('admin/invoice/manage_invoice/invoice_details/' . $inv_info->invoices_id) . "' >" . $inv_info->reference_no . "</a> and " . lang('trans_id') . ": <a href='" . base_url('admin/invoice/manage_invoice/payments_details/' . $payments_id) . "'>" . $this->input->post('trans_id', true) . "</a>";
                    // save into tbl_transaction
                    $tr_data = array(
                        'name' => lang('invoice_payment', lang('trans_id') . '# ' . $trans_id),
                        'type' => 'Income',
                        'amount' => $paid_amount,
                        'credit' => $paid_amount,
                        'date' => date('Y-m-d'),
                        'paid_by' => $inv_info->client_id,
                        'payment_methods_id' => config_item('default_payment_method'),
                        'reference' => $trans_id,
                        'notes' => lang('this_deposit_from_invoice_payment', $reference) . ' ' . 'from credit notes',
                        'permission' => 'all',
                    );

                    $account_info = $this->invoice_model->check_by(array('account_id' => $account_id), 'tbl_accounts');
                    if (!empty($account_info)) {
                        $ac_data['balance'] = $account_info->balance + $tr_data['amount'];
                        $this->invoice_model->_table_name = "tbl_accounts"; //table name
                        $this->invoice_model->_primary_key = "account_id";
                        $this->invoice_model->save($ac_data, $account_info->account_id);

                        $aaccount_info = $this->invoice_model->check_by(array('account_id' => $account_id), 'tbl_accounts');

                        $tr_data['total_balance'] = $aaccount_info->balance;
                        $tr_data['account_id'] = $account_id;

                        // save into tbl_transaction
                        $this->invoice_model->_table_name = "tbl_transactions"; //table name
                        $this->invoice_model->_primary_key = "transactions_id";
                        $return_id = $this->invoice_model->save($tr_data);

                        $deduct_account['account_id'] = $account_id;
                        $this->invoice_model->_table_name = 'tbl_payments';
                        $this->invoice_model->_primary_key = 'payments_id';
                        $this->invoice_model->save($deduct_account, $payments_id);

                        // save into activities
                        $activities = array(
                            'user' => $this->session->userdata('user_id'),
                            'module' => 'transactions',
                            'module_field_id' => $return_id,
                            'activity' => 'activity_new_deposit',
                            'icon' => 'fa-building-o',
                            'link' => 'admin/transactions/view_details/' . $return_id,
                            'value1' => $account_info->account_name,
                            'value2' => $paid_amount,
                        );
                        // Update into tbl_project
                        $this->invoice_model->_table_name = "tbl_activities"; //table name
                        $this->invoice_model->_primary_key = "activities_id";
                        $this->invoice_model->save($activities);

                    }
                }
                if ($this->input->post('send_thank_you') == 'on') {
                    $this->send_payment_email($invoices_id, $paid_amount); //send thank you email
                }

                if ($this->input->post('send_sms') == 'on') {
                    $this->send_payment_sms($invoices_id, $payments_id); //send thank you email
                }
            }
        }

    }

    public
    function applied_credits($invoices_id)
    {
        $data['title'] = lang('applied_credits');
        $data['all_credit_used'] = get_result('tbl_credit_used', array('invoices_id' => $invoices_id));
        $data['subview'] = $this->load->view('admin/invoice/applied_credits', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }


    public function invoiceList($filterBy = null, $search_by = null)
    {
        if ($this->input->is_ajax_request()) {
            $where = array();
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_invoices';
            $this->datatables->join_table = array('tbl_client');
            $this->datatables->join_where = array('tbl_invoices.client_id=tbl_client.client_id');
            $this->datatables->column_order = array('reference_no', 'tbl_client.name', 'invoice_date', 'due_date', 'status');
            $this->datatables->column_search = array('reference_no', 'tbl_client.name', 'invoice_date', 'due_date', 'status');
            $this->datatables->order = array('invoices_id' => 'desc');

            if (empty($filterBy)) {
                $filterBy = '_' . date('Y');
            }
            if (!empty($filterBy) && !is_numeric($filterBy)) {
                $ex = explode('_', $filterBy);
                if ($ex[0] != 'c') {
                    $filterBy = $filterBy;
                }
            }
            if (!empty($search_by)) {
                if ($search_by == 'by_project') {
                    $where = array('project_id' => $filterBy);
                }
                if ($search_by == 'by_agent') {
                    $where = array('user_id' => $filterBy);
                }
                if ($search_by == 'by_client') {
                    $where = array('tbl_invoices.client_id' => $filterBy);
                }
                if ($search_by == 'by_client_draft') {
                    $where = array('tbl_invoices.client_id' => $filterBy, 'status !=' => 'draft');
                }
                if ($filterBy == 'by_client_recurring') {
                    $where = array('tbl_invoices.client_id' => $filterBy, 'recurring' => 'Yes');
                }
            } else {
                if ($filterBy == 'recurring') {
                    $where = array('recurring' => 'Yes');
                }
                if ($filterBy == 'paid') {
                    $where = array('status' => 'Paid');
                } else if ($filterBy == 'not_paid') {
                    $where = array('status' => 'Unpaid');
                } else if ($filterBy == 'draft') {
                    $where = array('status' => 'draft');
                } else if ($filterBy == 'partially_paid') {
                    $where = array('status' => 'partially_paid');
                } else if ($filterBy == 'cancelled') {
                    $where = array('status' => 'Cancelled');
                } else if ($filterBy == 'overdue') {
                    $where = array('UNIX_TIMESTAMP(due_date) <' => strtotime(date('Y-m-d')), 'status !=' => 'Paid');
                } else if ($filterBy == 'last_month' || $filterBy == 'this_months') {
                    if ($filterBy == 'last_month') {
                        $month = date('Y-m', strtotime('-1 months'));
                    } else {
                        $month = date('Y-m');
                    }
                    $where = array('invoice_month' => $month);
                } else if (strstr($filterBy, '_')) {
                    $year = str_replace('_', '', $filterBy);
                    $where = array('invoice_year' => $year);
                }
            }
            // get all invoice
            $fetch_data = $this->datatables->get_invoices($filterBy, $search_by);

            $data = array();

            $edited = can_action('13', 'edited');
            $deleted = can_action('13', 'deleted');
            foreach ($fetch_data as $_key => $v_invoices) {
                if (!empty($v_invoices)) {
                    $action = null;
                    $can_edit = $this->invoice_model->can_action('tbl_invoices', 'edit', array('invoices_id' => $v_invoices->invoices_id));
                    $can_delete = $this->invoice_model->can_action('tbl_invoices', 'delete', array('invoices_id' => $v_invoices->invoices_id));
                    if ($this->invoice_model->get_payment_status($v_invoices->invoices_id) == lang('fully_paid')) {
                        $invoice_status = lang('fully_paid');
                        $label = "success";
                    } elseif ($this->invoice_model->get_payment_status($v_invoices->invoices_id) == lang('draft')) {
                        $invoice_status = lang('draft');
                        $label = "default";
                    } elseif ($this->invoice_model->get_payment_status($v_invoices->invoices_id) == lang('partially_paid')) {
                        $invoice_status = lang('partially_paid');
                        $label = "warning";
                    } elseif ($v_invoices->emailed == 'Yes') {
                        $invoice_status = lang('sent');
                        $label = "info";
                    } else {
                        $invoice_status = $this->invoice_model->get_payment_status($v_invoices->invoices_id);
                        $label = "danger";
                    }

                    $sub_array = array();
                    $name = null;
                    $name .= '<a class="text-info" href="' . base_url() . 'admin/invoice/manage_invoice/invoice_details/' . $v_invoices->invoices_id . '">' . $v_invoices->reference_no . '</a>';
                    $sub_array[] = $name;
                    $sub_array[] = strftime(config_item('date_format'), strtotime($v_invoices->invoice_date));
                    $payment_status = $this->invoice_model->get_payment_status($v_invoices->invoices_id);
                    $overdue = null;
                    if (strtotime($v_invoices->due_date) < strtotime(date('Y-m-d')) && $payment_status != lang('fully_paid')) {
                        $overdue .= '<span class="label label-danger ">' . lang("overdue") . '</span>';
                    }
                    $sub_array[] = strftime(config_item('date_format'), strtotime($v_invoices->due_date)) . ' ' . $overdue;
                    $sub_array[] = client_name($v_invoices->client_id);
                    $sub_array[] = display_money($this->invoice_model->calculate_to('invoice_due', $v_invoices->invoices_id), client_currency($v_invoices->client_id));
                    $recurring = null;
                    if ($v_invoices->recurring == 'Yes') {
                        $recurring = '<span data-toggle="tooltip" data-placement="top"
                                                              title="' . lang("recurring") . '"
                                                              class="label label-primary"><i
                                                                class="fa fa-retweet"></i></span>';
                    }
                    $sub_array[] = "<span class='label label-" . $label . "'>" . $invoice_status . "</span>" . ' ' . $recurring;;

                    $custom_form_table = custom_form_table(9, $v_invoices->invoices_id);

                    if (!empty($custom_form_table)) {
                        foreach ($custom_form_table as $c_label => $v_fields) {
                            $sub_array[] = $v_fields;
                        }
                    }
                    if (!empty($can_edit) && !empty($edited)) {
                        $action .= '<a data-toggle="modal" data-target="#myModal"
                                                               title="' . lang('clone') . ' ' . lang('invoice') . '"
                                                               href="' . base_url() . 'admin/invoice/clone_invoice/' . $v_invoices->invoices_id . '"
                                                               class="btn btn-xs btn-purple">
                                                                <i class="fa fa-copy"></i></a>' . ' ';
                        $action .= btn_edit('admin/invoice/manage_invoice/create_invoice/' . $v_invoices->invoices_id) . ' ';
                    }
                    if (!empty($can_delete) && !empty($deleted)) {
                        $action .= ajax_anchor(base_url("admin/invoice/delete/delete_invoice/$v_invoices->invoices_id"), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                    }
                    $change_status = null;
                    if (!empty($can_edit) && !empty($edited)) {
                        $ch_url = base_url() . 'admin/invoice/';
                        $change_status .= '<div class="btn-group">
        <button class="btn btn-xs btn-default dropdown-toggle"
                data-toggle="dropdown">
                    ' . lang('change') . '
            <span class="caret"></span></button>
        <ul class="dropdown-menu animated zoomIn">';
                        $change_status .= '<li><a href="' . $ch_url . 'manage_invoice/invoice_details/' . $v_invoices->invoices_id . '">' . lang('preview_invoice') . '</a></li>';
                        $change_status .= '<li><a href="' . $ch_url . 'manage_invoice/payment/' . $v_invoices->invoices_id . '">' . lang('pay_invoice') . '</a></li>';
                        $change_status .= '<li><a href="' . $ch_url . 'manage_invoice/email_invoice/' . $v_invoices->invoices_id . '">' . lang('email_invoice') . '</a></li>';
                        $change_status .= '<li><a href="' . $ch_url . 'manage_invoice/send_reminder/' . $v_invoices->invoices_id . '">' . lang('send_reminder') . '</a></li>';
                        $change_status .= '<li><a href="' . $ch_url . 'manage_invoice/send_overdue/' . $v_invoices->invoices_id . '">' . lang('send_invoice_overdue') . '</a></li>';
                        $change_status .= '<li><a href="' . $ch_url . 'manage_invoice/invoice_history/' . $v_invoices->invoices_id . '">' . lang('invoice_history') . '</a></li>';
                        $change_status .= '<li><a href="' . $ch_url . 'pdf_invoice/' . $v_invoices->invoices_id . '">' . lang('pdf') . '</a></li>';
                        $change_status .= '</ul></div>';
                        $action .= $change_status;
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

    public function recurringinvoiceList()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_invoices';
            $this->datatables->join_table = array('tbl_client');
            $this->datatables->join_where = array('tbl_invoices.client_id=tbl_client.client_id');
            $this->datatables->column_order = array('reference_no', 'tbl_client.name', 'invoice_date', 'due_date', 'status');
            $this->datatables->column_search = array('reference_no', 'tbl_client.name', 'invoice_date', 'due_date', 'status');
            $this->datatables->order = array('invoices_id' => 'desc');

            // get all invoice
            $fetch_data = $this->datatables->get_datatable_permission(array('recurring' => 'Yes'));

            $data = array();

            $edited = can_action('13', 'edited');
            $deleted = can_action('13', 'deleted');
            foreach ($fetch_data as $_key => $v_invoices) {
                if (!empty($v_invoices)) {
                    $action = null;

                    $can_edit = $this->invoice_model->can_action('tbl_invoices', 'edit', array('invoices_id' => $v_invoices->invoices_id));
                    $can_delete = $this->invoice_model->can_action('tbl_invoices', 'delete', array('invoices_id' => $v_invoices->invoices_id));
                    if ($this->invoice_model->get_payment_status($v_invoices->invoices_id) == lang('fully_paid')) {
                        $invoice_status = lang('fully_paid');
                        $label = "success";
                    } elseif ($this->invoice_model->get_payment_status($v_invoices->invoices_id) == lang('draft')) {
                        $invoice_status = lang('draft');
                        $label = "default";
                    } elseif ($this->invoice_model->get_payment_status($v_invoices->invoices_id) == lang('partially_paid')) {
                        $invoice_status = lang('partially_paid');
                        $label = "warning";
                    } elseif ($v_invoices->emailed == 'Yes') {
                        $invoice_status = lang('sent');
                        $label = "info";
                    } else {
                        $invoice_status = $this->invoice_model->get_payment_status($v_invoices->invoices_id);
                        $label = "danger";
                    }

                    $sub_array = array();
                    $name = null;
                    $name .= '<a class="text-info" href="' . base_url() . 'admin/invoice/manage_invoice/invoice_details/' . $v_invoices->invoices_id . '">' . $v_invoices->reference_no . '</a>';

                    $sub_array[] = $name;
                    $sub_array[] = strftime(config_item('date_format'), strtotime($v_invoices->invoice_date));
                    $payment_status = $this->invoice_model->get_payment_status($v_invoices->invoices_id);
                    $overdue = null;
                    if (strtotime($v_invoices->due_date) < strtotime(date('Y-m-d')) && $payment_status != lang('fully_paid')) {
                        $overdue .= '<span class="label label-danger ">' . lang("overdue") . '</span>';
                    }
                    $sub_array[] = strftime(config_item('date_format'), strtotime($v_invoices->due_date)) . ' ' . $overdue;
                    $sub_array[] = client_name($v_invoices->client_id);
                    $sub_array[] = display_money($this->invoice_model->calculate_to('invoice_due', $v_invoices->invoices_id), client_currency($v_invoices->client_id));
                    $recurring = null;
                    if ($v_invoices->recurring == 'Yes') {
                        $recurring = '<span data-toggle="tooltip" data-placement="top"
                                                              title="' . lang("recurring") . '"
                                                              class="label label-primary"><i
                                                                class="fa fa-retweet"></i></span>';
                    }
                    $sub_array[] = "<span class='label label-" . $label . "'>" . $invoice_status . "</span>" . ' ' . $recurring;;

                    $custom_form_table = custom_form_table(9, $v_invoices->invoices_id);

                    if (!empty($custom_form_table)) {
                        foreach ($custom_form_table as $c_label => $v_fields) {
                            $sub_array[] = $v_fields;
                        }
                    }
                    if (!empty($can_edit) && !empty($edited)) {
                        $action .= '<a data-toggle="modal" data-target="#myModal"
                                                               title="' . lang('clone') . ' ' . lang('invoice') . '"
                                                               href="' . base_url() . 'admin/invoice/clone_invoice/' . $v_invoices->invoices_id . '"
                                                               class="btn btn-xs btn-purple">
                                                                <i class="fa fa-copy"></i></a>' . ' ';
                        $action .= btn_edit('admin/invoice/manage_invoice/create_invoice/' . $v_invoices->invoices_id) . ' ';
                    }
                    if (!empty($can_delete) && !empty($deleted)) {
                        $action .= ajax_anchor(base_url("admin/invoice/delete/delete_invoice/$v_invoices->invoices_id"), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                    }
                    $change_status = null;
                    if (!empty($can_edit) && !empty($edited)) {
                        $ch_url = base_url() . 'admin/invoice/';
                        $change_status .= '<div class="btn-group">
        <button class="btn btn-xs btn-default dropdown-toggle"
                data-toggle="dropdown">
                    ' . lang('change') . '
            <span class="caret"></span></button>
        <ul class="dropdown-menu animated zoomIn">';
                        $change_status .= '<li><a href="' . $ch_url . 'manage_invoice/invoice_details/' . $v_invoices->invoices_id . '">' . lang('preview_invoice') . '</a></li>';
                        $change_status .= '<li><a href="' . $ch_url . 'manage_invoice/payment/' . $v_invoices->invoices_id . '">' . lang('pay_invoice') . '</a></li>';
                        $change_status .= '<li><a href="' . $ch_url . 'manage_invoice/email_invoice/' . $v_invoices->invoices_id . '">' . lang('email_invoice') . '</a></li>';
                        $change_status .= '<li><a href="' . $ch_url . 'manage_invoice/send_reminder/' . $v_invoices->invoices_id . '">' . lang('send_reminder') . '</a></li>';
                        $change_status .= '<li><a href="' . $ch_url . 'manage_invoice/send_overdue/' . $v_invoices->invoices_id . '">' . lang('send_invoice_overdue') . '</a></li>';
                        $change_status .= '<li><a href="' . $ch_url . 'manage_invoice/invoice_history/' . $v_invoices->invoices_id . '">' . lang('invoice_history') . '</a></li>';
                        $change_status .= '<li><a href="' . $ch_url . 'pdf_invoice/' . $v_invoices->invoices_id . '">' . lang('pdf') . '</a></li>';
                        $change_status .= '</ul></div>';
                        $action .= $change_status;
                    }

                    $sub_array[] = $action;
                    $data[] = $sub_array;
                }
            }
            render_table($data, array('recurring' => 'Yes'));
        } else {
            redirect('admin/dashboard');
        }
    }

    public function paymentList($filterBy = null, $search_by = null)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_payments';
            $this->datatables->join_table = array('tbl_invoices', 'tbl_client', 'tbl_payment_methods');
            $this->datatables->join_where = array('tbl_payments.invoices_id=tbl_invoices.invoices_id', 'tbl_payments.paid_by=tbl_client.client_id', 'tbl_payment_methods.payment_methods_id=tbl_payments.payment_method');
            $this->datatables->column_order = array('payment_date', 'created_date', 'amount', 'tbl_client.name', 'tbl_invoices.date_saved', 'tbl_invoices.reference_no', 'tbl_payment_methods.method_name');
            $this->datatables->column_search = array('payment_date', 'created_date', 'amount', 'tbl_client.name', 'tbl_invoices.date_saved', 'tbl_invoices.reference_no', 'tbl_payment_methods.method_name');
            $this->datatables->order = array('payments_id' => 'desc');

            $where = array();
            if (!empty($search_by)) {
                if ($search_by == 'by_invoice') {
                    $where = array('tbl_payments.invoices_id' => $filterBy);
                }
                if ($search_by == 'by_account') {
                    $where = array('account_id' => $filterBy);
                }
                if ($search_by == 'by_client') {
                    $where = array('tbl_payments.paid_by' => $filterBy);
                }
            } else {
                if ($filterBy == 'last_month' || $filterBy == 'this_months') {
                    if ($filterBy == 'last_month') {
                        $month = date('m', strtotime('-1 months'));
                        $year = date('Y', strtotime('-1 months'));
                    } else {
                        $month = date('m');
                        $year = date('Y');
                    }
                    $where = array('year_paid' => $year, 'month_paid' => $month);
                } else if ($filterBy == 'today') {
                    $where = array('UNIX_TIMESTAMP(payment_date)' => strtotime(date('Y-m-d')));
                } else if (strstr($filterBy, '_')) {
                    $year = str_replace('_', '', $filterBy);
                    $where = array('year_paid' => $year);
                }
            }

            // get all invoice
            $fetch_data = $this->datatables->get_payment($filterBy, $search_by);
            $data = array();

            $edited = can_action('13', 'edited');
            $deleted = can_action('13', 'deleted');
            foreach ($fetch_data as $_key => $v_payments_info) {
                if (!empty($v_payments_info)) {
                    $action = null;
                    $v_invoices = get_row('tbl_invoices', array('invoices_id' => $v_payments_info->invoices_id));
                    if (empty($v_invoices)) {
                        $v_invoices = new stdClass();
                        $v_invoices->client_id = 0;
                        $v_invoices->date_saved = 0;
                        $v_invoices->invoices_id = 0;
                        $v_invoices->reference_no = '-';
                    }
                    $can_edit = $this->invoice_model->can_action('tbl_invoices', 'edit', array('invoices_id' => $v_invoices->invoices_id));
                    $can_delete = $this->invoice_model->can_action('tbl_invoices', 'delete', array('invoices_id' => $v_invoices->invoices_id));

                    $payment_methods = $this->invoice_model->check_by(array('payment_methods_id' => $v_payments_info->payment_method), 'tbl_payment_methods');
                    $sub_array = array();
                    $name = null;
                    $name .= '<a class="text-info" href="' . base_url() . 'admin/invoice/manage_invoice/payments_details/' . $v_payments_info->payments_id . '">' . strftime(config_item('date_format'), strtotime($v_payments_info->payment_date)) . '</a>';

                    $sub_array[] = $name;
                    $sub_array[] = strftime(config_item('date_format'), strtotime($v_invoices->date_saved));
                    $sub_array[] = '<a class="text-info" href="' . base_url() . 'admin/invoice/manage_invoice/invoice_details/' . $v_invoices->invoices_id . '">' . $v_invoices->reference_no . '</a>';
                    $sub_array[] = client_name($v_invoices->client_id);
                    $sub_array[] = display_money($v_payments_info->amount, client_currency($v_invoices->client_id));
                    $sub_array[] = !empty($payment_methods->method_name) ? $payment_methods->method_name : '-';

                    if (!empty($can_edit) && !empty($edited)) {
                        $action .= btn_edit('admin/invoice/all_payments/' . $v_payments_info->payments_id) . ' ';
                    }
                    if (!empty($can_delete) && !empty($deleted)) {
                        $action .= ajax_anchor(base_url("admin/invoice/delete/delete_payment/$v_payments_info->payments_id"), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                    }
                    $action .= btn_view('admin/invoice/manage_invoice/payments_details/' . $v_payments_info->payments_id) . ' ';
                    $action .= '<a class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="' . lang('send_email') . '" href="' . base_url() . 'admin/invoice/send_payment/' . $v_payments_info->payments_id . '/' . $v_payments_info->amount . '"><i class="fa fa-envelope"></i></a>' . ' ';;

                    $sub_array[] = $action;
                    $data[] = $sub_array;
                }
            }

            render_table($data, $where);
        } else {
            redirect('admin/dashboard');
        }
    }


    public
    function make_payment()
    {
        $edited = can_action('13', 'edited');
        if (!empty($edited)) {
            $data['all_invoices'] = $this->invoice_model->get_client_wise_invoice();
            $data['modal_subview'] = $this->load->view('admin/invoice/make_payment', $data, FALSE);
            $this->load->view('admin/_layout_modal', $data);
        } else {
            set_message('error', lang('there_in_no_value'));
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/invoice/all_payments');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public
    function client_change_data($customer_id, $current_invoice = 'undefined')
    {
        if ($this->input->is_ajax_request()) {
            $data = array();
            $data['client_currency'] = $this->invoice_model->client_currency_symbol($customer_id);
            $_data['invoices_to_merge'] = $this->invoice_model->check_for_merge_invoice($customer_id, $current_invoice);
            $data['merge_info'] = $this->load->view('admin/invoice/merge_invoice', $_data, true);
            echo json_encode($data);
            exit();
        }
    }

    public
    function get_merge_data($id)
    {
        $invoice_items = $this->invoice_model->ordered_items_by_id($id);
        $i = 0;
        foreach ($invoice_items as $item) {
            $invoice_items[$i]->taxname = $this->invoice_model->get_invoice_item_taxes($item->items_id);
//            $invoice_items[$i]->new_itmes_id = $item->saved_items_id;
            $invoice_items[$i]->qty = $item->quantity;
            $invoice_items[$i]->rate = $item->unit_cost;
            $i++;
        }
        echo json_encode($invoice_items);
        exit();
    }

    public
    function payments_pdf($id)
    {
        $data['title'] = "Payments PDF"; //Page title
        // get payment info by id
        $this->invoice_model->_table_name = 'tbl_payments';
        $this->invoice_model->_order_by = 'payments_id';
        $data['payments_info'] = $this->invoice_model->get_by(array('payments_id' => $id), TRUE);
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/invoice/payments_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it('Payment  # ' . $data['payments_info']->trans_id));
    }

    public
    function pdf_invoice($id)
    {
        $data['invoice_info'] = $this->invoice_model->check_by(array('invoices_id' => $id), 'tbl_invoices');
        $data['title'] = "Invoice PDF"; //Page title
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/invoice/invoice_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it('Invoice# ' . $data['invoice_info']->reference_no));
    }

    public
    function project_invoice($id)
    {
        $data['title'] = lang('project') . ' ' . lang('invoice'); //Page title
        $data['active'] = 2;
        $data['project_id'] = $id;
        $data['project_info'] = $this->invoice_model->check_by(array('project_id' => $id), 'tbl_project');
        // get all client
        $this->invoice_model->_table_name = 'tbl_client';
        $this->invoice_model->_order_by = 'client_id';
        $data['all_client'] = $this->invoice_model->get();
        // get permission user
        $data['permission_user'] = $this->invoice_model->all_permission_user('13');
        // get all invoice
        $data['all_invoices_info'] = $this->invoice_model->get_permission('tbl_invoices');
        $data['subview'] = $this->load->view('admin/invoice/manage_invoice', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public
    function all_payments($id = NULL)
    {
        if (!empty($id)) {
            $can_edit = $this->invoice_model->can_action('tbl_invoices', 'edit', array('invoices_id' => $id));
            if (!empty($can_edit)) {
                $payments_info = $this->invoice_model->check_by(array('payments_id' => $id), 'tbl_payments');
                $data['invoice_info'] = $this->invoice_model->check_by(array('invoices_id' => $payments_info->invoices_id), 'tbl_invoices');
            }
            $data['title'] = "Edit Payments"; //Page title
            $subview = 'edit_payments';
        } else {
            $data['title'] = "All Payments"; //Page title
            $subview = 'all_payments';
        }
        $data['all_invoice_info'] = $this->invoice_model->get_permission('tbl_invoices');

        // get payment info by id

        if (!empty($id)) {
            $can_edit = $this->invoice_model->can_action('tbl_payments', 'edit', array('payments_id' => $id));
            if (!empty($can_edit)) {
                $this->invoice_model->_table_name = 'tbl_payments';
                $this->invoice_model->_order_by = 'payments_id';
                $data['payments_info'] = $this->invoice_model->get_by(array('payments_id' => $id), TRUE);
            } else {
                set_message('error', lang('no_permission_to_access'));
                if (empty($_SERVER['HTTP_REFERER'])) {
                    redirect('admin/invoice/all_payments');
                } else {
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }

        }
        $data['subview'] = $this->load->view('admin/invoice/' . $subview, $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public
    function save_invoice($id = NULL)
    {
        $created = can_action('13', 'created');
        $edited = can_action('13', 'edited');
        if (!empty($created) || !empty($edited) && !empty($id)) {
            $data = $this->invoice_model->array_from_post(array('reference_no', 'client_id', 'project_id', 'discount_type', 'discount_percent', 'user_id', 'adjustment', 'discount_total', 'show_quantity_as'));

            $data['allow_paypal'] = ($this->input->post('allow_paypal') == 'Yes') ? 'Yes' : 'No';
            $data['allow_stripe'] = ($this->input->post('allow_stripe') == 'Yes') ? 'Yes' : 'No';
            $data['allow_2checkout'] = ($this->input->post('allow_2checkout') == 'Yes') ? 'Yes' : 'No';
            $data['allow_authorize'] = ($this->input->post('allow_authorize') == 'Yes') ? 'Yes' : 'No';
            $data['allow_ccavenue'] = ($this->input->post('allow_ccavenue') == 'Yes') ? 'Yes' : 'No';
            $data['allow_braintree'] = ($this->input->post('allow_braintree') == 'Yes') ? 'Yes' : 'No';
            $data['allow_mollie'] = ($this->input->post('allow_mollie') == 'Yes') ? 'Yes' : 'No';
            $data['allow_payumoney'] = ($this->input->post('allow_payumoney') == 'Yes') ? 'Yes' : 'No';
            $data['allow_tapPayment'] = ($this->input->post('allow_tapPayment') == 'Yes') ? 'Yes' : 'No';
            $data['allow_razorpay'] = ($this->input->post('allow_razorpay') == 'Yes') ? 'Yes' : 'No';
            $data['client_visible'] = ($this->input->post('client_visible') == 'Yes') ? 'Yes' : 'No';
            $data['invoice_date'] = date('Y-m-d', strtotime($this->input->post('invoice_date', TRUE)));
            if (empty($data['invoice_date'])) {
                $data['invoice_date'] = date('Y-m-d');
            }
            $data['invoice_year'] = date('Y', strtotime($this->input->post('invoice_date', TRUE)));
            $data['invoice_month'] = date('Y-m', strtotime($this->input->post('invoice_date', TRUE)));
            $data['due_date'] = date('Y-m-d', strtotime($this->input->post('due_date', TRUE)));
            $data['notes'] = $this->input->post('notes', TRUE);
            $tax['tax_name'] = $this->input->post('total_tax_name', TRUE);
            $tax['total_tax'] = $this->input->post('total_tax', TRUE);
            $data['total_tax'] = json_encode($tax);
            $i_tax = 0;
            if (!empty($tax['total_tax'])) {
                foreach ($tax['total_tax'] as $v_tax) {
                    $i_tax += $v_tax;
                }
            }
            $data['tax'] = $i_tax;
            $save_as_draft = $this->input->post('save_as_draft', TRUE);
            if (!empty($save_as_draft)) {
                $data['status'] = 'draft';
            }

            $currency = $this->invoice_model->client_currency_symbol($data['client_id']);
            if (!empty($currency->code)) {
                $curren = $currency->code;
            } else {
                $curren = config_item('default_currency');
            }
            $data['currency'] = $curren;

            $permission = $this->input->post('permission', true);
            if (!empty($permission)) {
                if ($permission == 'everyone') {
                    $assigned = 'all';
                } else {
                    $assigned_to = $this->invoice_model->array_from_post(array('assigned_to'));
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
                    redirect('admin/invoice/manage_invoice');
                } else {
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }
            // get all client
            $this->invoice_model->_table_name = 'tbl_invoices';
            $this->invoice_model->_primary_key = 'invoices_id';
            if (!empty($id)) {
                $invoice_id = $id;
                $can_edit = $this->invoice_model->can_action('tbl_invoices', 'edit', array('invoices_id' => $id));
                if (!empty($can_edit)) {
                    $this->invoice_model->save($data, $id);
                } else {
                    set_message('error', lang('there_in_no_value'));
                    redirect('admin/invoice/manage_invoice');
                }
                $action = ('activity_invoice_updated');
                $description = 'not_invoice_updated';
                $msg = lang('invoice_updated');

            } else {
                $invoice_id = $this->invoice_model->save($data);
                $action = ('activity_invoice_created');
                $description = 'not_invoice_created';
                $msg = lang('invoice_created');
            }
            save_custom_field(9, $invoice_id);

            $recuring_frequency = $this->input->post('recuring_frequency', TRUE);
            if (!empty($recuring_frequency) && $recuring_frequency != 'none') {
                $recur_data = $this->invoice_model->array_from_post(array('recur_start_date', 'recur_end_date'));
                $recur_data['recuring_frequency'] = $recuring_frequency;
                $this->get_recuring_frequency($invoice_id, $recur_data); // set recurring
            } else {
                $update_recur = array(
                    'recurring' => 'No',
                    'recur_end_date' => date('Y-m-d'),
                    'recur_next_date' => '0000-00-00'
                );
                $this->invoice_model->_table_name = 'tbl_invoices';
                $this->invoice_model->_primary_key = 'invoices_id';
                $this->invoice_model->save($update_recur, $invoice_id);
            }

            $qty_calculation = config_item('qty_calculation_from_items');
            // save items
            $invoices_to_merge = $this->input->post('invoices_to_merge', TRUE);
            $cancel_merged_invoices = $this->input->post('cancel_merged_invoices', TRUE);
            if (!empty($invoices_to_merge)) {
                foreach ($invoices_to_merge as $inv_id) {
                    if (empty($cancel_merged_invoices)) {
                        if (!empty($qty_calculation) && $qty_calculation == 'Yes') {
                            $all_items_info = $this->db->where('invoices_id', $inv_id)->get('tbl_items')->result();
                            if (!empty($all_items_info)) {
                                foreach ($all_items_info as $v_items) {
                                    $this->return_items($v_items->items_id);
                                }
                            }
                        }
                        $this->db->where('invoices_id', $inv_id);
                        $this->db->delete('tbl_invoices');

                        $this->db->where('invoices_id', $inv_id);
                        $this->db->delete('tbl_items');

                    } else {
                        $mdata = array('status' => 'Cancelled');
                        $this->invoice_model->_table_name = 'tbl_invoices';
                        $this->invoice_model->_primary_key = 'invoices_id';
                        $this->invoice_model->save($mdata, $inv_id);
                    }
                }
            }

            $removed_items = $this->input->post('removed_items', TRUE);
            if (!empty($removed_items)) {
                foreach ($removed_items as $r_id) {
                    if ($r_id != 'undefined') {
                        if (!empty($qty_calculation) && $qty_calculation == 'Yes') {
                            $this->return_items($r_id);
                        }

                        $this->db->where('items_id', $r_id);
                        $this->db->delete('tbl_items');
                    }
                }
            }

            $itemsid = $this->input->post('items_id', TRUE);
            $items_data = $this->input->post('items', true);
            if (!empty($items_data)) {
                $index = 0;
                foreach ($items_data as $items) {
                    unset($items['invoice_items_id']);
                    unset($items['total_qty']);
                    $items['invoices_id'] = $invoice_id;
                    $tax = 0;
                    if (!empty($items['taxname'])) {
                        foreach ($items['taxname'] as $tax_name) {
                            $tax_rate = explode("|", $tax_name);
                            $tax += $tax_rate[1];

                        }
                        $items['item_tax_name'] = $items['taxname'];
                        unset($items['taxname']);
                        $items['item_tax_name'] = json_encode($items['item_tax_name']);
                    }
                    if (empty($items['saved_items_id'])) {
                        $items['saved_items_id'] = 0;
                    }

                    $price = $items['quantity'] * $items['unit_cost'];
                    $items['item_tax_total'] = ($price / 100 * $tax);
                    $items['total_cost'] = $price;
                    // get all client
                    $this->invoice_model->_table_name = 'tbl_items';
                    $this->invoice_model->_primary_key = 'items_id';

                    if (!empty($items['items_id'])) {
                        $items_id = $items['items_id'];
                        if (!empty($qty_calculation) && $qty_calculation == 'Yes') {
                            $this->check_existing_qty($items_id, $items['quantity']);
                        }
                        $this->invoice_model->save($items, $items_id);
                    } else {
                        if (!empty($qty_calculation) && $qty_calculation == 'Yes') {
                            if (!empty($items['saved_items_id']) && $items['saved_items_id'] != 'undefined') {
                                $this->invoice_model->reduce_items($items['saved_items_id'], $items['quantity']);
                            }
                        }
                        $items_id = $this->invoice_model->save($items);
                    }
                    $index++;
                }
            }
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'invoice',
                'module_field_id' => $invoice_id,
                'activity' => $action,
                'icon' => 'fa-shopping-cart',
                'link' => 'admin/invoice/manage_invoice/invoice_details/' . $invoice_id,
                'value1' => $data['reference_no']
            );
            $this->invoice_model->_table_name = 'tbl_activities';
            $this->invoice_model->_primary_key = 'activities_id';
            $this->invoice_model->save($activity);


            // send notification to client
            if (!empty($data['client_id'])) {
                $client_info = $this->invoice_model->check_by(array('client_id' => $data['client_id']), 'tbl_client');
                if (!empty($client_info->primary_contact)) {
                    $notifyUser = array($client_info->primary_contact);
                } else {
                    $user_info = $this->invoice_model->check_by(array('company' => $data['client_id']), 'tbl_account_details');
                    if (!empty($user_info)) {
                        $notifyUser = array($user_info->user_id);
                    }
                }
            }
            if (!empty($notifyUser)) {
                foreach ($notifyUser as $v_user) {
                    if ($v_user != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $v_user,
                            'icon' => 'shopping-cart',
                            'description' => $description,
                            'link' => 'client/invoice/manage_invoice/invoice_details/' . $invoice_id,
                            'value' => $data['reference_no'],
                        ));
                    }
                }
                show_notification($notifyUser);
            }
            // messages for user
            $type = "success";
            $message = $msg;
            set_message($type, $message);
            redirect('admin/invoice/manage_invoice/invoice_details/' . $invoice_id);
        } else {
            redirect('admin/invoice/manage_invoice');
        }
    }

    function return_items($items_id)
    {
        $items_info = $this->db->where('items_id', $items_id)->get('tbl_items')->row();
        if (!empty($items_info->saved_items_id)) {
            $this->invoice_model->return_items($items_info->saved_items_id, $items_info->quantity);
        }
        return true;

    }

    function check_existing_qty($items_id, $qty)
    {
        $items_info = $this->db->where('items_id', $items_id)->get('tbl_items')->row();
        if ($items_info->quantity != $qty) {
            if ($qty > $items_info->quantity) {
                $reduce_qty = $qty - $items_info->quantity;
                if (!empty($items_info->saved_items_id)) {
                    $this->invoice_model->reduce_items($items_info->saved_items_id, $reduce_qty);
                }
            }
            if ($qty < $items_info->quantity) {
                $return_qty = $items_info->quantity - $qty;
                if (!empty($items_info->saved_items_id)) {
                    $this->invoice_model->return_items($items_info->saved_items_id, $return_qty);
                }
            }
        }
        return true;

    }

    public
    function recurring_invoice($id = NULL)
    {
        $data['title'] = lang('recurring_invoice');
        if (!empty($id)) {
            $data['invoice_info'] = $this->invoice_model->check_by(array('invoices_id' => $id), 'tbl_invoices');
            $data['active'] = 2;
        } else {
            $data['active'] = 1;
        }
        // get all client
        $this->invoice_model->_table_name = 'tbl_client';
        $this->invoice_model->_order_by = 'client_id';
        $data['all_client'] = $this->invoice_model->get();
        // get permission user
        $data['permission_user'] = $this->invoice_model->all_permission_user('51');

        // get all invoice
        $data['all_invoices_info'] = $this->invoice_model->get_permission('tbl_invoices');

        $data['subview'] = $this->load->view('admin/invoice/recurring_invoice', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    function get_recuring_frequency($invoices_id, $recur_data)
    {
        $recur_days = $this->get_calculate_recurring_days($recur_data['recuring_frequency']);
        $due_date = $this->invoice_model->get_table_field('tbl_invoices', array('invoices_id' => $invoices_id), 'due_date');

        $next_date = date("Y-m-d", strtotime($due_date . "+ " . $recur_days . " days"));

        if ($recur_data['recur_end_date'] == '') {
            $recur_end_date = '0000-00-00';
        } else {
            $recur_end_date = date('Y-m-d', strtotime($recur_data['recur_end_date']));
        }
        $update_invoice = array(
            'recurring' => 'Yes',
            'recuring_frequency' => $recur_days,
            'recur_frequency' => $recur_data['recuring_frequency'],
            'recur_start_date' => date('Y-m-d', strtotime($recur_data['recur_start_date'])),
            'recur_end_date' => $recur_end_date,
            'recur_next_date' => $next_date
        );
        $this->invoice_model->_table_name = 'tbl_invoices';
        $this->invoice_model->_primary_key = 'invoices_id';
        $this->invoice_model->save($update_invoice, $invoices_id);
        return TRUE;
    }

    function get_calculate_recurring_days($recuring_frequency)
    {
        switch ($recuring_frequency) {
            case '7D':
                return 7;
                break;
            case '1M':
                return 31;
                break;
            case '3M':
                return 90;
                break;
            case '6M':
                return 182;
                break;
            case '1Y':
                return 365;
                break;
        }
    }

    public
    function stop_recurring($invoices_id)
    {
        $update_recur = array(
            'recurring' => 'No',
            'recur_end_date' => date('Y-m-d'),
            'recur_next_date' => '0000-00-00'
        );
        $this->invoice_model->_table_name = 'tbl_invoices';
        $this->invoice_model->_primary_key = 'invoices_id';
        $this->invoice_model->save($update_recur, $invoices_id);
        // Log Activity
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'invoice',
            'module_field_id' => $invoices_id,
            'activity' => 'activity_recurring_stopped',
            'icon' => 'fa-shopping-cart',
            'link' => 'admin/invoice/manage_invoice/invoice_details/' . $invoices_id,
        );
        $this->invoice_model->_table_name = 'tbl_activities';
        $this->invoice_model->_primary_key = 'activities_id';
        $this->invoice_model->save($activity);
        // messages for user
        $type = "success";
        $message = lang('recurring_invoice_stopped');
        set_message($type, $message);
        redirect('admin/invoice/manage_invoice');
    }

    public
    function insert_items($invoices_id)
    {
        $edited = can_action('13', 'edited');
        $can_edit = $this->invoice_model->can_action('tbl_invoices', 'edit', array('invoices_id' => $invoices_id));
        if (!empty($can_edit) && !empty($edited)) {
            $data['invoices_id'] = $invoices_id;
            $data['modal_subview'] = $this->load->view('admin/invoice/_modal_insert_items', $data, FALSE);
            $this->load->view('admin/_layout_modal', $data);
        } else {
            set_message('error', lang('there_in_no_value'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public
    function clone_invoice($invoices_id)
    {
        $edited = can_action('13', 'edited');
        $can_edit = $this->invoice_model->can_action('tbl_invoices', 'edit', array('invoices_id' => $invoices_id));
        if (!empty($can_edit) && !empty($edited)) {
            $data['invoice_info'] = $this->invoice_model->check_by(array('invoices_id' => $invoices_id), 'tbl_invoices');
            // get all client
            $this->invoice_model->_table_name = 'tbl_client';
            $this->invoice_model->_order_by = 'client_id';
            $data['all_client'] = $this->invoice_model->get();

            $data['modal_subview'] = $this->load->view('admin/invoice/_modal_clone_invoice', $data, FALSE);
            $this->load->view('admin/_layout_modal', $data);
        } else {
            set_message('error', lang('there_in_no_value'));
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/invoice/manage_invoice');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public
    function cloned_invoice($id)
    {
        $edited = can_action('13', 'edited');
        $can_edit = $this->invoice_model->can_action('tbl_invoices', 'edit', array('invoices_id' => $id));
        if (!empty($can_edit) && !empty($edited)) {
            if (config_item('increment_invoice_number') == 'FALSE') {
                $this->load->helper('string');
                $reference_no = config_item('invoice_prefix') . ' ' . random_string('nozero', 6);
            } else {
                $reference_no = $this->invoice_model->generate_invoice_number();
            }
            $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $id), 'tbl_invoices');

            // save into invoice table
            $new_invoice = array(
                'reference_no' => $reference_no,
                'recur_start_date' => $invoice_info->recur_start_date,
                'recur_end_date' => $invoice_info->recur_end_date,
                'client_id' => $this->input->post('client_id', true),
                'project_id' => $invoice_info->project_id,
                'invoice_date' => $this->input->post('invoice_date', true),
                'invoice_year' => date('Y', strtotime($this->input->post('invoice_date', true))),
                'invoice_month' => date('Y-m', strtotime($this->input->post('invoice_date', true))),
                'due_date' => $this->input->post('due_date', true),
                'notes' => $invoice_info->notes,
                'total_tax' => $invoice_info->total_tax,
                'tax' => $invoice_info->tax,
                'discount_type' => $invoice_info->discount_type,
                'discount_percent' => $invoice_info->discount_percent,
                'user_id' => $invoice_info->user_id,
                'adjustment' => $invoice_info->adjustment,
                'discount_total' => $invoice_info->discount_total,
                'show_quantity_as' => $invoice_info->show_quantity_as,
                'recurring' => $invoice_info->recurring,
                'recuring_frequency' => $invoice_info->recuring_frequency,
                'recur_frequency' => $invoice_info->recur_frequency,
                'recur_next_date' => $invoice_info->recur_next_date,
                'currency' => $invoice_info->currency,
                'status' => $invoice_info->status,
                'date_saved' => $invoice_info->date_saved,
                'emailed' => $invoice_info->emailed,
                'show_client' => $invoice_info->show_client,
                'viewed' => $invoice_info->viewed,
                'allow_paypal' => $invoice_info->allow_paypal,
                'allow_stripe' => $invoice_info->allow_stripe,
                'allow_2checkout' => $invoice_info->allow_2checkout,
                'allow_authorize' => $invoice_info->allow_authorize,
                'allow_ccavenue' => $invoice_info->allow_ccavenue,
                'allow_braintree' => $invoice_info->allow_braintree,
                'permission' => $invoice_info->permission,
            );

            $this->invoice_model->_table_name = "tbl_invoices"; //table name
            $this->invoice_model->_primary_key = "invoices_id";
            $new_invoice_id = $this->invoice_model->save($new_invoice);

            $invoice_items = $this->db->where('invoices_id', $id)->get('tbl_items')->result();
            if (!empty($invoice_items)) {
                foreach ($invoice_items as $new_item) {
                    $this->invoice_model->reduce_items($new_item->saved_items_id, $new_item->quantity);
                    $items = array(
                        'invoices_id' => $new_invoice_id,
                        'saved_items_id' => $new_item->saved_items_id,
                        'item_name' => $new_item->item_name,
                        'item_desc' => $new_item->item_desc,
                        'unit_cost' => $new_item->unit_cost,
                        'quantity' => $new_item->quantity,
                        'item_tax_rate' => $new_item->item_tax_rate,
                        'item_tax_name' => $new_item->item_tax_name,
                        'item_tax_total' => $new_item->item_tax_total,
                        'total_cost' => $new_item->total_cost,
                        'unit' => $new_item->unit,
                        'order' => $new_item->order,
                        'date_saved' => $new_item->date_saved,
                    );
                    $this->invoice_model->_table_name = "tbl_items"; //table name
                    $this->invoice_model->_primary_key = "items_id";
                    $this->invoice_model->save($items);
                }
            }
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'invoice',
                'module_field_id' => $new_invoice_id,
                'activity' => ('activity_cloned_invoice'),
                'icon' => 'fa-shopping-cart',
                'link' => 'admin/invoice/manage_invoice/invoice_details/' . $new_invoice_id,
                'value1' => ' from ' . $invoice_info->reference_no . ' to ' . $reference_no,
            );
            // Update into tbl_project
            $this->invoice_model->_table_name = "tbl_activities"; //table name
            $this->invoice_model->_primary_key = "activities_id";
            $this->invoice_model->save($activities);

            // messages for user
            $type = "success";
            $message = lang('invoice_created');
            set_message($type, $message);
            redirect('admin/invoice/manage_invoice/invoice_details/' . $new_invoice_id);
        } else {
            set_message('error', lang('there_in_no_value'));
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/invoice/manage_invoice');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public
    function add_insert_items($invoices_id)
    {

        $edited = can_action('13', 'edited');
        $can_edit = $this->invoice_model->can_action('tbl_invoices', 'edit', array('invoices_id' => $invoices_id));
        if (!empty($can_edit) && !empty($edited)) {
            $saved_items_id = $this->input->post('saved_items_id', TRUE);
            if (!empty($saved_items_id)) {

                foreach ($saved_items_id as $v_items_id) {
                    $this->invoice_model->reduce_items($v_items_id, 1);
                    $items_info = $this->invoice_model->check_by(array('saved_items_id' => $v_items_id), 'tbl_saved_items');

                    $tax_info = json_decode($items_info->tax_rates_id);
                    $tax_name = array();
                    if (!empty($tax_info)) {
                        foreach ($tax_info as $v_tax) {
                            $all_tax = $this->db->where('tax_rates_id', $v_tax)->get('tbl_tax_rates')->row();
                            $tax_name[] = $all_tax->tax_rate_name . '|' . $all_tax->tax_rate_percent;
                        }
                    }
                    if (!empty($tax_name)) {
                        $tax_name = $tax_name;
                    } else {
                        $tax_name = array();
                    }

                    $data['quantity'] = 1;
                    $data['invoices_id'] = $invoices_id;
                    $data['item_name'] = $items_info->item_name;
                    $data['item_desc'] = $items_info->item_desc;
                    $data['hsn_code'] = $items_info->hsn_code;
                    $data['unit_cost'] = $items_info->unit_cost;
                    $data['item_tax_rate'] = '0.00';
                    $data['item_tax_name'] = json_encode($tax_name);
                    $data['item_tax_total'] = $items_info->item_tax_total;
                    $data['total_cost'] = $items_info->unit_cost;
                    // get all client
                    $this->invoice_model->_table_name = 'tbl_items';
                    $this->invoice_model->_primary_key = 'items_id';
                    $items_id = $this->invoice_model->save($data);

                    $action = ('activity_invoice_items_added');
                    $activity = array(
                        'user' => $this->session->userdata('user_id'),
                        'module' => 'invoice',
                        'module_field_id' => $items_id,
                        'activity' => $action,
                        'icon' => 'fa-circle-o',
                        'value1' => $items_info->item_name
                    );
                    $this->invoice_model->_table_name = 'tbl_activities';
                    $this->invoice_model->_primary_key = 'activities_id';
                    $this->invoice_model->save($activity);
                }

                $this->update_invoice_tax($saved_items_id, $invoices_id);

                $type = "success";
                $msg = lang('invoice_item_added');

            } else {
                $type = "error";
                $msg = 'please Select an items';
            }
            $message = $msg;
            set_message($type, $message);
            redirect('admin/invoice/manage_invoice/invoice_details/' . $invoices_id);
        } else {
            set_message('error', lang('there_in_no_value'));
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/invoice/manage_invoice');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    function update_invoice_tax($saved_items_id, $invoices_id)
    {

        $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $invoices_id), 'tbl_invoices');
        $tax_info = json_decode($invoice_info->total_tax);

        $tax_name = $tax_info->tax_name;
        $total_tax = $tax_info->total_tax;
        $invoice_tax = array();
        if (!empty($tax_name)) {
            foreach ($tax_name as $t_key => $v_tax_info) {
                array_push($invoice_tax, array('tax_name' => $v_tax_info, 'total_tax' => $total_tax[$t_key]));
            }
        }
        $all_tax_info = array();
        if (!empty($saved_items_id)) {
            foreach ($saved_items_id as $v_items_id) {
                $items_info = $this->invoice_model->check_by(array('saved_items_id' => $v_items_id), 'tbl_saved_items');

                $tax_info = json_decode($items_info->tax_rates_id);
                if (!empty($tax_info)) {
                    foreach ($tax_info as $v_tax) {
                        $all_tax = $this->db->where('tax_rates_id', $v_tax)->get('tbl_tax_rates')->row();
                        array_push($all_tax_info, array('tax_name' => $all_tax->tax_rate_name . '|' . $all_tax->tax_rate_percent, 'total_tax' => $items_info->unit_cost / 100 * $all_tax->tax_rate_percent));
                    }
                }
            }
        }
        if (!empty($invoice_tax) && is_array($invoice_tax) && !empty($all_tax_info)) {
            $all_tax_info = array_merge($all_tax_info, $invoice_tax);
        }

        $results = array();
        foreach ($all_tax_info as $value) {
            if (!isset($results[$value['tax_name']])) {
                $results[$value['tax_name']] = 0;
            }
            $results[$value['tax_name']] += $value['total_tax'];

        }
        if (!empty($results)) {

            foreach ($results as $key => $value) {
                $structured_results['tax_name'][] = $key;
                $structured_results['total_tax'][] = $value;
            }
            $invoice_data['tax'] = array_sum($structured_results['total_tax']);
            $invoice_data['total_tax'] = json_encode($structured_results);

            $this->invoice_model->_table_name = 'tbl_invoices';
            $this->invoice_model->_primary_key = 'invoices_id';
            $this->invoice_model->save($invoice_data, $invoices_id);
        }
        return true;
    }

    public
    function add_item($id = NULL)
    {
        $edited = can_action('13', 'edited');
        $data = $this->invoice_model->array_from_post(array('invoices_id', 'item_order'));
        $can_edit = $this->invoice_model->can_action('tbl_invoices', 'edit', array('invoices_id' => $data['invoices_id']));
        if (!empty($can_edit) && !empty($edited)) {
            $quantity = $this->input->post('quantity', TRUE);
            $array_data = $this->invoice_model->array_from_post(array('item_name', 'item_desc', 'item_tax_rate', 'unit_cost'));
            if (!empty($quantity)) {
                foreach ($quantity as $key => $value) {
                    if (!empty($array_data['item_name'][$key])) {
                        $data['quantity'] = $value;
                        $data['item_name'] = $array_data['item_name'][$key];
                        $data['item_desc'] = $array_data['item_desc'][$key];
                        $data['unit_cost'] = $array_data['unit_cost'][$key];
                        $data['item_tax_rate'] = $array_data['item_tax_rate'][$key];
                        $sub_total = $data['unit_cost'] * $data['quantity'];

                        $data['item_tax_total'] = ($data['item_tax_rate'] / 100) * $sub_total;
                        $data['total_cost'] = $sub_total + $data['item_tax_total'];

                        // get all client
                        $this->invoice_model->_table_name = 'tbl_items';
                        $this->invoice_model->_primary_key = 'items_id';
                        if (!empty($id)) {
                            $items_id = $id;
                            $this->invoice_model->save($data, $id);
                            $action = ('activity_invoice_items_updated');
                            $msg = lang('invoice_item_updated');
                        } else {
                            $items_id = $this->invoice_model->save($data);
                            $action = ('activity_invoice_items_added');
                            $msg = lang('invoice_item_added');
                        }
                        $activity = array(
                            'user' => $this->session->userdata('user_id'),
                            'module' => 'invoice',
                            'module_field_id' => $items_id,
                            'activity' => $action,
                            'icon' => 'fa-circle-o',
                            'value1' => $data['item_name']
                        );
                        $this->invoice_model->_table_name = 'tbl_activities';
                        $this->invoice_model->_primary_key = 'activities_id';
                        $this->invoice_model->save($activity);
                    }
                }
            }
            $type = "success";
            $message = $msg;
            set_message($type, $message);
            redirect('admin/invoice/manage_invoice/invoice_details/' . $data['invoices_id']);
        } else {
            set_message('error', lang('there_in_no_value'));
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/invoice/manage_invoice');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public
    function change_status($action, $id)
    {
        $edited = can_action('13', 'edited');
        $can_edit = $this->invoice_model->can_action('tbl_invoices', 'edit', array('invoices_id' => $id));
        if (!empty($can_edit) && !empty($edited)) {
            $where = array('invoices_id' => $id);
            if ($action == 'hide') {
                $data = array('show_client' => 'No');
            } else {
                $data = array('show_client' => 'Yes');
            }
            $this->invoice_model->set_action($where, $data, 'tbl_invoices');
            // messages for user
            $type = "success";
            $message = lang('invoice_status_changed', $action);
            set_message($type, $message);
            redirect('admin/invoice/manage_invoice/invoice_details/' . $id);
        } else {
            set_message('error', lang('there_in_no_value'));
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/invoice/manage_invoice');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public
    function delete($action, $invoices_id, $item_id = NULL)
    {
        $deleted = can_action('13', 'deleted');
        $can_delete = $this->invoice_model->can_action('tbl_invoices', 'delete', array('invoices_id' => $invoices_id));
        if (!empty($can_delete) && !empty($deleted)) {
            $invoices_info = $this->invoice_model->check_by(array('invoices_id' => $invoices_id), 'tbl_invoices');
            if (!empty($invoices_info->reference_no)) {
                $val = $invoices_info->reference_no;
            } else {
                $val = NULL;
            }
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'invoice',
                'module_field_id' => $invoices_id,
                'activity' => ('activity_invoice' . $action),
                'icon' => 'fa-shopping-cart',
                'value1' => $val,

            );
            $this->invoice_model->_table_name = 'tbl_activities';
            $this->invoice_model->_primary_key = 'activities_id';
            $this->invoice_model->save($activity);

            if ($action == 'delete_item') {
                $this->invoice_model->_table_name = 'tbl_items';
                $this->invoice_model->_primary_key = 'items_id';
                $this->invoice_model->delete($item_id);
            } elseif ($action == 'delete_invoice') {
                $this->invoice_model->_table_name = 'tbl_items';
                $this->invoice_model->delete_multiple(array('invoices_id' => $invoices_id));

                $this->invoice_model->_table_name = 'tbl_payments';
                $this->invoice_model->delete_multiple(array('invoices_id' => $invoices_id));

                $this->invoice_model->_table_name = 'tbl_reminders';
                $this->invoice_model->delete_multiple(array('module' => 'invoice', 'module_id' => $invoices_id));

                $this->invoice_model->_table_name = 'tbl_pinaction';
                $this->invoice_model->delete_multiple(array('module_name' => 'invoice', 'module_id' => $invoices_id));

                $this->invoice_model->_table_name = 'tbl_credit_used';
                $this->invoice_model->delete_multiple(array('invoices_id' => $invoices_id));

                $this->invoice_model->_table_name = 'tbl_invoices';
                $this->invoice_model->_primary_key = 'invoices_id';
                $this->invoice_model->delete($invoices_id);
            } elseif ($action == 'delete_payment') {
                $this->invoice_model->_table_name = 'tbl_payments';
                $this->invoice_model->_primary_key = 'payments_id';
                $this->invoice_model->delete($invoices_id);
            } elseif ($action == 'delete_applied_credits') {
                $credit_used = get_row('tbl_credit_used', array('credit_used_id' => $item_id));
                if (!empty($credit_used->payments_id) && $credit_used->payments_id != 0) {
                    $this->invoice_model->_table_name = 'tbl_payments';
                    $this->invoice_model->_primary_key = 'payments_id';
                    $this->invoice_model->delete($credit_used->payments_id);
                }
                $this->invoice_model->_table_name = 'tbl_credit_used';
                $this->invoice_model->_primary_key = 'credit_used_id';
                $this->invoice_model->delete($item_id);
            }
            $type = "success";


            if ($action == 'delete_item') {
                $text = lang('invoice_item_deleted');
//                set_message($type, $text);
//                redirect('admin/invoice/manage_invoice/invoice_details/' . $invoices_id);
            } elseif ($action == 'delete_payment') {
                $text = lang('payment_deleted');
//                set_message($type, $text);
//                redirect('admin/invoice/manage_invoice/all_payments');
            } else {
                $text = lang('deleted_invoice');
//                set_message($type, $text);
//                redirect('admin/invoice/manage_invoice');
            }
            echo json_encode(array("status" => $type, 'message' => $text));
            exit();
        } else {
            echo json_encode(array("status" => 'error', 'message' => lang('there_in_no_value')));
            exit();
//            set_message('error', lang('there_in_no_value'));
//            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public
    function get_payment($invoices_id)
    {
        $edited = can_action('13', 'edited');
        $can_edit = $this->invoice_model->can_action('tbl_invoices', 'edit', array('invoices_id' => $invoices_id));
        if (!empty($can_edit) && !empty($edited)) {
            $due = $this->invoice_model->calculate_to('invoice_due', $invoices_id);
            $paid_amount = $this->input->post('amount', TRUE);
            if ($paid_amount != 0) {
                if ($paid_amount > $due) {
                    // messages for user
                    $type = "error";
                    $message = lang('overpaid_amount');
                    set_message($type, $message);
                    redirect('admin/invoice/manage_invoice/payment/' . $invoices_id);
                } else {
                    $inv_info = $this->invoice_model->check_by(array('invoices_id' => $invoices_id), 'tbl_invoices');
                    $data = array(
                        'invoices_id' => $invoices_id,
                        'paid_by' => $inv_info->client_id,
                        'payment_method' => $this->input->post('payment_methods_id', TRUE),
                        'currency' => $this->input->post('currency', TRUE),
                        'amount' => $paid_amount,
                        'payment_date' => date('Y-m-d', strtotime($this->input->post('payment_date', TRUE))),
                        'trans_id' => $this->input->post('trans_id', true),
                        'notes' => $this->input->post('notes', true),
                        'month_paid' => date("m", strtotime($this->input->post('payment_date', TRUE))),
                        'year_paid' => date("Y", strtotime($this->input->post('payment_date', TRUE))),
                    );

                    $this->invoice_model->_table_name = 'tbl_payments';
                    $this->invoice_model->_primary_key = 'payments_id';
                    $payments_id = $this->invoice_model->save($data);

                    if ($paid_amount < $due) {
                        $status = 'partially_paid';
                    }
                    if ($paid_amount == $due) {
                        $status = 'Paid';
                    }
                    $invoice_data['status'] = $status;
                    update('tbl_invoices', array('invoices_id' => $invoices_id), $invoice_data);

                    $activity = array(
                        'user' => $this->session->userdata('user_id'),
                        'module' => 'invoice',
                        'module_field_id' => $invoices_id,
                        'activity' => ('activity_new_payment'),
                        'icon' => 'fa-shopping-cart',
                        'link' => 'admin/invoice/manage_invoice/invoice_details/' . $invoices_id,
                        'value1' => display_money($paid_amount, client_currency($inv_info->client_id)),
                        'value2' => $inv_info->reference_no,
                    );
                    $this->invoice_model->_table_name = 'tbl_activities';
                    $this->invoice_model->_primary_key = 'activities_id';
                    $this->invoice_model->save($activity);

                    if (!empty($inv_info->user_id)) {
                        $notifiedUsers = array($inv_info->user_id);
                        foreach ($notifiedUsers as $users) {
                            if ($users != $this->session->userdata('user_id')) {
                                add_notification(array(
                                    'to_user_id' => $users,
                                    'description' => 'not_new_invoice_payment',
                                    'icon' => 'shopping-cart',
                                    'link' => 'admin/invoice/manage_invoice/invoice_details/' . $invoices_id,
                                    'value' => lang('invoice') . ' ' . $inv_info->reference_no . ' ' . lang('amount') . display_money($paid_amount, $currency->symbol),
                                ));
                            }
                        }
                        show_notification($notifiedUsers);
                    }

                    if ($this->input->post('save_into_account') == 'on') {
                        $account_id = $this->input->post('account_id', true);
                        if (empty($account_id)) {
                            $account_id = config_item('default_account');
                        }
                        if (!empty($account_id)) {
                            $reference = lang('invoice') . ' ' . lang('reference_no') . ": <a href='" . base_url('admin/invoice/manage_invoice/invoice_details/' . $inv_info->invoices_id) . "' >" . $inv_info->reference_no . "</a> and " . lang('trans_id') . ": <a href='" . base_url('admin/invoice/manage_invoice/payments_details/' . $payments_id) . "'>" . $this->input->post('trans_id', true) . "</a>";
                            $trans_id = $this->input->post('trans_id', true);
                            // save into tbl_transaction
                            $tr_data = array(
                                'name' => lang('invoice_payment', lang('trans_id') . '# ' . $trans_id),
                                'type' => 'Income',
                                'amount' => $paid_amount,
                                'credit' => $paid_amount,
                                'date' => date('Y-m-d', strtotime($this->input->post('payment_date', TRUE))),
                                'paid_by' => $inv_info->client_id,
                                'payment_methods_id' => $this->input->post('payment_methods_id', TRUE),
                                'reference' => $trans_id,
                                'notes' => lang('this_deposit_from_invoice_payment', $reference),
                                'permission' => 'all',
                            );


                            $account_info = $this->invoice_model->check_by(array('account_id' => $account_id), 'tbl_accounts');
                            if (!empty($account_info)) {
                                $ac_data['balance'] = $account_info->balance + $tr_data['amount'];
                                $this->invoice_model->_table_name = "tbl_accounts"; //table name
                                $this->invoice_model->_primary_key = "account_id";
                                $this->invoice_model->save($ac_data, $account_info->account_id);

                                $aaccount_info = $this->invoice_model->check_by(array('account_id' => $account_id), 'tbl_accounts');

                                $tr_data['total_balance'] = $aaccount_info->balance;
                                $tr_data['account_id'] = $account_id;

                                // save into tbl_transaction
                                $this->invoice_model->_table_name = "tbl_transactions"; //table name
                                $this->invoice_model->_primary_key = "transactions_id";
                                $return_id = $this->invoice_model->save($tr_data);

                                $deduct_account['account_id'] = $account_id;
                                $this->invoice_model->_table_name = 'tbl_payments';
                                $this->invoice_model->_primary_key = 'payments_id';
                                $this->invoice_model->save($deduct_account, $payments_id);

                                // save into activities
                                $activities = array(
                                    'user' => $this->session->userdata('user_id'),
                                    'module' => 'transactions',
                                    'module_field_id' => $return_id,
                                    'activity' => 'activity_new_deposit',
                                    'icon' => 'fa-building-o',
                                    'link' => 'admin/transactions/view_details/' . $return_id,
                                    'value1' => $account_info->account_name,
                                    'value2' => $paid_amount,
                                );
                                // Update into tbl_project
                                $this->invoice_model->_table_name = "tbl_activities"; //table name
                                $this->invoice_model->_primary_key = "activities_id";
                                $this->invoice_model->save($activities);

                            }
                        }
                    }
                    if ($this->input->post('send_thank_you') == 'on') {
                        $this->send_payment_email($invoices_id, $paid_amount); //send thank you email
                    }

                    if ($this->input->post('send_sms') == 'on') {
                        $this->send_payment_sms($invoices_id, $payments_id); //send thank you email
                    }
                }
            }
            // messages for user
            $type = "success";
            $message = lang('generate_payment');
            set_message($type, $message);
            redirect('admin/invoice/manage_invoice/invoice_details/' . $invoices_id);
        } else {
            set_message('error', lang('there_in_no_value'));
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/invoice/all_payments');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public function send_payment_sms($invoices_id, $payments_id)
    {
        $inv_info = $this->invoice_model->check_by(array('invoices_id' => $invoices_id), 'tbl_invoices');
        $mobile = client_can_received_sms($inv_info->client_id);
        if (!empty($mobile)) {
            $merge_fields = [];
            $merge_fields = array_merge($merge_fields, merge_invoice_template($invoices_id));
            $merge_fields = array_merge($merge_fields, merge_invoice_template($invoices_id, 'payment', $payments_id));
            $merge_fields = array_merge($merge_fields, merge_invoice_template($invoices_id, 'client', $inv_info->client_id));
            $this->sms->send(SMS_PAYMENT_RECORDED, $mobile, $merge_fields);
        }
        return true;
    }


    public
    function update_payemnt($payments_id)
    {
        $data = array(
            'amount' => $this->input->post('amount', TRUE),
            'payment_method' => $this->input->post('payment_methods_id', TRUE),
            'payment_date' => date('Y-m-d', strtotime($this->input->post('payment_date', TRUE))),
            'notes' => $this->input->post('notes', TRUE),
            'month_paid' => date("m", strtotime($this->input->post('payment_date', TRUE))),
            'year_paid' => date("Y", strtotime($this->input->post('payment_date', TRUE))),
        );
        $payments_info = $this->invoice_model->check_by(array('payments_id' => $payments_id), 'tbl_payments');
        if (empty($payments_info)) {
            $type = "error";
            $message = "No Record Found";
            set_message($type, $message);
            redirect('admin/invoice/all_payments');
        }
        if ($payments_info->amount != $data['amount']) {
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'invoice',
                'module_field_id' => $payments_id,
                'activity' => ('activity_update_payment'),
                'icon' => 'fa-shopping-cart',
                'link' => 'admin/invoice/manage_invoice/payments_details/' . $payments_id,
                'value1' => $data['amount'],
                'value2' => $data['payment_date'],
            );
            $this->invoice_model->_table_name = 'tbl_activities';
            $this->invoice_model->_primary_key = 'activities_id';
            $this->invoice_model->save($activity);


            // send notification to client
            if (!empty($payments_info)) {
                $client_info = $this->invoice_model->check_by(array('client_id' => $payments_info->paid_by), 'tbl_client');
                if (!empty($client_info->primary_contact)) {
                    $notifyUser = array($client_info->primary_contact);
                } else {
                    $user_info = $this->invoice_model->check_by(array('company' => $client_info->client_id), 'tbl_account_details');
                    if (!empty($user_info)) {
                        $notifyUser = array($user_info->user_id);
                    }
                }
            }
            if (!empty($notifyUser)) {
                foreach ($notifyUser as $v_user) {
                    if ($v_user != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $v_user,
                            'icon' => 'shopping-cart',
                            'description' => 'not_payment_update',
                            'link' => 'client/invoice/manage_invoice/payments_details/' . $payments_id,
                            'value' => lang('trans_id') . ' ' . $payments_info->trans_id . ' ' . lang('new') . ' ' . lang('amount') . ' ' . display_money($data['amount'], $payments_info->currency),
                        ));
                    }
                }
                show_notification($notifyUser);
            }
        }

        $this->invoice_model->_table_name = 'tbl_payments';
        $this->invoice_model->_primary_key = 'payments_id';
        $this->invoice_model->save($data, $payments_id);


        // messages for user
        $type = "success";
        $message = lang('generate_payment');
        set_message($type, $message);
        redirect('admin/invoice/all_payments');

    }

    public
    function send_payment($invoices_id, $paid_amount)
    {
        $this->send_payment_email($invoices_id, $paid_amount); //send email
        $type = "success";
        $message = lang('payment_information_send');
        set_message($type, $message);
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('admin/invoice/all_payments');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    function send_payment_email($invoices_id, $paid_amount)
    {
        $inv_info = $this->invoice_model->check_by(array('invoices_id' => $invoices_id), 'tbl_invoices');
        $email_template = email_templates(array('email_group' => 'payment_email'), $inv_info->client_id);
        $message = $email_template->template_body;
        $subject = $email_template->subject;
        if (!empty($inv_info)) {
            $currency = $inv_info->currency;
            $reference = $inv_info->reference_no;

            $invoice_currency = str_replace("{INVOICE_CURRENCY}", $currency, $message);
            $reference = str_replace("{INVOICE_REF}", $reference, $invoice_currency);
            $amount = str_replace("{PAID_AMOUNT}", $paid_amount, $reference);
            $message = str_replace("{SITE_NAME}", config_item('company_name'), $amount);

            $data['message'] = $message;
            $message = $this->load->view('email_template', $data, TRUE);
            $client_info = $this->invoice_model->check_by(array('client_id' => $inv_info->client_id), 'tbl_client');

            // send notification to client
            if (!empty($client_info)) {
                $client_info = $this->invoice_model->check_by(array('client_id' => $client_info->client_id), 'tbl_client');
                if (!empty($client_info->primary_contact)) {
                    $notifyUser = array($client_info->primary_contact);
                } else {
                    $user_info = $this->invoice_model->check_by(array('company' => $client_info->client_id), 'tbl_account_details');
                    if (!empty($user_info)) {
                        $notifyUser = array($user_info->user_id);
                    }
                }
            }
            if (!empty($notifyUser)) {
                foreach ($notifyUser as $v_user) {
                    if ($v_user != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $v_user,
                            'icon' => 'shopping-cart',
                            'description' => 'not_payment_received',
                            'link' => 'client/invoice/manage_invoice/invoice_details/' . $invoices_id,
                            'value' => lang('invoice') . ' ' . $inv_info->reference_no . ' ' . lang('amount') . display_money($paid_amount, $inv_info->currency),
                        ));
                    }
                }
                show_notification($notifyUser);
            }

            $address = $client_info->email;

            $params['recipient'] = $address;

            $params['subject'] = '[ ' . config_item('company_name') . ' ]' . ' ' . $subject;
            $params['message'] = $message;
            $params['resourceed_file'] = '';

            $activity = array(
                'user' => my_id(),
                'module' => 'invoice',
                'module_field_id' => $invoices_id,
                'activity' => ('activity_send_payment'),
                'icon' => 'fa-shopping-cart',
                'link' => 'admin/invoice/manage_invoice/invoice_details/' . $invoices_id,
                'value1' => $reference,
                'value2' => $currency . ' ' . $amount,
            );
            $this->invoice_model->_table_name = 'tbl_activities';
            $this->invoice_model->_primary_key = 'activities_id';
            $this->invoice_model->save($activity);
            $this->invoice_model->send_email($params);
        } else {
            return true;
        }
    }

    public
    function change_invoice_status($action, $id)
    {
        if ($action == 'mark_as_sent') {
            $data = array('emailed' => 'Yes', 'date_sent' => date("Y-m-d H:i:s", time()), 'status' => 'Unpaid');
        }
        if ($action == 'mark_as_cancelled') {
            $data = array('status' => 'Cancelled');
        }
        if ($action == 'unmark_as_cancelled') {
            $data = array('status' => 'Unpaid');
        }
        $this->invoice_model->_table_name = 'tbl_invoices';
        $this->invoice_model->_primary_key = 'invoices_id';
        $this->invoice_model->save($data, $id);

        // messages for user
        $type = "success";
        $imessage = lang('invoice_update');
        set_message($type, $imessage);
        redirect('admin/invoice/manage_invoice/invoice_details/' . $id);
    }

    public
    function send_invoice_email($invoice_id, $row = null)
    {
        if (!empty($row)) {
            $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');
            $client_info = $this->invoice_model->check_by(array('client_id' => $invoice_info->client_id), 'tbl_client');
            if (!empty($client_info)) {
                $client = $client_info->name;
                $currency = $this->invoice_model->client_currency_symbol($client_info->client_id);;
            } else {
                $client = '-';
                $currency = $this->invoice_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
            }

            $amount = $this->invoice_model->calculate_to('invoice_due', $invoice_info->invoices_id);
            $currency = $currency->code;
            $email_template = email_templates(array('email_group' => 'invoice_message'), $invoice_info->client_id);
            $message = $email_template->template_body;
            $ref = $invoice_info->reference_no;
            $subject = $email_template->subject;
            $due_date = $invoice_info->due_date;
        } else {
            $message = $this->input->post('message', TRUE);
            $ref = $this->input->post('ref', TRUE);
            $subject = $this->input->post('subject', TRUE);
            $client = $this->input->post('client_name', TRUE);
            $amount = $this->input->post('amount', true);
            $currency = $this->input->post('currency', TRUE);
            $due_date = $this->input->post('due_date', TRUE);
        }
        $client_name = str_replace("{CLIENT}", $client, $message);
        $Ref = str_replace("{REF}", $ref, $client_name);
        $Amount = str_replace("{AMOUNT}", $amount, $Ref);
        $Currency = str_replace("{CURRENCY}", $currency, $Amount);
        $Due_date = str_replace("{DUE_DATE}", $due_date, $Currency);

        $link = str_replace("{INVOICE_LINK}", base_url() . 'client/invoice/manage_invoice/invoice_details/' . $invoice_id, $Due_date);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $link);

        $this->send_email_invoice($invoice_id, $message, $subject); // Email Invoice

        $data = array('status' => 'sent', 'emailed' => 'Yes', 'date_sent' => date("Y-m-d H:i:s", time()));

        $this->invoice_model->_table_name = 'tbl_invoices';
        $this->invoice_model->_primary_key = 'invoices_id';
        $this->invoice_model->save($data, $invoice_id);

        // Log Activity
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'invoice',
            'module_field_id' => $invoice_id,
            'activity' => ('activity_invoice_sent'),
            'icon' => 'fa-shopping-cart',
            'link' => 'admin/invoice/manage_invoice/invoice_details/' . $invoice_id,
            'value1' => $ref,
            'value2' => $this->input->post('currency', TRUE) . ' ' . $this->input->post('amount'),
        );
        $this->invoice_model->_table_name = 'tbl_activities';
        $this->invoice_model->_primary_key = 'activities_id';
        $this->invoice_model->save($activity);
        // messages for user
        $type = "success";
        $imessage = lang('invoice_sent');
        set_message($type, $imessage);
        redirect('admin/invoice/manage_invoice/invoice_details/' . $invoice_id);
    }

    function send_email_invoice($invoice_id, $message, $subject)
    {
        $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');
        $client_info = $this->invoice_model->check_by(array('client_id' => $invoice_info->client_id), 'tbl_client');

        $recipient = $client_info->email;

        $data['message'] = $message;

        $message = $this->load->view('email_template', $data, TRUE);
        $params = array(
            'recipient' => $recipient,
            'subject' => $subject,
            'message' => $message
        );
        $params['resourceed_file'] = 'uploads/' . lang('invoice') . '_' . $invoice_info->reference_no . '.pdf';
        $params['resourcement_url'] = base_url() . 'uploads/' . lang('invoice') . '_' . $invoice_info->reference_no . '.pdf';

        $this->attach_pdf($invoice_id);

        $this->invoice_model->send_email($params);

        $mobile = client_can_received_sms($invoice_info->client_id);
        if (!empty($mobile)) {
            $merge_fields = [];
            $merge_fields = array_merge($merge_fields, merge_invoice_template($invoice_id));
            $merge_fields = array_merge($merge_fields, merge_invoice_template($invoice_id, 'client', $invoice_info->client_id));
            $this->sms->send(SMS_INVOICE_REMINDER, $mobile, $merge_fields);
        }
        //Delete invoice in tmp folder
        if (is_file('uploads/' . lang('invoice') . '_' . $invoice_info->reference_no . '.pdf')) {
            unlink('uploads/' . lang('invoice') . '_' . $invoice_info->reference_no . '.pdf');
        }
        // send notification to client
        if (!empty($client_info->primary_contact)) {
            $notifyUser = array($client_info->primary_contact);
        } else {
            $user_info = $this->invoice_model->check_by(array('company' => $invoice_info->client_id), 'tbl_account_details');
            if (!empty($user_info)) {
                $notifyUser = array($user_info->user_id);
            }
        }
        if (!empty($notifyUser)) {
            foreach ($notifyUser as $v_user) {
                if ($v_user != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $v_user,
                        'icon' => 'shopping-cart',
                        'description' => 'not_email_send_alert',
                        'link' => 'client/invoice/manage_invoice/invoice_details/' . $invoice_id,
                        'value' => lang('invoice') . ' ' . $invoice_info->reference_no,
                    ));
                }
            }
            show_notification($notifyUser);
        }

    }

    public
    function attach_pdf($id)
    {
        $data['page'] = lang('invoices');
        $data['invoice_info'] = $this->invoice_model->check_by(array('invoices_id' => $id), 'tbl_invoices');
        $data['title'] = lang('invoices'); //Page title
        $this->load->helper('dompdf');
        $html = $this->load->view('admin/invoice/invoice_pdf', $data, TRUE);
        $result = pdf_create($html, slug_it(lang('invoice') . '_' . $data['invoice_info']->reference_no), 1, null, true);
        return $result;
    }

    function invoice_email($invoice_id)
    {
        $data['invoice_info'] = $this->invoice_model->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');
        $data['title'] = "Invoice PDF"; //Page title
        $message = $this->load->view('admin/invoice/invoice_pdf', $data, TRUE);

        $client_info = $this->invoice_model->check_by(array('client_id' => $data['invoice_info']->client_id), 'tbl_client');

        $recipient = $client_info->email;

        $data['message'] = $message;

        $message = $this->load->view('email_template', $data, TRUE);

        $params = array(
            'recipient' => $recipient,
            'subject' => '[ ' . config_item('company_name') . ' ]' . ' New Invoice' . ' ' . $data['invoice_info']->reference_no,
            'message' => $message
        );
        $params['resourceed_file'] = '';

        $this->invoice_model->send_email($params);

        $data = array('emailed' => 'Yes', 'date_sent' => date("Y-m-d H:i:s", time()));

        $this->invoice_model->_table_name = 'tbl_invoices';
        $this->invoice_model->_primary_key = 'invoices_id';
        $invoice_id = $this->invoice_model->save($data, $invoice_id);

        $data['invoice_info'] = $this->invoice_model->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');
        // Log Activity
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'invoice',
            'module_field_id' => $invoice_id,
            'activity' => ('activity_invoice_sent'),
            'icon' => 'fa-shopping-cart',
            'link' => 'admin/invoice/manage_invoice/invoice_details/' . $invoice_id,
            'value1' => $data['invoice_info']->reference_no,
        );

        $this->invoice_model->_table_name = 'tbl_activities';
        $this->invoice_model->_primary_key = 'activities_id';
        $this->invoice_model->save($activity);

        // send notification to client
        if (!empty($client_info->primary_contact)) {
            $notifyUser = array($client_info->primary_contact);
        } else {
            $user_info = $this->invoice_model->check_by(array('company' => $data['invoice_info']->client_id), 'tbl_account_details');
            if (!empty($user_info)) {
                $notifyUser = array($user_info->user_id);
            }
        }
        if (!empty($notifyUser)) {
            foreach ($notifyUser as $v_user) {
                if ($v_user != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $v_user,
                        'icon' => 'shopping-cart',
                        'description' => 'not_email_send_alert',
                        'link' => 'client/invoice/manage_invoice/invoice_details/' . $invoice_id,
                        'value' => lang('invoice') . ' ' . $data['invoice_info']->reference_no,
                    ));
                }
            }
            show_notification($notifyUser);
        }
        // messages for user
        $type = "success";
        $imessage = lang('invoice_sent');
        set_message($type, $imessage);
        redirect('admin/invoice/manage_invoice/invoice_details/' . $invoice_id);

    }

    public
    function tax_rates($action = NULL, $id = NULL)
    {
        $edited = can_action('16', 'edited');
        $deleted = can_action('16', 'deleted');
        $data['page'] = lang('sales');
        $data['sub_active'] = lang('tax_rates');
        if ($action == 'edit_tax_rates') {
            $data['active'] = 2;
            if (!empty($id)) {
                $can_edit = $this->invoice_model->can_action('tbl_tax_rates', 'edit', array('tax_rates_id' => $id));
                if (!empty($can_edit) && !empty($edited)) {
                    $data['tax_rates_info'] = $this->invoice_model->check_by(array('tax_rates_id' => $id), 'tbl_tax_rates');
                }
            }
        } else {
            $data['active'] = 1;
        }
        if ($action == 'delete_tax_rates') {
            $can_delete = $this->invoice_model->can_action('tbl_tax_rates', 'delete', array('tax_rates_id' => $id));
            if (!empty($can_delete) && !empty($deleted)) {
                $tax_rates_info = $this->invoice_model->check_by(array('tax_rates_id' => $id), 'tbl_tax_rates');
                // Log Activity
                $activity = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'invoice',
                    'module_field_id' => $id,
                    'activity' => ('activity_taxt_rate_deleted'),
                    'icon' => 'fa-shopping-cart',
                    'value1' => $tax_rates_info->tax_rate_name,
                );
                $this->invoice_model->_table_name = 'tbl_activities';
                $this->invoice_model->_primary_key = 'activities_id';
                $this->invoice_model->save($activity);

                $this->invoice_model->_table_name = 'tbl_tax_rates';
                $this->invoice_model->_primary_key = 'tax_rates_id';
                $this->invoice_model->delete($id);
                // messages for user
                $type = "success";
                $message = lang('tax_deleted');
            } else {
                $type = "error";
                $message = lang('there_in_no_value');
            }
            echo json_encode(array("status" => $type, 'message' => $message));
            exit();
        } else {
            $data['title'] = "Tax Rates Info"; //Page title
            $subview = 'tax_rates';
            // get permission user
            $data['permission_user'] = $this->invoice_model->all_permission_user('16');
            // get all invoice
            $data['all_tax_rates'] = $this->invoice_model->get_permission('tbl_tax_rates');

            $data['subview'] = $this->load->view('admin/invoice/' . $subview, $data, TRUE);
            $this->load->view('admin/_layout_main', $data); //page load
        }
    }

    public function taxList()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_tax_rates';
            $this->datatables->column_order = array('tax_rate_name', 'tax_rate_name');
            $this->datatables->column_search = array('tax_rate_name', 'tax_rate_name');
            $this->datatables->order = array('tax_rates_id' => 'desc');

            // get all invoice
            $fetch_data = $this->datatables->get_datatable_permission();

            $data = array();

            $edited = can_action('16', 'edited');
            $deleted = can_action('16', 'deleted');
            foreach ($fetch_data as $_key => $v_tax_rates) {

                $action = null;
                $can_delete = $this->invoice_model->can_action('tbl_tax_rates', 'delete', array('tax_rates_id' => $v_tax_rates->tax_rates_id));
                $can_edit = $this->invoice_model->can_action('tbl_tax_rates', 'edit', array('tax_rates_id' => $v_tax_rates->tax_rates_id));

                $sub_array = array();

                $sub_array[] = $v_tax_rates->tax_rate_name;
                $sub_array[] = $v_tax_rates->tax_rate_percent . '%';

                if (!empty($can_edit) && !empty($edited)) {
                    $action .= btn_edit('admin/invoice/tax_rates/edit_tax_rates/' . $v_tax_rates->tax_rates_id) . ' ';
                }
                if (!empty($can_delete) && !empty($deleted)) {
                    $action .= ajax_anchor(base_url("admin/invoice/tax_rates/delete_tax_rates/$v_tax_rates->tax_rates_id"), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                }

                $sub_array[] = $action;
                $data[] = $sub_array;
            }

            render_table($data);
        } else {
            redirect('admin/dashboard');
        }
    }

    public
    function save_tax_rate($id = NULL)
    {
        $data = $this->invoice_model->array_from_post(array('tax_rate_name', 'tax_rate_percent'));
        $permission = $this->input->post('permission', true);
        if (!empty($permission)) {
            if ($permission == 'everyone') {
                $assigned = 'all';
            } else {
                $assigned_to = $this->invoice_model->array_from_post(array('assigned_to'));
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
                redirect('admin/invoice/tax_rates');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }

        $this->invoice_model->_table_name = 'tbl_tax_rates';
        $this->invoice_model->_primary_key = 'tax_rates_id';
        $id = $this->invoice_model->save($data, $id);

        // Log Activity
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'invoice',
            'module_field_id' => $id,
            'activity' => ('activity_taxt_rate_add'),
            'icon' => 'fa-shopping-cart',
            'value1' => $data['tax_rate_name'],
        );
        $this->invoice_model->_table_name = 'tbl_activities';
        $this->invoice_model->_primary_key = 'activities_id';
        $this->invoice_model->save($activity);

        // messages for user
        $type = "success";
        $message = lang('tax_added');
        set_message($type, $message);
        $save = $this->input->post('save', true);
        if ($save == 2) {
            redirect('admin/invoice/tax_rates/edit_tax_rates');
        } else {
            redirect('admin/invoice/tax_rates');
        }

    }

    public
    function zipped($module, $client_id = null, $id = null)
    {
        $this->load->helper('dompdf');
        if ($module == 'estimate') {
            $this->load->model('estimates_model');
        } elseif ($module == 'proposal') {
            $this->load->model('proposal_model');
        } elseif ($module == 'credit_note') {
            $this->load->model('credit_note_model');
        }
        if ($this->input->post()) {
            if ($module == 'invoice') {
                $view = can_action('13', 'view');
                if (!$view) {
                    access_denied('Zip Invoices');
                }

                $status = $this->input->post('invoice_status');
                $ex = explode('_', $status);
                if (!empty($ex)) {
                    if (!empty($ex[1]) && is_numeric($ex[1])) {
                        $ex = 'year';
                    } else {
                        $ex = 'no';
                    }
                }

                $client_id = $this->input->post('client_id');
                if (!empty($client_id)) {
                    $client_info = $this->db->where('client_id', $client_id)->get('tbl_client')->row();
                    $file_name = slug_it($client_info->name);
                } else {
                    $file_name = slug_it($status);
                    $client_id = null;
                }

                if ($this->input->post('from_date') && $this->input->post('to_date') && $status != 'last_month' && $status != 'this_months' && $ex != 'year') {
                    $from_date = $this->input->post('from_date', true);
                    $to_date = $this->input->post('to_date', true);
                    if (!empty($client_id)) {
                        $this->db->where('client_id', $client_id);
                    }
                    $this->db->where('invoice_date BETWEEN "' . $from_date . '" AND "' . $to_date . '"');
                    $all_invoice = $this->db->get('tbl_invoices')->result();

                } else {
                    $from_date = null;
                    $to_date = null;
                    $all_invoice = $this->invoice_model->get_invoices($status, $client_id);
                }

                $this->load->helper('file');
                if (!is_really_writable(TEMP_FOLDER)) {
                    show_error('uploads folder is not writable. You need to change the permissions to 755');
                }
                $dir = TEMP_FOLDER . $file_name;

                if (is_dir($dir)) {
                    delete_dir($dir);
                }
                if (empty($all_invoice)) {
                    set_message('error', lang('no_record_available'));
                    if (!empty($client_id)) {
                        redirect('admin/client/client_details/' . $client_id . '/invoice');
                    } else {
                        redirect('admin/invoice/manage_invoice');
                    }
                }

                mkdir($dir, 0777);
                foreach ($all_invoice as $v_invoice) {
                    $data['invoice_info'] = $v_invoice;
                    $pdf_file = $this->load->view('admin/invoice/invoice_pdf', $data, TRUE);
                    $_temp_file_name = slug_it($data['invoice_info']->reference_no);
                    $file_name = $dir . strtoupper($_temp_file_name);
                    if (!empty($client_info->name)) {
                        $cl_name = slug_it($client_info->name);
                    } else {
                        $cl_name = slug_it($status);
                    }
                    pdf_create($pdf_file, slug_it($data['invoice_info']->reference_no), 1, null, true, $cl_name);
                }
            } else if ($module == 'estimate') {
                $view = can_action('14', 'view');
                if (!$view) {
                    access_denied('Zip Estimate');
                }

                $status = $this->input->post('invoice_status', true);
                $ex = explode('_', $status);
                if (!empty($ex)) {
                    if (!empty($ex[1]) && is_numeric($ex[1])) {
                        $ex = 'year';
                    } else {
                        $ex = 'no';
                    }
                }

                $client_id = $this->input->post('client_id', true);
                if (!empty($client_id)) {
                    $client_info = $this->db->where('client_id', $client_id)->get('tbl_client')->row();
                    $file_name = slug_it($client_info->name);
                } else {
                    $file_name = lang($status);
                    $client_id = null;
                }
                if ($this->input->post('from_date') && $this->input->post('to_date') && $status != 'last_month' && $status != 'this_months' && $ex != 'year') {
                    $from_date = $this->input->post('from_date', true);
                    $to_date = $this->input->post('to_date', true);
                    if (!empty($client_id)) {
                        $this->db->where('client_id', $client_id);
                    }
                    $this->db->where('estimate_date BETWEEN "' . $from_date . '" AND "' . $to_date . '"');
                    $all_estimate = $this->db->get('tbl_estimates')->result();

                } else {
                    $from_date = null;
                    $to_date = null;
                    $this->load->model('estimates_model');
                    $all_estimate = $this->estimates_model->get_estimates($status, $client_id);
                }

                $this->load->helper('file');
                if (!is_really_writable(TEMP_FOLDER)) {
                    show_error('uploads folder is not writable. You need to change the permissions to 755');
                }
                $dir = TEMP_FOLDER . $file_name;

                if (is_dir($dir)) {
                    delete_dir($dir);
                }
                if (empty($all_estimate)) {
                    set_message('error', lang('no_record_available'));
                    if (!empty($client_id)) {
                        redirect('admin/client/client_details/' . $client_id . '/estimate');
                    } else {
                        redirect('admin/estimates');
                    }
                }
                mkdir($dir, 0777);
                foreach ($all_estimate as $v_estimate) {
                    $data['estimates_info'] = $v_estimate;
                    $pdf_file = $this->load->view('admin/estimates/estimates_pdf', $data, TRUE);
                    $_temp_file_name = slug_it($data['estimates_info']->reference_no);
                    $file_name = $dir . strtoupper($_temp_file_name);
                    if (!empty($client_info->name)) {
                        $cl_name = slug_it($client_info->name);
                    } else {
                        $cl_name = slug_it($status);
                    }
                    pdf_create($pdf_file, slug_it($data['estimates_info']->reference_no), 1, null, true, $cl_name);
                }
            } else if ($module == 'credit_note') {
                $view = can_action('14', 'view');
                if (!$view) {
                    access_denied('Zip Credit Notes');
                }
                $status = $this->input->post('invoice_status', true);
                $ex = explode('_', $status);
                if (!empty($ex)) {
                    if (!empty($ex[1]) && is_numeric($ex[1])) {
                        $ex = 'year';
                    } else {
                        $ex = 'no';
                    }
                }

                $client_id = $this->input->post('client_id', true);
                if (!empty($client_id)) {
                    $client_info = $this->db->where('client_id', $client_id)->get('tbl_client')->row();
                    $file_name = slug_it($client_info->name);
                } else {
                    $file_name = lang($status);
                    $client_id = null;
                }
                if ($this->input->post('from_date') && $this->input->post('to_date') && $status != 'last_month' && $status != 'this_months' && $ex != 'year') {
                    $from_date = $this->input->post('from_date', true);
                    $to_date = $this->input->post('to_date', true);
                    if (!empty($client_id)) {
                        $this->db->where('client_id', $client_id);
                    }
                    $this->db->where('credit_note_date BETWEEN "' . $from_date . '" AND "' . $to_date . '"');
                    $all_credit_note = $this->db->get('tbl_credit_note')->result();

                } else {
                    $from_date = null;
                    $to_date = null;
                    $this->load->model('credit_note_model');
                    $all_credit_note = $this->credit_note_model->get_credit_note($status, $client_id);
                }

                $this->load->helper('file');
                if (!is_really_writable(TEMP_FOLDER)) {
                    show_error('uploads folder is not writable. You need to change the permissions to 755');
                }
                $dir = TEMP_FOLDER . $file_name;

                if (is_dir($dir)) {
                    delete_dir($dir);
                }
                if (empty($all_credit_note)) {
                    set_message('error', lang('no_record_available'));
                    if (!empty($client_id)) {
                        redirect('admin/client/client_details/' . $client_id . '/credit_note');
                    } else {
                        redirect('admin/credit_note');
                    }
                }
                mkdir($dir, 0777);
                foreach ($all_credit_note as $v_credit_note) {
                    $data['credit_note_info'] = $v_credit_note;
                    $pdf_file = $this->load->view('admin/credit_note/credit_note_pdf', $data, TRUE);
                    $_temp_file_name = slug_it($data['credit_note_info']->reference_no);
                    $file_name = $dir . strtoupper($_temp_file_name);
                    if (!empty($client_info->name)) {
                        $cl_name = slug_it($client_info->name);
                    } else {
                        $cl_name = slug_it($status);
                    }
                    pdf_create($pdf_file, slug_it($data['credit_note_info']->reference_no), 1, null, true, $cl_name);
                }
            } else if ($module == 'proposal') {
                $view = can_action('140', 'view');
                if (!$view) {
                    access_denied('Zip Proposal');
                }

                $status = $this->input->post('invoice_status', true);
                $ex = explode('_', $status);
                if (!empty($ex)) {
                    if (!empty($ex[1]) && is_numeric($ex[1])) {
                        $ex = 'year';
                    } else {
                        $ex = 'no';
                    }
                }
                $client_id = $this->input->post('client_id', true);
                if (!empty($client_id)) {
                    $client_info = $this->db->where('client_id', $client_id)->get('tbl_client')->row();
                    $file_name = slug_it($client_info->name);
                } else {
                    $file_name = slug_it($status);
                    $client_id = null;
                }
                if ($this->input->post('from_date') && $this->input->post('to_date') && $status != 'last_month' && $status != 'this_months' && $ex != 'year') {
                    $from_date = $this->input->post('from_date', true);
                    $to_date = $this->input->post('to_date', true);
                    if (!empty($client_id)) {
                        $this->db->where('module', 'client');
                        $this->db->where('module_id', $client_id);
                    }
                    $this->db->where('proposal_date BETWEEN "' . $from_date . '" AND "' . $to_date . '"');
                    $all_proposal = $this->db->get('tbl_proposals')->result();
                } else {
                    $from_date = null;
                    $to_date = null;
                    $this->load->model('proposal_model');
                    $all_proposal = $this->proposal_model->get_proposals($status, $client_id);
                }

                $this->load->helper('file');
                if (!is_really_writable(TEMP_FOLDER)) {
                    show_error('uploads folder is not writable. You need to change the permissions to 755');
                }
                $dir = TEMP_FOLDER . $file_name;
                if (is_dir($dir)) {
                    delete_dir($dir);
                }
                if (empty($all_proposal)) {
                    set_message('error', lang('no_record_available'));
                    if (!empty($client_id)) {
                        redirect('admin/client/client_details/' . $client_id . '/proposal');
                    } else {
                        redirect('admin/proposals');
                    }
                }
                mkdir($dir, 0777);
                foreach ($all_proposal as $v_proposal) {
                    $data['proposals_info'] = $v_proposal;
                    $pdf_file = $this->load->view('admin/proposals/proposals_pdf', $data, TRUE);
                    $_temp_file_name = slug_it($data['proposals_info']->reference_no);
                    $file_name = $dir . strtoupper($_temp_file_name);
                    if (!empty($client_info->name)) {
                        $cl_name = slug_it($client_info->name);
                    } else {
                        $cl_name = slug_it($status);
                    }
                    pdf_create($pdf_file, slug_it($data['proposals_info']->reference_no), 1, null, true, $cl_name);
                }
            } else if ($module == 'payment') {
                $view = can_action('15', 'view');
                if (!$view) {
                    access_denied('Zip Payment');
                }

                $status = $this->input->post('invoice_status', true);
                $ex = explode('_', $status);
                if (!empty($ex)) {
                    if (!empty($ex[1]) && is_numeric($ex[1])) {
                        $ex = 'year';
                    } else {
                        $ex = 'no';
                    }
                }
                $client_id = $this->input->post('client_id', true);
                if (!empty($client_id)) {
                    $client_info = $this->db->where('client_id', $client_id)->get('tbl_client')->row();
                    $file_name = slug_it($client_info->name);
                } else {
                    $file_name = slug_it($status);
                    $client_id = null;
                }
                if ($this->input->post('from_date') && $this->input->post('to_date') && $status != 'last_month' && $status != 'this_months' && $ex != 'year') {
                    $from_date = $this->input->post('from_date', true);
                    $to_date = $this->input->post('to_date', true);
                    if (!empty($client_id)) {
                        $this->db->where('paid_by', $client_id);
                    }
                    $this->db->where('payment_date BETWEEN "' . $from_date . '" AND "' . $to_date . '"');
                    $all_payments = $this->db->get('tbl_payments')->result();

                } else {
                    $from_date = null;
                    $to_date = null;
                    $all_payments = $this->invoice_model->get_payments($status, $client_id);
                }
                $this->load->helper('file');
                if (!is_really_writable(TEMP_FOLDER)) {
                    show_error('uploads folder is not writable. You need to change the permissions to 755');
                }
                $dir = TEMP_FOLDER . $file_name;

                if (is_dir($dir)) {
                    delete_dir($dir);
                }
                if (empty($all_payments)) {
                    set_message('error', lang('no_record_available'));
                    if (!empty($client_id)) {
                        redirect('admin/client/client_details/' . $client_id . '/payment');
                    } else {
                        redirect('admin/invoice/all_payments');
                    }
                }
                mkdir($dir, 0777);
                foreach ($all_payments as $v_payment) {
                    $data['payments_info'] = $v_payment;
                    $pdf_file = $this->load->view('admin/invoice/payments_pdf', $data, TRUE);
                    $_temp_file_name = slug_it($data['payments_info']->trans_id);
                    $file_name = $dir . strtoupper($_temp_file_name);
                    if (!empty($client_info->name)) {
                        $cl_name = slug_it($client_info->name);
                    } else {
                        $cl_name = slug_it($status);
                    }
                    pdf_create($pdf_file, slug_it($data['payments_info']->trans_id), 1, null, true, $cl_name);
                }
            }

            $this->load->library('zip');
            // Read the invoices
            $this->zip->read_dir($dir, false);
            // Delete the temp directory for the client
            delete_dir($dir);
            if (!empty($client_info->name)) {
                $cl_name = slug_it($client_info->name);
            } else {
                $cl_name = slug_it($status);
            }
            $this->zip->download($module . '-' . $cl_name . '.zip');
            $this->zip->clear_data();
        } else {
            $data['title'] = lang('zip_' . $module);
            $data['client_id'] = $client_id;
            $data['module'] = $module;
            $data['subview'] = $this->load->view('admin/invoice/zipped', $data, FALSE);
            $this->load->view('admin/_layout_modal', $data);
        }
    }

    public
    function reminder($module, $module_id, $id = null)
    {
        $data['title'] = lang('reminder') . ' ' . lang('list');
        if ($this->input->post()) {
            $r_data['date'] = $this->input->post('date', true);
            $r_data['module'] = $module;
            $r_data['module_id'] = $module_id;
            $r_data['user_id'] = $this->input->post('user_id', true);
            $r_data['description'] = $this->input->post('description', true);
            $notify_by_email = $this->input->post('notify_by_email', true);
            if (empty($notify_by_email)) {
                $notify_by_email = 'No';
            } else {
                $notify_by_email = 'Yes';
            }
            $r_data['notify_by_email'] = $notify_by_email;
            $r_data['created_by'] = $this->session->userdata('user_id');
            $this->invoice_model->_table_name = 'tbl_reminders';
            $this->invoice_model->_primary_key = 'reminder_id';
            $this->invoice_model->save($r_data, $id);
            if ($module == 'client') {
                $url = 'admin/client/client_details/' . $module_id;
            } elseif ($module == 'invoice') {
                $url = 'admin/invoice/manage_invoice/invoice_details/' . $module_id;
            } elseif ($module == 'estimate') {
                $url = 'admin/estimates/index/estimates_details/' . $module_id;
            } elseif ($module == 'proposal') {
                $url = 'admin/proposals/index/proposals_details/' . $module_id;
            } else if ($module == 'leads') {
                $url = 'admin/leads/leads_details/' . $module_id;
            } else {
                $url = '#';
            }
            // Log Activity
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => $module,
                'module_field_id' => $module_id,
                'activity' => ('activity_added_reminder'),
                'icon' => 'fa-shopping-cart',
                'link' => $url,
                'value1' => $r_data['description'],
            );
            $this->invoice_model->_table_name = 'tbl_activities';
            $this->invoice_model->_primary_key = 'activities_id';
            $this->invoice_model->save($activity);

            $type = "success";
            $message = lang('update_reminder');
            set_message($type, $message);

            if ($module == 'invoice') {
                redirect('admin/invoice/manage_invoice/invoice_details/' . $module_id);
            } else if ($module == 'estimate') {
                redirect('admin/estimates/index/estimates_details/' . $module_id);
            } else if ($module == 'proposal') {
                redirect('admin/proposals/index/proposals_details/' . $module_id);
            } else if ($module == 'client') {
                redirect('admin/client/client_details/' . $module_id);
            } else if ($module == 'leads') {
                redirect('admin/leads/leads_details/' . $module_id);
            } else {
                if (empty($_SERVER['HTTP_REFERER'])) {
                    redirect('admin/dashboard');
                } else {
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }


        } else {
            if (!empty($id)) {
                $data['active'] = 2;
                $data['reminder_info'] = $this->db->where('reminder_id', $id)->get('tbl_reminders')->row();
            } else {
                $data['active'] = 1;
            }
            $data['all_reminder'] = $this->db->where(array('module' => $module, 'module_id' => $module_id))->get('tbl_reminders')->result();

            $data['module_id'] = $module_id;
            $data['module'] = $module;
            $data['subview'] = $this->load->view('admin/invoice/reminder', $data, FALSE);
            $this->load->view('admin/_layout_modal', $data);
        }
    }

    public
    function delete_reminder($module, $module_id, $id = null)
    {
        $reminder_info = $this->db->where('reminder_id', $id)->get('tbl_reminders')->row();

        if ($module == 'client') {
            $url = 'admin/client/client_details/' . $module_id;
        } elseif ($module == 'invoice') {
            $url = 'admin/invoice/manage_invoice/invoice_details/' . $module_id;
        } elseif ($module == 'estimate') {
            $url = 'admin/estimates/index/estimates_details/' . $module_id;
        } elseif ($module == 'proposal') {
            $url = 'admin/proposals/index/proposals_details/' . $module_id;
        } else if ($module == 'leads') {
            $url = 'admin/leads/leads_details/' . $module_id;
        } else {
            $url = '#';
        }
        // Log Activity
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => $module,
            'module_field_id' => $module_id,
            'activity' => ('activity_delete_reminder'),
            'icon' => 'fa-shopping-cart',
            'link' => $url,
            'value1' => $reminder_info->description,
        );
        $this->invoice_model->_table_name = 'tbl_activities';
        $this->invoice_model->_primary_key = 'activities_id';
        $this->invoice_model->save($activity);

        $this->invoice_model->_table_name = 'tbl_reminders';
        $this->invoice_model->_primary_key = 'reminder_id';
        $this->invoice_model->delete($id);

        echo json_encode(array("status" => 'success', 'message' => lang('delete_reminder')));
        exit();
    }

}
