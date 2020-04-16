<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Invoice extends Client_Controller
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
        $data['page'] = lang('invoices');
        $data['title'] = lang('invoices');
        $data['breadcrumbs'] = lang('invoices');
        if ($action == 'all_payments') {
            $data['sub_active'] = lang('payments_received');
        } else {
            $data['sub_active'] = lang('invoice');
        }
        if (!empty($item_id)) {
            $data['item_info'] = $this->invoice_model->check_by(array('items_id' => $item_id), 'tbl_items');
        }

        if (!empty($id) && $action != 'payments_details') {
            // get all invoice info by id
            $data['invoice_info'] = $this->invoice_model->check_by(array('invoices_id' => $id), 'tbl_invoices');
            if (empty($data['invoice_info'])) {
                redirect('client/invoice/manage_invoice');
            }
            $client_id = client_id();
            if ($client_id != $data['invoice_info']->client_id) {
                redirect('client/invoice/manage_invoice');
            }
        }
        if ($action == 'create_invoice') {
            $data['active'] = 2;
        } else {
            $data['active'] = 1;
        }
        $user_id = $this->session->userdata('user_id');
        $client_id = $this->session->userdata('client_id');
        // get all client
        $this->invoice_model->_table_name = 'tbl_client';
        $this->invoice_model->_order_by = 'client_id';
        $data['all_client'] = $this->invoice_model->get();

        // get all client
        $data['all_invoices_info'] = $this->db->where(array('client_id' => $client_id))->get('tbl_invoices')->result();

        if ($action == 'invoice_details') {
            $data['title'] = "Invoice Details"; //Page title
            if (empty($data['invoice_info']) || $data['invoice_info']->show_client == 'No') {
                set_message('error', 'No data Found');
                redirect('client/invoice/manage_invoice');
            }
            $subview = 'invoice_details';
        } elseif ($action == 'payment') {
            $data['title'] = "Invoice Payment"; //Page title
            $subview = 'payment';
        } elseif ($action == 'payments_details') {
            $data['page'] = lang('payments');
            $data['title'] = "Payments Details"; //Page title
            $subview = 'payments_details';
            // get payment info
            $this->invoice_model->_table_name = 'tbl_payments';
            $this->invoice_model->_order_by = 'invoices_id';
            $data['all_payments_info'] = $this->invoice_model->get_by(array('invoices_id !=' => '0', 'paid_by' => $this->session->userdata('client_id')), FALSE);
            // get payment info by id
            $this->invoice_model->_table_name = 'tbl_payments';
            $this->invoice_model->_order_by = 'payments_id';
            $data['payments_info'] = $this->invoice_model->get_by(array('payments_id' => $id), TRUE);

            $client_id = client_id();
            if ($client_id != $data['payments_info']->paid_by) {
                redirect('client/invoice/all_payments');
            }
        } elseif ($action == 'invoice_history') {
            $data['title'] = "Invoice History"; //Page title
            $subview = 'invoice_history';
        } elseif ($action == 'email_invoice') {
            $data['title'] = "Email Invoice"; //Page title
            $subview = 'email_invoice';
            $data['editor'] = $this->data;
        } elseif ($action == 'send_reminder') {
            $data['title'] = "Send Remainder"; //Page title
            $subview = 'send_reminder';
            $data['editor'] = $this->data;
        } else {
            $data['title'] = lang('invoices');
            $subview = 'manage_invoice';
        }
        $user_info = $this->invoice_model->check_by(array('user_id' => $user_id), 'tbl_users');
        $data['role'] = $user_info->role_id;
        $data['subview'] = $this->load->view('client/invoice/' . $subview, $data, TRUE);
        $this->load->view('client/_layout_main', $data); //page load
    }

    public function invoiceList($filterBy = null)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_invoices';
            $this->datatables->column_order = array('reference_no', 'status', 'invoice_date', 'due_date');
            $this->datatables->column_search = array('reference_no', 'status', 'invoice_date', 'due_date');
            $this->datatables->order = array('invoices_id' => 'desc');
            $where_in = null;
            $client_id = $this->session->userdata('client_id');
            if (empty($filterBy)) {
                $where = array('show_client' => 'Yes', 'client_id' => $client_id, 'status !=' => 'draft');
            }
            if ($filterBy == 'recurring') {
                $where = array('show_client' => 'Yes', 'client_id' => $client_id, 'status !=' => 'draft', 'recurring' => 'Yes');
            } else if ($filterBy == 'paid') {
                $where = array('show_client' => 'Yes', 'client_id' => $client_id, 'status' => 'Paid');
            } else if ($filterBy == 'not_paid') {
                $where = array('show_client' => 'Yes', 'client_id' => $client_id, 'status' => 'Unpaid');
            } else if ($filterBy == 'partially_paid') {
                $where = array('show_client' => 'Yes', 'client_id' => $client_id, 'status' => 'partially_paid');
            } else if ($filterBy == 'cancelled') {
                $where = array('show_client' => 'Yes', 'client_id' => $client_id, 'status' => 'Cancelled');
            } else if ($filterBy == 'overdue') {
                $where = array('show_client' => 'Yes', 'client_id' => $client_id, 'UNIX_TIMESTAMP(due_date) <' => strtotime(date('Y-m-d')));
                $status = array('partially_paid', 'Unpaid', 'Cancelled');
                $where_in = array('status', $status);
            } else if ($filterBy == 'last_month' || $filterBy == 'this_months') {
                if ($filterBy == 'last_month') {
                    $month = date('Y-m', strtotime('-1 months'));
                } else {
                    $month = date('Y-m');
                }
                $where = array('show_client' => 'Yes', 'client_id' => $client_id, 'status !=' => 'draft', 'invoice_month' => $month);
            }
            // get all invoice
            $fetch_data = $this->datatables->get_client_invoices($filterBy);

            $data = array();
            foreach ($fetch_data as $_key => $v_invoices) {
                $action = null;

                if ($this->invoice_model->get_payment_status($v_invoices->invoices_id) == lang('fully_paid')) {
                    $invoice_status = lang('fully_paid');
                    $label = "success";
                } elseif ($this->invoice_model->get_payment_status($v_invoices->invoices_id) == lang('draft')) {
                    $invoice_status = lang('draft');
                    $label = "default";
                } elseif ($this->invoice_model->get_payment_status($v_invoices->invoices_id) == lang('partially_paid')) {
                    $invoice_status = lang('partially_paid');
                    $label = "warning";
                } elseif ($v_invoices->status != 'Cancelled' && $v_invoices->emailed == 'Yes') {
                    $invoice_status = lang('sent');
                    $label = "info";
                } else {
                    $invoice_status = $this->invoice_model->get_payment_status($v_invoices->invoices_id);
                    $label = "danger";
                }

                $sub_array = array();
                $name = null;
                $name .= '<a class="text-info" href="' . base_url() . 'client/invoice/manage_invoice/invoice_details/' . $v_invoices->invoices_id . '">' . $v_invoices->reference_no . '</a>';
                $sub_array[] = $name;
                $payment_status = $this->invoice_model->get_payment_status($v_invoices->invoices_id);
                $overdue = null;
                if (strtotime($v_invoices->due_date) < strtotime(date('Y-m-d')) && $payment_status != lang('fully_paid')) {
                    $overdue .= '<span class="label label-danger ">' . lang("overdue") . '</span>';
                }
                $sub_array[] = strftime(config_item('date_format'), strtotime($v_invoices->due_date)) . ' ' . $overdue;
                $sub_array[] = display_money($this->invoice_model->calculate_to('total', $v_invoices->invoices_id), client_currency($v_invoices->client_id));
                $sub_array[] = display_money($this->invoice_model->calculate_to('invoice_due', $v_invoices->invoices_id), client_currency($v_invoices->client_id));
                $recurring = null;
                if ($v_invoices->recurring == 'Yes') {
                    $recurring = '<span data-toggle="tooltip" data-placement="top"
                                                              title="' . lang("recurring") . '"
                                                              class="label label-primary"><i
                                                                class="fa fa-retweet"></i></span>';
                }
                $sub_array[] = "<span class='label label-" . $label . "'>" . $invoice_status . "</span>" . ' ' . $recurring;;

                $data[] = $sub_array;
            }
            render_table($data, $where, $where_in);
        } else {
            redirect('client/dashboard');
        }
    }

    public function refund_itemslist($id = NULL)
    {
        $data['page'] = lang('invoices');
        $data['title'] = lang('invoices');
        $data['breadcrumbs'] = lang('refund') . ' ' . lang('items');
        $data['subview'] = $this->load->view('client/invoice/refund_itemslist', $data, TRUE);
        $this->load->view('client/_layout_main', $data); //page load
    }

    public function RefundList()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->load->model('return_stock_model');
            $this->datatables->table = 'tbl_return_stock';
            $this->datatables->column_order = array('reference_no', 'return_stock_date', 'due_date', 'status', 'amount');
            $this->datatables->column_search = array('reference_no', 'return_stock_date', 'due_date', 'status', 'amount');
            $this->datatables->order = array('return_stock_id' => 'desc');
            $client_id = $this->session->userdata('client_id');
            $where = array('module' => 'client', 'module_id' => $client_id);

            $fetch_data = make_datatables($where);

            $data = array();
            foreach ($fetch_data as $_key => $v_return_stock) {
                if (!empty($v_return_stock)) {
                    $action = null;
                    $sub_array = array();
                    $currency = $this->return_stock_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');

                    $sub_array[] = '<a href="' . base_url() . 'client/invoice/return_stock_details/' . $v_return_stock->return_stock_id . '">' . ($v_return_stock->reference_no) . '</a>';

                    $sub_array[] = display_date($v_return_stock->return_stock_date);
                    $sub_array[] = display_money($this->return_stock_model->calculate_to('return_stock_due', $v_return_stock->return_stock_id), $currency->symbol);
                    $status = $this->return_stock_model->get_payment_status($v_return_stock->return_stock_id);
                    if ($status == ('fully_paid')) {
                        $bg = "success";
                    } elseif ($status == ('partially_paid')) {
                        $bg = "warning";
                    } elseif ($v_return_stock->emailed == 'Yes') {
                        $bg = "info";
                    } else {
                        $bg = "danger";
                    }
                    $sub_array[] = '<span class="badge bg-' . $bg . '">' . lang($status) . '</span>';
                    if ($v_return_stock->status == 'Pending' && $v_return_stock->created_by == my_id()) {
                        $action .= ajax_anchor(base_url("client/invoice/delete_return_stock/" . $v_return_stock->return_stock_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                    }
                    $action .= btn_view('client/invoice/return_stock_details/' . $v_return_stock->return_stock_id) . ' ';
                    $sub_array[] = $action;
                    $data[] = $sub_array;
                }
            }
            render_table($data, $where);
        } else {
            redirect('client/dashboard');
        }
    }

    public function delete_return_stock($id)
    {
        $this->load->model('return_stock_model');
        $return_stock_info = $this->return_stock_model->check_by(array('return_stock_id' => $id), 'tbl_return_stock');
        if (!empty($return_stock_info) && $return_stock_info->status == 'Pending') {
            $this->return_stock_model->_table_name = 'tbl_return_stock_items';
            $this->return_stock_model->delete_multiple(array('return_stock_id' => $id));

            $this->return_stock_model->_table_name = 'tbl_return_stock_payments';
            $this->return_stock_model->delete_multiple(array('return_stock_id' => $id));

            $this->return_stock_model->_table_name = 'tbl_return_stock';
            $this->return_stock_model->_primary_key = 'return_stock_id';
            $this->return_stock_model->delete($id);

            $type = "success";
            if (!empty($return_stock_info->reference_no)) {
                $val = $return_stock_info->reference_no;
            } else {
                $val = NULL;
            }
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'return_stock',
                'module_field_id' => $id,
                'activity' => ('activity_delete_return_stock'),
                'icon' => 'fa fa-truck',
                'value1' => $val,

            );
            $this->return_stock_model->_table_name = 'tbl_activities';
            $this->return_stock_model->_primary_key = 'activities_id';
            $this->return_stock_model->save($activity);

            echo json_encode(array("status" => $type, 'message' => lang('activity_delete_return_stock')));
            exit();
        } else {
            echo json_encode(array("status" => 'error', 'message' => lang('there_in_no_value')));
            exit();
        }
    }

    public function pdf_invoice($id)
    {
        $data['title'] = "Invoice PDF"; //Page title
        $data['invoice_info'] = $this->invoice_model->check_by(array('invoices_id' => $id), 'tbl_invoices');
        if (empty($data['invoice_info'])) {
            redirect('client/invoice/manage_invoice');
        }
        $client_id = client_id();
        if ($client_id != $data['invoice_info']->client_id) {
            redirect('client/invoice/manage_invoice');
        }
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('client/invoice/invoice_pdf', $data, TRUE);
        pdf_create($viewfile, 'Invoice  # ' . $data['invoice_info']->reference_no);
    }

    public function payments_pdf($id)
    {
        $data['title'] = "Payments PDF"; //Page title
        // get payment info by id
        $this->invoice_model->_table_name = 'tbl_payments';
        $this->invoice_model->_order_by = 'payments_id';
        $data['payments_info'] = $this->invoice_model->get_by(array('payments_id' => $id), TRUE);
        $client_id = client_id();
        if ($client_id != $data['payments_info']->paid_by) {
            redirect('client/invoice/all_payments');
        }
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/invoice/payments_pdf', $data, TRUE);
        pdf_create($viewfile, 'Payment  # ' . $data['payments_info']->trans_id);
    }

    public function all_payments($id = NULL)
    {
        $data['breadcrumbs'] = lang('payments');
        $data['page'] = lang('payments');
        if (!empty($id)) {
            $data['invoice_info'] = $this->invoice_model->check_by(array('invoices_id' => $id), 'tbl_invoices');
            if (empty($data['invoice_info'])) {
                redirect('client/invoice/manage_invoice');
            }
            // get payment info by id
            $this->invoice_model->_table_name = 'tbl_payments';
            $this->invoice_model->_order_by = 'payments_id';
            $data['payments_info'] = $this->invoice_model->get_by(array('payments_id' => $id), TRUE);

            $data['title'] = "Edit Payments"; //Page title
            $subview = 'edit_payments';
        } else {
            $data['title'] = "All Payments"; //Page title
            $subview = 'all_payments';
        }
        // get payment info
        $this->invoice_model->_table_name = 'tbl_payments';
        $this->invoice_model->_order_by = 'invoices_id';
        $data['all_payments_info'] = $this->invoice_model->get_by(array('paid_by' => $this->session->userdata('client_id')), FALSE);

        $user_id = $this->session->userdata('user_id');
        $user_info = $this->invoice_model->check_by(array('user_id' => $user_id), 'tbl_users');
        $data['role'] = $user_info->role_id;

        $data['subview'] = $this->load->view('client/invoice/' . $subview, $data, TRUE);
        $this->load->view('client/_layout_main', $data); //page load
    }

    public function save_invoice($id = NULL)
    {

        $data = $this->invoice_model->array_from_post(array('reference_no', 'client_id', 'tax', 'discount'));

        $data['due_date'] = date('Y-m-d', strtotime($this->input->post('due_date', TRUE)));

        $data['notes'] = $this->input->post('notes', TRUE);

        $data['allow_paypal'] = $this->input->post('allow_paypal', TRUE) == 'on' ? 'Yes' : 'No';
        $data['allow_2checkout'] = $this->input->post('allow_2checkout', TRUE) == 'on' ? 'Yes' : 'No';
        $data['allow_stripe'] = $this->input->post('allow_stripe', TRUE) == 'on' ? 'Yes' : 'No';
        $data['allow_bitcoin'] = $this->input->post('allow_bitcoin', TRUE) == 'on' ? 'Yes' : 'No';

        $currency = $this->invoice_model->client_currency_symbol($data['client_id']);
        $data['currency'] = $currency->code;

        // get all client
        $this->invoice_model->_table_name = 'tbl_invoices';
        $this->invoice_model->_primary_key = 'invoices_id';
        if (!empty($id)) {
            $invoice_id = $id;
            $this->invoice_model->save($data, $id);
        } else {
            $invoice_id = $this->invoice_model->save($data);
        }

        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'invoice',
            'module_field_id' => $invoice_id,
            'activity' => 'activity_invoice_created',
            'icon' => 'fa-circle-o',
            'value1' => $data['reference_no']
        );
        $this->invoice_model->_table_name = 'tbl_activities';
        $this->invoice_model->_primary_key = 'activities_id';
        $this->invoice_model->save($activity);

        // messages for user
        $type = "success";
        $message = lang('invoice_created');
        set_message($type, $message);
        redirect('client/invoice/manage_invoice');
    }

    public function add_item($id = NULL)
    {

        $data = $this->invoice_model->array_from_post(array('invoices_id', 'item_order'));
        $quantity = $this->input->post('quantity', TRUE);
        $array_data = $this->invoice_model->array_from_post(array('item_name', 'item_desc', 'item_tax_rate', 'unit_cost'));

        foreach ($quantity as $key => $value) {
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
            } else {
                $items_id = $this->invoice_model->save($data);
            }
        }
        $type = "success";
        $message = lang('invoice_item_added');
        set_message($type, $message);
        redirect('client/invoice/manage_invoice/invoice_details/' . $data['invoices_id']);
    }

    public function change_status($action, $id)
    {
        $where = array('invoices_id' => $id);
        if ($action == 'hide') {
            $data = array('show_client' => 'No');
        } else {
            $data = array('show_client' => 'Yes');
        }
        $this->invoice_model->set_action($where, $data, 'tbl_invoices');
        // messages for user
        $type = "success";
        $message = lang('invoice_' . $action);
        set_message($type, $message);
        redirect('client/invoice/manage_invoice/invoice_details/' . $id);
    }

    public function delete($action, $invoices_id, $item_id = NULL)
    {
        if ($action == 'delete_item') {
            $this->invoice_model->_table_name = 'tbl_items';
            $this->invoice_model->_primary_key = 'items_id';
            $this->invoice_model->delete($item_id);
        } elseif ($action == 'delete_invoice') {
            $this->invoice_model->_table_name = 'tbl_items';
            $this->invoice_model->delete_multiple(array('invoices_id' => $invoices_id));

            $this->invoice_model->_table_name = 'tbl_payments';
            $this->invoice_model->delete_multiple(array('invoices_id' => $invoices_id));

            $this->invoice_model->_table_name = 'tbl_invoices';
            $this->invoice_model->_primary_key = 'invoices_id';
            $this->invoice_model->delete($invoices_id);
        } elseif ($action == 'delete_payment') {
            $this->invoice_model->_table_name = 'tbl_payments';
            $this->invoice_model->_primary_key = 'payments_id';
            $this->invoice_model->delete($invoices_id);
        }
        $type = "success";
        if ($action == 'delete_item') {
            $text = lang('invoice_item_deleted');
            set_message($type, $text);
            redirect('client/invoice/manage_invoice/invoice_details/' . $invoices_id);
        } elseif ($action == 'delete_payment') {
            $text = lang('payment_deleted');
            set_message($type, $text);
            redirect('client/invoice/manage_invoice/all_payments');
        } else {
            $text = lang('deleted_invoice');
            set_message($type, $text);
            redirect('client/invoice/manage_invoice');
        }
    }

    public function get_payment($invoices_id)
    {

        $due = round($this->invoice_model->calculate_to('invoice_due', $invoices_id), 2);

        $paid_amount = $this->input->post('amount', TRUE);

        if ($paid_amount != 0) {
            if ($paid_amount > $due) {
                // messages for user
                $type = "error";
                $message = lang('overpaid_amount');
                set_message($type, $message);
                redirect('client/invoice/manage_invoice/payment/' . $invoices_id);
            } else {

                $inv_info = $this->invoice_model->check_by(array('invoices_id' => $invoices_id), 'tbl_invoices');

                $data = array(
                    'invoices_id' => $invoices_id,
                    'paid_by' => $inv_info->client_id,
                    'payment_method' => $this->input->post('payment_method', TRUE),
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
                $this->invoice_model->save($data);

                $activity = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'invoice',
                    'module_field_id' => $invoices_id,
                    'activity' => 'activity_new_payment',
                    'icon' => 'fa-usd',
                    'value1' => display_money($paid_amount, $inv_info->client_id),
                    'value2' => $inv_info->reference_no,
                );
                $this->invoice_model->_table_name = 'tbl_activities';
                $this->invoice_model->_primary_key = 'activities_id';
                $this->invoice_model->save($activity);

                if ($this->input->post('send_thank_you') == 'on') {
                    $this->send_payment_email($invoices_id, $paid_amount); //send thank you email
                }
            }
        }
        // messages for user
        $type = "success";
        $message = lang('generate_payment');
        set_message($type, $message);
        redirect('client/invoice/manage_invoice/invoice_details/' . $invoices_id);
    }

    public function update_payemnt($payments_id)
    {
        $data = array(
            'amount' => $this->input->post('amount', TRUE),
            'payment_method' => $this->input->post('payment_method', TRUE),
            'payment_date' => date('Y-m-d', strtotime($this->input->post('payment_date', TRUE))),
            'notes' => $this->input->post('notes', TRUE),
            'month_paid' => date("m", strtotime($this->input->post('payment_date', TRUE))),
            'year_paid' => date("Y", strtotime($this->input->post('payment_date', TRUE))),
        );
        $this->invoice_model->_table_name = 'tbl_payments';
        $this->invoice_model->_primary_key = 'payments_id';
        $this->invoice_model->save($data, $payments_id);

        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'invoice',
            'module_field_id' => $payments_id,
            'activity' => 'activity_update_payment',
            'icon' => 'fa-usd',
            'value1' => $data['amount'],
            'value2' => $data['payment_date'],
        );
        $this->invoice_model->_table_name = 'tbl_activities';
        $this->invoice_model->_primary_key = 'activities_id';
        $this->invoice_model->save($activity);

        // messages for user
        $type = "success";
        $message = lang('generate_payment');
        set_message($type, $message);
        redirect('client/invoice/manage_invoice/all_payments');
    }

    function send_payment_email($invoices_id, $paid_amount)
    {
        $inv_info = $this->invoice_model->check_by(array('invoices_id' => $invoices_id), 'tbl_invoices');
        $email_template = email_templates(array('email_group' => 'payment_email'), $inv_info->client_id);
        $message = $email_template->template_body;
        $subject = $email_template->subject;

        $currency = $inv_info->currency;
        $reference = $inv_info->reference_no;

        $invoice_currency = str_replace("{INVOICE_CURRENCY}", $currency, $message);
        $reference = str_replace("{INVOICE_REF}", $reference, $invoice_currency);
        $amount = str_replace("{PAID_AMOUNT}", $paid_amount, $reference);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $amount);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);
        $client_info = $this->invoice_model->check_by(array('client_id' => $inv_info->client_id), 'tbl_client');

        $address = $client_info->email;

        $params['recipient'] = $address;

        $params['subject'] = '[ ' . config_item('company_name') . ' ]' . ' ' . $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';
        $this->invoice_model->send_email($params);
    }

    public function send_invoice_email($invoice_id)
    {

        $ref = $this->input->post('ref', TRUE);
        $subject = $this->input->post('subject', TRUE);
        $message = $this->input->post('message', TRUE);

        $client_name = str_replace("{CLIENT}", $this->input->post('client_name', TRUE), $message);
        $Ref = str_replace("{REF}", $ref, $client_name);
        $Amount = str_replace("{AMOUNT}", $this->input->post('amount'), $Ref);
        $Currency = str_replace("{CURRENCY}", $this->input->post('currency', TRUE), $Amount);
        $link = str_replace("{INVOICE_LINK}", base_url() . 'admin/invoice/invoice_details/' . $invoice_id, $Currency);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $link);


        $this->send_email_invoice($invoice_id, $message, $subject); // Email Invoice

        $data = array('emailed' => 'Yes', 'date_sent' => date("Y-m-d H:i:s", time()));

        $this->invoice_model->_table_name = 'tbl_invoices';
        $this->invoice_model->_primary_key = 'invoices_id';
        $this->invoice_model->save($data, $invoice_id);

        // Log Activity
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'invoice',
            'module_field_id' => $invoice_id,
            'activity' => 'activity_invoice_sent',
            'icon' => 'fa-envelope',
            'value1' => $ref
        );
        $this->invoice_model->_table_name = 'tbl_activities';
        $this->invoice_model->_primary_key = 'activities_id';
        $this->invoice_model->save($activity);
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

        $params['resourceed_file'] = '';
        $this->invoice_model->send_email($params);
    }

    public function tax_rates($action = NULL, $id = NULL)
    {

        $data['page'] = lang('sales');
        $data['sub_active'] = lang('tax_rates');
        if ($action == 'edit_tax_rates') {
            $data['active'] = 2;
            if (!empty($id)) {
                $data['tax_rates_info'] = $this->invoice_model->check_by(array('tax_rates_id' => $id), 'tbl_tax_rates');
            }
        } else {
            $data['active'] = 1;
        }
        if ($action == 'delete_tax_rates') {
            $this->invoice_model->_table_name = 'tbl_tax_rates';
            $this->invoice_model->_primary_key = 'tax_rates_id';
            $this->invoice_model->delete($id);
            // messages for user
            $type = "success";
            $message = lang('tax_deleted');
            set_message($type, $message);
            redirect('client/invoice/tax_rates');
        } else {
            $data['title'] = "Tax Rates Info"; //Page title
            $subview = 'tax_rates';
        }
        $user_id = $this->session->userdata('user_id');
        $user_info = $this->invoice_model->check_by(array('user_id' => $user_id), 'tbl_users');
        $data['role'] = $user_info->role_id;

        $data['subview'] = $this->load->view('client/invoice/' . $subview, $data, TRUE);
        $this->load->view('client/_layout_main', $data); //page load
    }

    public function save_tax_rate($id = NULL)
    {
        $data = $this->invoice_model->array_from_post(array('tax_rate_name', 'tax_rate_percent'));

        $this->invoice_model->_table_name = 'tbl_tax_rates';
        $this->invoice_model->_primary_key = 'tax_rates_id';
        $id = $this->invoice_model->save($data, $id);

        // Log Activity
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'invoice',
            'module_field_id' => $id,
            'activity' => 'activity_taxt_rate_set',
            'icon' => 'fa-circle-o',
            'value1' => $data['tax_rate_name'],
        );
        $this->invoice_model->_table_name = 'tbl_activities';
        $this->invoice_model->_primary_key = 'activities_id';
        $this->invoice_model->save($activity);

        // messages for user
        $type = "success";
        $message = lang('tax_added');
        set_message($type, $message);
        redirect('client/invoice/tax_rates');
    }

    public
    function refund_items()
    {
        $data['title'] = lang('refund_items');
        $data['breadcrumbs'] = lang('refund') . ' ' . lang('items');
        $data['all_invoices'] = $this->db->where(array('client_id' => client_id(), 'status !=' => 'Paid'))->get('tbl_invoices')->result();
        $data['modal_subview'] = $this->load->view('client/invoice/refund_items', $data, FALSE);
        $this->load->view('client/_layout_modal', $data);
    }

    public
    function select_item($invoice_id)
    {
        $data['title'] = lang('refund_items');
        $data['breadcrumbs'] = lang('refund_items');
        $data['invoice_info'] = get_row('tbl_invoices', array('invoices_id' => $invoice_id));
        if ($data['invoice_info']->status == 'Paid') {
            set_message('error', lang('there_in_no_value'));
            redirect('client/invoice/refund_itemslist');
        }
        $data['subview'] = $this->load->view('client/invoice/select_refund_items', $data, true);
        $this->load->view('client/_layout_main', $data);
    }

    public function submit_refund_items($invoice_id)
    {

        $this->load->model('return_stock_model');
        $invoice_info = get_row('tbl_invoices', array('invoices_id' => $invoice_id));
        $reference_no = $this->return_stock_model->generate_return_stock_number();
        $data['reference_no'] = $reference_no;
        $data['invoices_id'] = $invoice_id;
        $data['module'] = 'client';
        $data['module_id'] = client_id();
        $data['return_stock_date'] = date('Y-m-d');
        $data['due_date'] = date('Y-m-d', strtotime('+7 days'));
        $data['tax'] = $invoice_info->tax;
        $data['total_tax'] = $invoice_info->total_tax;
        $data['permission'] = $invoice_info->permission;
        $data['discount_type'] = $invoice_info->discount_type;
        $data['discount_percent'] = $invoice_info->discount_percent;
        $data['user_id'] = $invoice_info->user_id;
        $data['main_status'] = 'Pending';
        $data['status'] = 'Pending';
        $data['adjustment'] = $invoice_info->adjustment;
        $data['discount_total'] = $invoice_info->discount_total;
        $data['show_quantity_as'] = $invoice_info->show_quantity_as;
        $data['update_stock'] = 'No';
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

        // get all client
        $this->return_stock_model->_table_name = 'tbl_return_stock';
        $this->return_stock_model->_primary_key = 'return_stock_id';
        if (!empty($id)) {
            $return_stock_id = $id;
            $this->return_stock_model->save($data, $id);
            $action = ('return_stock_updated');
            $msg = lang('return_stock_updated');

        } else {
            $data['created_by'] = my_id();
            $return_stock_id = $this->return_stock_model->save($data);
            $action = ('return_stock_created');
            $msg = lang('return_stock_created');
        }

        $items_data = $this->input->post('items', true);
        if (!empty($items_data)) {
            $index = 0;
            foreach ($items_data as $items) {
                $items['return_stock_id'] = $return_stock_id;
                $items['invoice_items_id'] = $items['items_id'];
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
                $this->return_stock_model->_table_name = 'tbl_return_stock_items';
                $this->return_stock_model->_primary_key = 'items_id';
                $items_id = $this->return_stock_model->save($items);
                $index++;
            }
        }
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'return_stock',
            'module_field_id' => $return_stock_id,
            'activity' => $action,
            'icon' => 'fa fa-truck',
            'link' => 'admin/return_stock/return_stock_details/' . $return_stock_id,
            'value1' => $data['reference_no']
        );
        $this->return_stock_model->_table_name = 'tbl_activities';
        $this->return_stock_model->_primary_key = 'activities_id';
        $this->return_stock_model->save($activity);

        $data['return_stock_id'] = $return_stock_id;
        $this->send_email_return_stock($invoice_id, $data); // Email Invoice


        // messages for user
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('client/invoice/return_stock_details/' . $return_stock_id);
    }

    function send_email_return_stock($invoice_id, $data)
    {
        $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');
        $email_template = email_templates(array('email_group' => 'invoice_item_refund_request'), $invoice_info->client_id);
        $message = $email_template->template_body;
        $subject = $email_template->subject;

        $reference = str_replace("{REF}", $invoice_info->reference_no, $message);
        $amount = str_replace("{INVOICE_LINK}", 'admin/return_stock/return_stock_details/' . $data['return_stock_id'], $reference);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $amount);
        $params['subject'] = '[ ' . config_item('company_name') . ' ]' . ' ' . $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';

        $users = array($data['user_id'], my_department_head($data['user_id']));
        if (!empty($users)) {
            foreach ($users as $v_user) {
                $login_info = $this->invoice_model->check_by(array('user_id' => $v_user), 'tbl_users');
                $params['recipient'] = $login_info->email;
                $this->invoice_model->send_email($params);

                if ($v_user != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $v_user,
                        'icon' => 'shopping-cart',
                        'description' => 'not_new_refund_request_for_invoice',
                        'link' => 'admin/return_stock/return_stock_details/' . $data['return_stock_id'],
                        'value' => $invoice_info->reference_no . ' ' . lang('return_stock') . ' ' . lang('reference_no') . ': ' . $data['reference_no'],
                    ));
                }
            }
            show_notification($users);
        }
        $this->invoice_model->send_email($params);
    }

    public function return_stock_details($id)
    {
        $this->load->model('return_stock_model');
        $data['title'] = lang('return_stock') . ' ' . lang('details'); //Page title
        $data['breadcrumbs'] = lang('return_stock') . ' ' . lang('details');
        $data['return_stock_info'] = $this->invoice_model->check_by(array('return_stock_id' => $id), 'tbl_return_stock');
        if (empty($data['return_stock_info'])) {
            set_message('error', lang('there_in_no_value'));
            redirect('client/return_stock/manage_invoice');
        }
        $data['subview'] = $this->load->view('client/invoice/return_stock_details', $data, TRUE);
        $this->load->view('client/_layout_main', $data); //page load
    }

    public function pdf_return_stock($id)
    {
        $this->load->model('return_stock_model');
        $data['return_stock_info'] = $this->return_stock_model->check_by(array('return_stock_id' => $id), 'tbl_return_stock');
        $data['title'] = lang('return_stock') . ' ' . "PDF"; //Page title
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/return_stock/return_stock_pdf', $data, TRUE);

        pdf_create($viewfile, lang('return_stock') . '# ' . $data['return_stock_info']->reference_no);
    }
}
