<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Return_stock extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('return_stock_model');
    }

    public function index($id = NULL)
    {
        $data['title'] = lang('all') . ' ' . lang('return_stock');
        if (!empty($id)) {
            $data['active'] = 2;
            $edited = can_action('153', 'edited');
            if (!empty($edited) && is_numeric($id)) {
                $data['return_stock_info'] = $this->return_stock_model->check_by(array('return_stock_id' => $id), 'tbl_return_stock');
            }
        } else {
            $data['active'] = 1;
        }
        $data['dropzone'] = true;
        $data['all_return_stocks'] = $this->return_stock_model->get_permission('tbl_return_stock');
        $data['permission_user'] = $this->return_stock_model->all_permission_user('153');
        $data['all_supplier'] = $this->return_stock_model->get_permission('tbl_suppliers');
        $data['subview'] = $this->load->view('admin/return_stock/manage_return_stock', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function return_stockList()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_return_stock';
            $this->datatables->column_order = array('reference_no', 'return_stock_date', 'due_date', 'status', 'amount');
            $this->datatables->column_search = array('reference_no', 'return_stock_date', 'due_date', 'status', 'amount');
            $this->datatables->order = array('return_stock_id' => 'desc');
            $fetch_data = make_datatables();

            $data = array();

            $edited = can_action('153', 'edited');
            $deleted = can_action('153', 'deleted');
            foreach ($fetch_data as $_key => $v_return_stock) {
                if (!empty($v_return_stock)) {
                    $action = null;
                    $sub_array = array();
                    $can_edit = $this->return_stock_model->can_action('tbl_return_stock', 'edit', array('return_stock_id' => $v_return_stock->return_stock_id));
                    $can_delete = $this->return_stock_model->can_action('tbl_return_stock', 'delete', array('return_stock_id' => $v_return_stock->return_stock_id));

                    $currency = $this->return_stock_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');

                    $sub_array[] = '<a href="' . base_url() . 'admin/return_stock/return_stock_details/' . $v_return_stock->return_stock_id . '">' . ($v_return_stock->reference_no) . '</a>';
                    if ($v_return_stock->module == 'client') {
                        $client_info = $this->return_stock_model->check_by(array('client_id' => $v_return_stock->module_id), 'tbl_client');
                    } else if ($v_return_stock->module == 'supplier') {
                        $client_info = $this->return_stock_model->check_by(array('supplier_id' => $v_return_stock->module_id), 'tbl_suppliers');
                    }
                    if (!empty($client_info)) {
                        $client_name = lang($v_return_stock->module) . ': ' . $client_info->name;
                    } else {
                        $client_name = '-';
                    }
                    $sub_array[] = $client_name;
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


                    if (!empty($can_edit) && !empty($edited)) {
                        $action .= btn_edit('admin/return_stock/index/' . $v_return_stock->return_stock_id) . ' ';
                    }
                    if (!empty($can_delete) && !empty($deleted)) {
                        $action .= ajax_anchor(base_url("admin/return_stock/delete_return_stock/" . $v_return_stock->return_stock_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                    }
                    if (!empty($can_edit) && !empty($edited)) {
                        $action .= btn_view('admin/return_stock/return_stock_details/' . $v_return_stock->return_stock_id) . ' ';
                        $action .= '<a class="btn btn-success btn-xs" data-popup="tooltip" data-placement="top" title="Payment" href="' . base_url() . 'admin/return_stock/payment/' . $v_return_stock->return_stock_id . '">' . lang('pay') . '</a>';
                    }
                    $sub_array[] = $action;
                    $data[] = $sub_array;
                }
            }

            render_table($data);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function save_return_stock($id = NULL)
    {
        $data = $this->return_stock_model->array_from_post(array('reference_no', 'discount_type', 'discount_percent', 'user_id', 'adjustment', 'discount_total', 'show_quantity_as'));
        $data['module'] = $this->input->post('module', true);
        if (!empty($data['module']) && $data['module'] == 'supplier') {
            $data['module_id'] = $this->input->post('supplier_id', true);
            $data['invoices_id'] = $this->input->post('purchase_id', true);
        } else {
            $data['module_id'] = $this->input->post('client_id', true);
            $data['invoices_id'] = $this->input->post('invoices_id', true);
        }
        $data['update_stock'] = ($this->input->post('update_stock') == 'Yes') ? 'Yes' : 'No';
        $data['return_stock_date'] = date('Y-m-d', strtotime($this->input->post('return_stock_date', TRUE)));
        if (empty($data['return_stock_date'])) {
            $data['return_stock_date'] = date('Y-m-d');
        }
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
        if (empty($id)) {
            $data['main_status'] = 'Pending';
            if ($data['update_stock'] == 'Yes') {
                $data['main_status'] = 'accepted';
            }
        }
        $permission = $this->input->post('permission', true);
        if (!empty($permission)) {
            if ($permission == 'everyone') {
                $assigned = 'all';
            } else {
                $assigned_to = $this->return_stock_model->array_from_post(array('assigned_to'));
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
            redirect($_SERVER['HTTP_REFERER']);
        }
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

        $removed_items = $this->input->post('removed_items', TRUE);
        if (!empty($removed_items)) {
            foreach ($removed_items as $r_id) {
                if ($r_id != 'undefined') {
                    $this->return_items($r_id);
                    $this->db->where('items_id', $r_id);
                    $this->db->delete('tbl_return_stock_items');
                }
            }
        }
        $items_data = $this->input->post('items', true);
        if (!empty($items_data)) {
            $index = 0;
            foreach ($items_data as $items) {
                $items['return_stock_id'] = $return_stock_id;
                if (!empty($items['saved_items_id'])) {
                    $items['invoice_items_id'] = $items['saved_items_id'];
                }
                unset($items['total_qty']);
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
                if ($data['update_stock'] == 'Yes') {
                    if (!empty($items['saved_items_id']) && $items['saved_items_id'] != 'undefined') {
                        if (!empty($items['items_id'])) {
                            $old_quantity = get_any_field('tbl_return_stock_items', array('items_id' => $items['items_id']), 'quantity');
                            if ($old_quantity != $items['quantity']) {
                                // $a < $b	Less than TRUE if $a is strictly less than $b.
                                // $a > $b	Greater than TRUE if $a is strictly greater than $b.
                                if ($old_quantity > $items['quantity']) {
                                    $quantity = $old_quantity - $items['quantity'];
                                    $this->return_stock_model->return_items($items['saved_items_id'], $quantity);
                                } else {
                                    $quantity = $items['quantity'] - $old_quantity;
                                    $this->return_stock_model->reduce_items($items['saved_items_id'], $quantity);
                                }
                            }
                        } else {
                            $this->return_stock_model->return_items($items['saved_items_id'], $items['quantity']);
                        }
                    }
                }

                $price = $items['quantity'] * $items['unit_cost'];
                $items['item_tax_total'] = ($price / 100 * $tax);
                $items['total_cost'] = $price;
                // get all client
                $this->return_stock_model->_table_name = 'tbl_return_stock_items';
                $this->return_stock_model->_primary_key = 'items_id';
                if (!empty($items['items_id'])) {
                    $items_id = $items['items_id'];
                    $this->return_stock_model->save($items, $items_id);
                } else {
                    $items_id = $this->return_stock_model->save($items);
                }
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

        // messages for user
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/return_stock/return_stock_details/' . $return_stock_id);
    }

    function return_items($items_id)
    {
        $items_info = $this->db->where('items_id', $items_id)->get('tbl_return_stock_items')->row();
        if (!empty($items_info->saved_items_id)) {
            $this->return_stock_model->return_items($items_info->saved_items_id, $items_info->quantity);
        }
        return true;
    }

    public function return_stock_details($id)
    {
        $data['title'] = lang('return_stock') . ' ' . lang('details'); //Page title
        $data['return_stock_info'] = $this->return_stock_model->check_by(array('return_stock_id' => $id), 'tbl_return_stock');
        if (empty($data['return_stock_info'])) {
            set_message('error', lang('there_in_no_value'));
            redirect('admin/return_stock');
        }
        $data['subview'] = $this->load->view('admin/return_stock/return_stock_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public
    function clone_return_stock($return_stock_id)
    {
        $edited = can_action('13', 'edited');
        $can_edit = $this->return_stock_model->can_action('tbl_return_stock', 'edit', array('return_stock_id' => $return_stock_id));
        if (!empty($can_edit) && !empty($edited)) {
            $data['return_stock_info'] = $this->return_stock_model->check_by(array('return_stock_id' => $return_stock_id), 'tbl_return_stock');

            $data['permission_user'] = $this->return_stock_model->all_permission_user('153');
            $data['all_supplier'] = $this->return_stock_model->get_permission('tbl_suppliers');

            $data['modal_subview'] = $this->load->view('admin/return_stock/_modal_clone_return_stock', $data, FALSE);

        } else {
            set_message('error', lang('there_in_no_value'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public
    function cloned_return_stock($id)
    {
        $edited = can_action('153', 'edited');
        $can_edit = $this->return_stock_model->can_action('tbl_return_stock', 'edit', array('return_stock_id' => $id));
        if (!empty($can_edit) && !empty($edited)) {
            if (config_item('increment_return_stock_number') == 'FALSE') {
                $this->load->helper('string');
                $reference_no = config_item('return_stock_prefix') . ' ' . random_string('nozero', 6);
            } else {
                $reference_no = config_item('return_stock_prefix') . ' ' . $this->return_stock_model->generate_return_stock_number();
            }
            $return_stock_info = $this->return_stock_model->check_by(array('return_stock_id' => $id), 'tbl_return_stock');
            $module = $this->input->post('module', true);
            if (!empty($module) && $module == 'supplier') {
                $module_id = $this->input->post('supplier_id', true);
            } else {
                $module_id = $this->input->post('client_id', true);
            }
            // save into invoice table
            $new_invoice = array(
                'reference_no' => $reference_no,
                'invoices_id' => $return_stock_info->invoices_id,
                'module' => $this->input->post('module', true),
                'module_id' => $module_id,
                'return_stock_date' => $this->input->post('return_stock_date', true),
                'due_date' => $this->input->post('due_date', true),
                'notes' => $return_stock_info->notes,
                'total_tax' => $return_stock_info->total_tax,
                'tax' => $return_stock_info->tax,
                'discount_type' => $return_stock_info->discount_type,
                'discount_percent' => $return_stock_info->discount_percent,
                'user_id' => $return_stock_info->user_id,
                'created_by' => my_id(),
                'adjustment' => $return_stock_info->adjustment,
                'discount_total' => $return_stock_info->discount_total,
                'show_quantity_as' => $return_stock_info->show_quantity_as,
                'status' => $return_stock_info->status,
                'main_status' => $return_stock_info->main_status,
                'update_stock' => $return_stock_info->update_stock,
                'emailed' => $return_stock_info->emailed,
                'permission' => $return_stock_info->permission,
            );

            $this->return_stock_model->_table_name = "tbl_return_stock";
            $this->return_stock_model->_primary_key = "return_stock_id";
            $new_return_stock_id = $this->return_stock_model->save($new_invoice);

            $return_stock_items = $this->db->where('return_stock_id', $id)->get('tbl_return_stock_items')->result();
            if (!empty($return_stock_items)) {
                foreach ($return_stock_items as $new_item) {
                    if ($return_stock_info->update_stock == 'Yes') {
                        if (!empty($new_item->saved_items_id) && $new_item->saved_items_id != 'undefined') {
                            $this->return_stock_model->reduce_items($new_item->saved_items_id, $new_item->quantity);
                        }
                    }
                    $items = array(
                        'return_stock_id' => $new_return_stock_id,
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
                    $this->return_stock_model->_table_name = "tbl_return_stock_items";
                    $this->return_stock_model->_primary_key = "items_id";
                    $this->return_stock_model->save($items);
                }
            }
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'return_stock',
                'module_field_id' => $new_return_stock_id,
                'activity' => ('activity_cloned_return_stock'),
                'icon' => 'fa-shopping-cart',
                'link' => 'admin/return_stock/return_stock_details/' . $new_return_stock_id,
                'value1' => ' from ' . $return_stock_info->reference_no . ' to ' . $reference_no,
            );
            // Update into tbl_project
            $this->return_stock_model->_table_name = "tbl_activities";
            $this->return_stock_model->_primary_key = "activities_id";
            $this->return_stock_model->save($activities);

            // messages for user
            $type = "success";
            $message = lang('return_stock_created');
            set_message($type, $message);
            redirect('admin/return_stock/return_stock_details/' . $new_return_stock_id);
        } else {
            set_message('error', lang('there_in_no_value'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }


    public function payment($id)
    {
        $data['title'] = lang('return_stock') . ' ' . lang('payment');
        // get payment info by id
        $this->return_stock_model->_table_name = 'tbl_return_stock_payments';
        $this->return_stock_model->_order_by = 'payments_id';
        $data['all_payments_history'] = $this->return_stock_model->get_by(array('return_stock_id' => $id), FALSE);
        $data['return_stock_info'] = $this->return_stock_model->check_by(array('return_stock_id' => $id), 'tbl_return_stock');

        $data['all_return_stocks'] = $this->return_stock_model->get_permission('tbl_return_stock');
        $data['subview'] = $this->load->view('admin/return_stock/payment', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public
    function get_payment($return_stock_id)
    {
        $edited = can_action('153', 'edited');
        $can_edit = $this->return_stock_model->can_action('tbl_return_stock', 'edit', array('return_stock_id' => $return_stock_id));
        if (!empty($can_edit) && !empty($edited)) {
            $due = round($this->return_stock_model->calculate_to('return_stock_due', $return_stock_id), 2);
            $paid_amount = $this->input->post('amount', TRUE);
            if ($paid_amount != 0) {
                if ($paid_amount > $due) {
                    // messages for user
                    $type = "error";
                    $message = lang('overpaid_amount');
                    set_message($type, $message);
                    redirect('admin/return_stock/payment/' . $return_stock_id);
                } else {
                    $return_stock_info = $this->return_stock_model->check_by(array('return_stock_id' => $return_stock_id), 'tbl_return_stock');
                    $data = array(
                        'return_stock_id' => $return_stock_id,
                        'module' => $return_stock_info->module,
                        'paid_to' => (!empty($return_stock_info->module_id) ? $return_stock_info->module_id : ''),
                        'paid_by' => my_id(),
                        'payment_method' => $this->input->post('payment_methods_id', TRUE),
                        'currency' => $this->input->post('currency', TRUE),
                        'amount' => $paid_amount,
                        'payment_date' => date('Y-m-d', strtotime($this->input->post('payment_date', TRUE))),
                        'trans_id' => $this->input->post('trans_id'),
                        'notes' => $this->input->post('notes'),
                        'month_paid' => date("m", strtotime($this->input->post('payment_date', TRUE))),
                        'year_paid' => date("Y", strtotime($this->input->post('payment_date', TRUE))),
                    );
                    $this->return_stock_model->_table_name = 'tbl_return_stock_payments';
                    $this->return_stock_model->_primary_key = 'payments_id';
                    $payments_id = $this->return_stock_model->save($data);

                    if ($paid_amount < $due) {
                        $status = 'partially_paid';
                    }
                    if ($paid_amount == $due) {
                        $status = 'Paid';
                    }

                    $return_stock_data['status'] = $status;
                    update('tbl_return_stock', array('return_stock_id' => $return_stock_id), $return_stock_data);
                    $currency = $this->return_stock_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                    $activity = array(
                        'user' => $this->session->userdata('user_id'),
                        'module' => 'return_stock',
                        'module_field_id' => $return_stock_id,
                        'activity' => ('activity_new_payment'),
                        'icon' => 'fa-shopping-cart',
                        'link' => 'admin/return_stock/return_stock_details/' . $return_stock_id,
                        'value1' => display_money($paid_amount, $currency->symbol),
                        'value2' => $return_stock_info->reference_no,
                    );
                    $this->return_stock_model->_table_name = 'tbl_activities';
                    $this->return_stock_model->_primary_key = 'activities_id';
                    $this->return_stock_model->save($activity);

                    if ($this->input->post('save_into_account') == 'on') {
                        $account_id = $this->input->post('account_id', true);
                        if (empty($account_id)) {
                            $account_id = config_item('default_account');
                        }
                        if (!empty($account_id)) {
                            $reference = lang('return_stock') . ' ' . lang('reference_no') . ": <a href='" . base_url('admin/return_stock/return_stock_details/' . $return_stock_info->return_stock_id) . "' >" . $return_stock_info->reference_no . "</a> and " . lang('trans_id') . ": <a href='" . base_url('admin/return_stock/payments_details/' . $payments_id) . "'>" . $this->input->post('trans_id', true) . "</a>";
                            $trans_id = $this->input->post('trans_id', true);
                            // save into tbl_transaction
                            $tr_data = array(
                                'name' => lang('return_stock_payment', lang('trans_id') . '# ' . $trans_id),
                                'type' => 'Income',
                                'amount' => $paid_amount,
                                'debit' => $paid_amount,
                                'credit' => 0,
                                'date' => date('Y-m-d', strtotime($this->input->post('payment_date', TRUE))),
                                'paid_by' => (!empty($return_stock_info->module_id) ? $return_stock_info->module_id : ''),
                                'payment_methods_id' => $this->input->post('payment_methods_id', TRUE),
                                'reference' => $trans_id,
                                'notes' => lang('this_deposit_from_return_stock_payment', $reference),
                                'permission' => 'all',
                            );
                            $account_info = $this->return_stock_model->check_by(array('account_id' => $account_id), 'tbl_accounts');
                            if (!empty($account_info)) {
                                $ac_data['balance'] = $account_info->balance + $tr_data['amount'];
                                $this->return_stock_model->_table_name = "tbl_accounts";
                                $this->return_stock_model->_primary_key = "account_id";
                                $this->return_stock_model->save($ac_data, $account_info->account_id);

                                $aaccount_info = $this->return_stock_model->check_by(array('account_id' => $account_id), 'tbl_accounts');

                                $tr_data['total_balance'] = $aaccount_info->balance;
                                $tr_data['account_id'] = $account_id;

                                // save into tbl_transaction
                                $this->return_stock_model->_table_name = "tbl_transactions";
                                $this->return_stock_model->_primary_key = "transactions_id";
                                $return_id = $this->return_stock_model->save($tr_data);

                                $deduct_account['account_id'] = $account_id;
                                $this->return_stock_model->_table_name = 'tbl_return_stock_payments';
                                $this->return_stock_model->_primary_key = 'payments_id';
                                $this->return_stock_model->save($deduct_account, $payments_id);

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
                                $this->return_stock_model->_table_name = "tbl_activities";
                                $this->return_stock_model->_primary_key = "activities_id";
                                $this->return_stock_model->save($activities);

                            }
                        }
                    }
                    if ($this->input->post('send_thank_you') == 'on') {
                        $this->send_payment_email($return_stock_id, $paid_amount); //send thank you email
                    }
                    if ($this->input->post('send_sms') == 'on') {
                        $this->send_return_payment_sms($return_stock_id, $payments_id); //send thank you email
                    }
                }
            }
            // messages for user
            $type = "success";
            $message = lang('generate_payment');
            set_message($type, $message);
            redirect('admin/return_stock/return_stock_details/' . $return_stock_id);
        } else {
            set_message('error', lang('there_in_no_value'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function send_return_payment_sms($return_stock_id, $payments_id)
    {
        $mobile = can_received_sms('purchase_confirmation_sms_number');
        if (!empty($mobile)) {
            $merge_fields = [];
            $merge_fields = array_merge($merge_fields, merge_return_stock_template($return_stock_id));
            $merge_fields = array_merge($merge_fields, merge_return_stock_template($return_stock_id, $payments_id));
            $this->sms->send(SMS_RETURN_STOCK_PAYMENT, $mobile, $merge_fields);
        }
        return true;
    }

    public function all_payments($id = NULL)
    {
        if (!empty($id)) {
            $can_edit = $this->return_stock_model->can_action('tbl_return_stock', 'edit', array('return_stock_id' => $id));
            if (!empty($can_edit)) {
                $payments_info = $this->return_stock_model->check_by(array('payments_id' => $id), 'tbl_return_stock_payments');
                $data['return_stock_info'] = $this->return_stock_model->check_by(array('return_stock_id' => $payments_info->return_stock_id), 'tbl_return_stock');
            }
            $data['title'] = lang('edit') . ' ' . lang('return_stock') . ' ' . lang('payment'); //Page title
            $subview = 'edit_payments';
        } else {
            $data['title'] = lang('all') . ' ' . lang('return_stock') . ' ' . lang('payment'); //Page title
            $subview = 'all_payments';
        }
        // get payment info by id
        if (!empty($id)) {
            $can_edit = $this->return_stock_model->can_action('tbl_return_stock_payments', 'edit', array('payments_id' => $id));
            if (!empty($can_edit)) {
                $data['payments_info'] = $this->return_stock_model->check_by(array('payments_id' => $id), 'tbl_return_stock_payments');

            } else {
                set_message('error', lang('no_permission_to_access'));
                redirect($_SERVER['HTTP_REFERER']);
            }

        }
        $data['subview'] = $this->load->view('admin/return_stock/' . $subview, $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    // all payment list
    public function paymentList()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_return_stock_payments';
            $this->datatables->join_table = array('tbl_return_stock', 'tbl_suppliers');
            $this->datatables->join_where = array('tbl_return_stock.return_stock_id=tbl_return_stock_payments.return_stock_id', 'tbl_suppliers.supplier_id=tbl_return_stock.supplier_id');
            $this->datatables->column_order = array('payment_date', 'return_stock_date', 'reference_no', 'tbl_suppliers.name', 'amount', 'payment_method');
            $this->datatables->column_search = array('payment_date', 'return_stock_date', 'reference_no', 'tbl_suppliers.name', 'amount', 'payment_method');
            $this->datatables->order = array('payments_id' => 'desc');
            $fetch_data = make_datatables();

            $data = array();

            $edited = can_action('154', 'edited');
            $deleted = can_action('154', 'deleted');
            foreach ($fetch_data as $_key => $v_payments_info) {
                $action = null;
                $can_edit = $this->return_stock_model->can_action('tbl_return_stock', 'edit', array('return_stock_id' => $v_payments_info->return_stock_id));
                $can_delete = $this->return_stock_model->can_action('tbl_return_stock', 'delete', array('return_stock_id' => $v_payments_info->return_stock_id));
                $currency = $this->return_stock_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                if (!empty($v_payments_info->name)) {
                    $c_name = $v_payments_info->name;
                } else {
                    $c_name = '-';
                }
                $payment_methods = $this->return_stock_model->check_by(array('payment_methods_id' => $v_payments_info->payment_method), 'tbl_payment_methods');
                $sub_array[] = '<a href="' . base_url() . 'admin/return_stock/payments_details/' . $v_payments_info->payments_id . '">' . display_date($v_payments_info->payment_date) . '</a>';
                $sub_array[] = display_date($v_payments_info->return_stock_date);
                $sub_array[] = '<a href="' . base_url() . 'admin/return_stock/return_stock_details/' . $v_payments_info->return_stock_id . '">' . display_date($v_payments_info->payment_date) . '</a>';
                $sub_array[] = $c_name;
                $sub_array[] = display_money($v_payments_info->amount, $currency->symbol);
                $sub_array[] = !empty($payment_methods->method_name) ? $payment_methods->method_name : '-';
                if (!empty($can_edit) && !empty($edited)) {
                    $action .= btn_edit('admin/return_stock/all_payments/' . $v_payments_info->payments_id) . ' ';
                }
                if (!empty($can_delete) && !empty($deleted)) {
                    $action .= ajax_anchor(base_url("admin/return_stock/delete_payment/" . $v_payments_info->payments_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                }
                $sub_array[] = $action;
                $data[] = $sub_array;
            }
            render_table($data);
        } else {
            redirect('admin/dashboard');
        }
    }


    public function payments_details($id)
    {
        $data['all_return_stocks'] = $this->return_stock_model->get_permission('tbl_return_stock');
        $data['title'] = lang('return_stock') . ' ' . lang('payment') . ' ' . lang('details'); //Page title
        $subview = 'payments_details';
        // get payment info by id
        $data['payments_info'] = $this->return_stock_model->check_by(array('payments_id' => $id), 'tbl_return_stock_payments');
        $data['subview'] = $this->load->view('admin/return_stock/' . $subview, $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
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

        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'return_stock',
            'module_field_id' => $payments_id,
            'activity' => ('activity_update_payment'),
            'icon' => 'fa-shopping-cart',
            'link' => 'admin/return_stock/return_stock_details/' . $payments_id,
            'value1' => $data['amount'],
            'value2' => $data['payment_date'],
        );
        $this->return_stock_model->_table_name = 'tbl_activities';
        $this->return_stock_model->_primary_key = 'activities_id';
        $this->return_stock_model->save($activity);

        $this->return_stock_model->_table_name = 'tbl_return_stock_payments';
        $this->return_stock_model->_primary_key = 'payments_id';
        $this->return_stock_model->save($data, $payments_id);


        // messages for user
        $type = "success";
        $message = lang('generate_payment');
        set_message($type, $message);
        redirect('admin/return_stock/all_payments');

    }

    public
    function send_payment($return_stock_id, $paid_amount)
    {
        $this->send_payment_email($return_stock_id, $paid_amount); //send email

        $type = "success";
        $message = lang('payment_information_send');
        set_message($type, $message);
        redirect($_SERVER['HTTP_REFERER']);
    }

    function send_payment_email($return_stock_id, $paid_amount)
    {
        $currency = $this->return_stock_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
        $email_template = email_templates(array('email_group' => 'payment_email'));
        $message = $email_template->template_body;
        $subject = $email_template->subject;

        $return_stock_info = $this->return_stock_model->check_by(array('return_stock_id' => $return_stock_id), 'tbl_return_stock');
        $currency = $currency->symbol;
        $reference = $return_stock_info->reference_no;

        $invoice_currency = str_replace("{
        INVOICE_CURRENCY}", $currency, $message);
        $reference = str_replace("{
        INVOICE_REF}", $reference, $invoice_currency);
        $amount = str_replace("{
        PAID_AMOUNT}", $paid_amount, $reference);
        $message = str_replace("{
        SITE_NAME}", config_item('company_name'), $amount);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);
        $supplier_info = $this->return_stock_model->check_by(array('supplier_id' => $return_stock_info->supplier_id), 'tbl_suppliers');
        $address = $supplier_info->email;
        $params['recipient'] = $address;

        $params['subject'] = '[ ' . config_item('company_name') . ' ]' . ' ' . $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';

        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'return_stock',
            'module_field_id' => $return_stock_id,
            'activity' => ('activity_send_payment'),
            'icon' => 'fa-shopping-cart',
            'link' => 'admin/return_stock/return_stock_details/' . $return_stock_id,
            'value1' => $reference,
            'value2' => $currency . ' ' . $amount,
        );
        $this->return_stock_model->_table_name = 'tbl_activities';
        $this->return_stock_model->_primary_key = 'activities_id';
        $this->return_stock_model->save($activity);

        $this->return_stock_model->send_email($params);
    }

    public
    function change_status($action, $id)
    {
        $return_stock_info = $this->return_stock_model->check_by(array('return_stock_id' => $id), 'tbl_return_stock');
        $return_stock_items = $this->db->where('return_stock_id', $id)->get('tbl_return_stock_items')->result();
        if ($action == 'mark_as_sent') {
            $data = array('emailed' => 'Yes', 'date_sent' => date("Y-m-d:s", time()));
        } elseif ($action == 'unmark_as_cancelled') {
            $payment_status = $this->return_stock_model->get_payment_status($return_stock_info->return_stock_id, true);
            $data = array('main_status' => 'Pending', 'status' => lang($payment_status));
        } elseif ($action == 'declined') {
            $data = array('main_status' => 'declined', 'status' => 'declined');
            $this->send_email_return_stock($id, $action); // Email Invoice
        } elseif ($action == 'cancelled') {
            if ($return_stock_info->main_status == 'accepted') {
                if (!empty($return_stock_items)) {
                    foreach ($return_stock_items as $items) {
                        $items = (array)$items;
                        if (!empty($items['saved_items_id']) && $items['saved_items_id'] != '0') {
                            $this->return_stock_model->reduce_items($items['saved_items_id'], $items['quantity']);
                        }
                    }
                }
            }
            $data = array('main_status' => 'cancelled', 'status' => 'cancelled');
            $this->send_email_return_stock($id, $action); // Email Invoice
        } elseif ($action == 'accepted') {
            if ($return_stock_info->main_status == 'Pending' || $return_stock_info->main_status == 'cancelled') {
                if (!empty($return_stock_items)) {
                    foreach ($return_stock_items as $items) {
                        $items = (array)$items;
                        if (!empty($items['saved_items_id']) && $items['saved_items_id'] != '0') {
                            $this->return_stock_model->return_items($items['saved_items_id'], $items['quantity']);
                        }
                    }
                }
            }
            $payment_status = $this->return_stock_model->get_payment_status($return_stock_info->return_stock_id, true);
            $data = array('main_status' => 'accepted', 'status' => lang($payment_status));
            $this->send_email_return_stock($id, $action); // Email Invoice
        } else {
            $data = array('status' => $action);
        }
        $this->return_stock_model->_table_name = 'tbl_return_stock';
        $this->return_stock_model->_primary_key = 'return_stock_id';
        $this->return_stock_model->save($data, $id);

        // messages for user
        $type = "success";
        $imessage = lang('return_stock_update');
        set_message($type, $imessage);
        redirect('admin/return_stock/return_stock_details/' . $id);
    }

    function send_email_return_stock($return_stock_id, $action)
    {
        $return_stock_info = $this->return_stock_model->check_by(array('return_stock_id' => $return_stock_id), 'tbl_return_stock');

        $email_template = email_templates(array('email_group' => 'invoice_item_refund_status'));
        $message = $email_template->template_body;
        $subject = $email_template->subject;
        $REF = str_replace("{REF}", $return_stock_info->reference_no, $subject);
        $subject = str_replace("{STATUS}", lang($action), $REF);

        $amount = str_replace("{LINK}", 'client/invoice/return_stock_details/' . $return_stock_id, $REF);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $amount);

        $params['subject'] = '[ ' . config_item('company_name') . ' ]' . ' ' . $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';
        if ($return_stock_info->module == 'client') {
            $client_info = $this->return_stock_model->check_by(array('client_id' => $return_stock_info->module_id), 'tbl_client');
            $params['recipient'] = $client_info->email;
            $this->return_stock_model->send_email($params);

            if (!empty($client_info->primary_contact) && $client_info->primary_contact != 0) {
                $users = array($client_info->primary_contact);
            }
            if (!empty($users)) {
                foreach ($users as $v_user) {
                    $login_info = $this->return_stock_model->check_by(array('user_id' => $v_user), 'tbl_users');
                    $params['recipient'] = $login_info->email;
                    $this->return_stock_model->send_email($params);

                    if ($v_user != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $v_user,
                            'icon' => 'shopping-cart',
                            'description' => 'not_refund_request_for_invoice_items_status',
                            'link' => 'client/invoice/return_stock_details/' . $return_stock_id,
                            'value' => lang('return_stock') . ' ' . lang('reference_no') . ': ' . $return_stock_info->reference_no,
                        ));
                    }
                }
                show_notification($users);
            }
            $this->return_stock_model->send_email($params);
        }
    }

    function send_return_stock_email($return_stock_id)
    {
        $return_stock_info = $this->return_stock_model->check_by(array('return_stock_id' => $return_stock_id), 'tbl_return_stock');
        $supplier_info = $this->return_stock_model->check_by(array('supplier_id' => $return_stock_info->supplier_id), 'tbl_suppliers');
        $currency = $this->return_stock_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
        $message = " < p>Hello $supplier_info->name </p >
<p >&nbsp;</p >

<p > This is a return_stock details of " . display_money($this->return_stock_model->calculate_to('total', $return_stock_info->return_stock_id), $currency->symbol) . " < br />
Please check the attachment bellow:<br />
<br />
Best Regards,<br />
The " . config_item('company_name') . " Team </p > ";
        $params = array(
            'recipient' => $supplier_info->email,
            'subject' => '[ ' . config_item('company_name') . ' ]' . ' return_stock' . ' ' . $return_stock_info->reference_no,
            'message' => $message
        );
        $params['resourceed_file'] = 'uploads/' . lang('return_stock') . '_' . $return_stock_info->reference_no . '.pdf';
        $params['resourcement_url'] = base_url() . 'uploads/' . lang('return_stock') . '_' . $return_stock_info->reference_no . '.pdf';
        $this->attach_pdf($return_stock_id);
        $this->return_stock_model->send_email($params);
        //Delete invoice in tmp folder
        if (is_file('uploads/' . lang('return_stock') . '_' . $return_stock_info->reference_no . '.pdf')) {
            unlink('uploads/' . lang('return_stock') . '_' . $return_stock_info->reference_no . '.pdf');
        }

        $data = array('emailed' => 'Yes', 'date_sent' => date("Y-m-d H:i:s", time()));

        $this->return_stock_model->_table_name = 'tbl_return_stock';
        $this->return_stock_model->_primary_key = 'return_stock_id';
        $this->return_stock_model->save($data, $return_stock_info->return_stock_id);

        // Log Activity
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'return_stock',
            'module_field_id' => $return_stock_info->return_stock_id,
            'activity' => ('activity_return_stock_sent'),
            'icon' => 'fa-shopping-cart',
            'link' => 'admin/return_stock/return_stock_details/' . $return_stock_info->return_stock_id,
            'value1' => $return_stock_info->reference_no,
            'value2' => display_money($this->return_stock_model->calculate_to('total', $return_stock_info->return_stock_id), $currency->symbol),
        );
        $this->return_stock_model->_table_name = 'tbl_activities';
        $this->return_stock_model->_primary_key = 'activities_id';
        $this->return_stock_model->save($activity);
        // messages for user
        $type = "success";
        $imessage = lang('invoice_sent');
        set_message($type, $imessage);
        redirect('admin/return_stock/return_stock_details/' . $return_stock_info->return_stock_id);
    }

    public function attach_pdf($id)
    {
        $data['page'] = lang('return_stock');
        $data['return_stock_info'] = $this->return_stock_model->check_by(array('return_stock_id' => $id), 'tbl_return_stock');
        $data['title'] = lang('invoices'); //Page title
        $this->load->helper('dompdf');
        $html = $this->load->view('admin/return_stock/return_stock_pdf', $data, TRUE);
        $result = pdf_create($html, lang('return_stock') . '_' . $data['return_stock_info']->reference_no, 1, null, true);
        return $result;
    }


    public function payments_pdf($id)
    {
        $data['title'] = "Payments PDF"; //Page title
        // get payment info by id
        $this->return_stock_model->_table_name = 'tbl_return_stock_payments';
        $this->return_stock_model->_order_by = 'payments_id';
        $data['payments_info'] = $this->return_stock_model->check_by(array('payments_id' => $id), 'tbl_return_stock_payments');
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/return_stock/payments_pdf', $data, TRUE);
        pdf_create($viewfile, lang('return_stock') . ' ' . lang('payment') . '# ' . $data['payments_info']->trans_id);
    }

    public function pdf_return_stock($id)
    {
        $data['return_stock_info'] = $this->return_stock_model->check_by(array('return_stock_id' => $id), 'tbl_return_stock');
        $data['title'] = lang('return_stock') . ' ' . "PDF"; //Page title
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/return_stock/return_stock_pdf', $data, TRUE);

        pdf_create($viewfile, lang('return_stock') . '# ' . $data['return_stock_info']->reference_no);
    }

    public function delete_return_stock($id)
    {
        $deleted = can_action('153', 'deleted');
        $can_delete = $this->return_stock_model->can_action('tbl_return_stock', 'delete', array('return_stock_id' => $id));
        if (!empty($can_delete) && !empty($deleted)) {
            $return_stock_info = $this->return_stock_model->check_by(array('return_stock_id' => $id), 'tbl_return_stock');
            $return_stock_items_info = $this->return_stock_model->check_by(array('return_stock_id' => $id), 'tbl_return_stock_items');
            if ($return_stock_info->update_stock == 'Yes') {
                if (!empty($return_stock_items_info)) {
                    foreach ($return_stock_items_info as $v_items) {
                        if (!empty($v_items->saved_items_id) && $v_items->saved_items_id != 0) {
                            $this->return_stock_model->return_items($v_items->saved_items_id, $v_items->quantity);
                        }
                    }
                }
            }
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

    public
    function client_change_data($name, $value, $current_id = null)
    {
        if ($this->input->is_ajax_request()) {
            $data = array();
//            $data['type'] = $name;
            if ($name == 'supplier_id') {
                $_data['purchases_to_merge'] = $this->db->where('supplier_id', $value)->get('tbl_purchases')->result();
            } elseif ($name == 'client_id') {
                $_data['invoices_to_merge'] = $this->db->where('client_id', $value)->get('tbl_invoices')->result();
            }
            $data['related_info'] = $this->load->view('admin/return_stock/related_to', $_data, true);
            echo json_encode($data);
            exit();
        }
    }

    public
    function get_merge_data($name, $id)
    {
        if ($name == 'purchase_id') {
            $this->load->model('purchase_model');
            $invoice_items = $this->purchase_model->ordered_items_by_id($id);
        } elseif ($name == 'invoices_id') {
            $this->load->model('invoice_model');
            $invoice_items = $this->invoice_model->ordered_items_by_id($id);
        }
        $i = 0;
        foreach ($invoice_items as $item) {
            if ($name == 'purchase_id') {
                $this->load->model('purchase_model');
                $invoice_items[$i]->taxname = $this->purchase_model->get_invoice_item_taxes($item->items_id, 'purchase');
            } elseif ($name == 'invoices_id') {
                $this->load->model('invoice_model');
                $invoice_items[$i]->taxname = $this->invoice_model->get_invoice_item_taxes($item->items_id);
            }
            $invoice_items[$i]->new_itmes_id = $item->items_id;
            $invoice_items[$i]->saved_items_id = $item->saved_items_id;
            $invoice_items[$i]->qty = $item->quantity;
            $invoice_items[$i]->total_qty = $item->quantity;
            $invoice_items[$i]->rate = $item->unit_cost;
            $i++;
        }
        echo json_encode($invoice_items);
        exit();
    }

}
