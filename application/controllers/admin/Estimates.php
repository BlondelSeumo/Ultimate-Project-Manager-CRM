<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Estimates extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('estimates_model');
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

    public function index($action = NULL, $id = NULL, $item_id = NULL)
    {

        $data['page'] = lang('sales');
        $data['sub_active'] = lang('estimates');
        if (!empty($item_id)) {
            $can_edit = $this->estimates_model->can_action('tbl_estimates', 'edit', array('estimates_id' => $id));
            if (!empty($can_edit)) {
                $data['item_info'] = $this->estimates_model->check_by(array('estimate_items_id' => $item_id), 'tbl_estimate_items');
            }
        }
        if ($action == 'edit_estimates') {
            $data['active'] = 2;
            $can_edit = $this->estimates_model->can_action('tbl_estimates', 'edit', array('estimates_id' => $id));
            if (!empty($can_edit)) {
                $data['estimates_info'] = $this->estimates_model->check_by(array('estimates_id' => $id), 'tbl_estimates');
                if (!empty($data['estimates_info']->client_id)) {
                    $data['estimate_to_merge'] = $this->estimates_model->check_for_merge_invoice($data['estimates_info']->client_id, $id);
                }
            }
        } else if ($action == 'project') {
            $data['project_id'] = $id;
            $data['project_info'] = $this->estimates_model->check_by(array('project_id' => $id), 'tbl_project');
            $data['active'] = 2;
        } else {
            $data['active'] = 1;
        }
        // get all client
        $this->estimates_model->_table_name = 'tbl_client';
        $this->estimates_model->_order_by = 'client_id';
        $data['all_client'] = $this->estimates_model->get();
        // get permission user
        $data['permission_user'] = $this->estimates_model->all_permission_user('14');
        $data['all_estimates_info'] = $this->estimates_model->get_permission('tbl_estimates');
        if ($action == 'estimates_details') {
            $data['title'] = "Estimates Details"; //Page title
            $data['estimates_info'] = $this->estimates_model->check_by(array('estimates_id' => $id), 'tbl_estimates');
            if (empty($data['estimates_info'])) {
                $type = "error";
                $message = lang('no_record_found');
                set_message($type, $message);
                redirect('admin/estimates');
            }
            $subview = 'estimates_details';
        } elseif ($action == 'estimates_history') {
            $data['estimates_info'] = $this->estimates_model->check_by(array('estimates_id' => $id), 'tbl_estimates');
            $data['title'] = "Estimates History"; //Page title
            $subview = 'estimates_history';
        } elseif ($action == 'email_estimates') {
            $data['estimates_info'] = $this->estimates_model->check_by(array('estimates_id' => $id), 'tbl_estimates');
            $data['title'] = "Email Estimates"; //Page title
            $subview = 'email_estimates';
            $data['editor'] = $this->data;
        } elseif ($action == 'pdf_estimates') {
            $data['estimates_info'] = $this->estimates_model->check_by(array('estimates_id' => $id), 'tbl_estimates');
            $data['title'] = "Estimates PDF"; //Page title
            $this->load->helper('dompdf');
            $viewfile = $this->load->view('admin/estimates/estimates_pdf', $data, TRUE);
            pdf_create($viewfile, slug_it('Estimates  # ' . $data['estimates_info']->reference_no));
        } else {
            $data['title'] = "Estimates"; //Page title
            $subview = 'estimates';
        }
        $data['subview'] = $this->load->view('admin/estimates/' . $subview, $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function estimatesList($filterBy = null, $search_by = null)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_estimates';
            $this->datatables->join_table = array('tbl_client');
            $this->datatables->join_where = array('tbl_estimates.client_id=tbl_client.client_id');
            $this->datatables->column_order = array('reference_no', 'tbl_client.name', 'estimate_date', 'due_date', 'status');
            $this->datatables->column_search = array('reference_no', 'tbl_client.name', 'estimate_date', 'due_date', 'status');
            $this->datatables->order = array('estimates_id' => 'desc');

            if (empty($filterBy)) {
                $filterBy = '_' . date('Y');
            }
            if (!empty($filterBy) && !is_numeric($filterBy)) {
                $ex = explode('_', $filterBy);
                if ($ex[0] != 'c') {
                    $filterBy = $filterBy;
                }
            }
            $where = array();
            $where_in = null;
            if (!empty($search_by)) {
                if ($search_by == 'by_project') {
                    $where = array('project_id' => $filterBy);
                }
                if ($search_by == 'by_agent') {
                    $where = array('user_id' => $filterBy);
                }
                if ($search_by == 'by_client') {
                    $where = array('tbl_estimates.client_id' => $filterBy);
                }
            } else {
                if ($filterBy == 'last_month' || $filterBy == 'this_months') {
                    if ($filterBy == 'last_month') {
                        $month = date('Y-m', strtotime('-1 months'));
                    } else {
                        $month = date('Y-m');
                    }
                    $where = array('estimate_month' => $month);
                } else if ($filterBy == 'expired') {
                    $where = array('UNIX_TIMESTAMP(due_date) <' => strtotime(date('Y-m-d')));
                    $status = array('draft', 'pending');
                    $where_in = array('status', $status);
                } else if (strstr($filterBy, '_')) {
                    $year = str_replace('_', '', $filterBy);
                    $where = array('estimate_year' => $year);
                } else if (!empty($filterBy) && $filterBy != 'all') {
                    $where = array('status' => $filterBy);
                }
            }
            // get all estimate
            $fetch_data = $this->datatables->get_estimates($filterBy, $search_by);

            $data = array();

            $edited = can_action('14', 'edited');
            $deleted = can_action('14', 'deleted');
            foreach ($fetch_data as $_key => $v_estimates) {
                if (!empty($v_estimates)) {
                    $action = null;
                    $can_edit = $this->estimates_model->can_action('tbl_estimates', 'edit', array('estimates_id' => $v_estimates->estimates_id));
                    $can_delete = $this->estimates_model->can_action('tbl_estimates', 'delete', array('estimates_id' => $v_estimates->estimates_id));

                    if ($v_estimates->status == 'pending') {
                        $label = "info";
                    } elseif ($v_estimates->status == 'accepted') {
                        $label = "success";
                    } else {
                        $label = "danger";
                    }

                    $sub_array = array();
                    $name = null;
                    $name .= '<a class="text-info" href="' . base_url() . 'admin/estimates/index/estimates_details/' . $v_estimates->estimates_id . '">' . $v_estimates->reference_no . '</a>';
                    if ($v_estimates->invoiced == 'Yes') {
                        $invoice_info = $this->db->where('invoices_id', $v_estimates->invoices_id)->get('tbl_invoices')->row();
                        if (!empty($invoice_info)) {
                            $name .= '<p class="text-sm m0 p0"><a class="text-success" href="' . base_url() . 'admin/invoice/manage_invoice/invoice_details/' . $invoice_info->invoices_id . '">' . lang('invoiced') . '</a></p>';
                        }
                    }
                    $sub_array[] = $name;
                    $sub_array[] = strftime(config_item('date_format'), strtotime($v_estimates->estimate_date));
                    $overdue = null;
                    if (strtotime($v_estimates->due_date) < strtotime(date('Y-m-d')) && $v_estimates->status == 'pending' || strtotime($v_estimates->due_date) < strtotime(date('Y-m-d')) && $v_estimates->status == ('draft')) {
                        $overdue .= '<span class="label label-danger ">' . lang("expired") . '</span>';
                    }
                    $sub_array[] = strftime(config_item('date_format'), strtotime($v_estimates->due_date)) . ' ' . $overdue;

                    $sub_array[] = client_name($v_estimates->client_id);

                    $sub_array[] = display_money($this->estimates_model->estimate_calculation('total', $v_estimates->estimates_id), client_currency($v_estimates->client_id));
                    $sub_array[] = "<span class='label label-" . $label . "'>" . lang($v_estimates->status) . "</span>";

                    $custom_form_table = custom_form_table(10, $v_estimates->estimates_id);

                    if (!empty($custom_form_table)) {
                        foreach ($custom_form_table as $c_label => $v_fields) {
                            $sub_array[] = $v_fields;
                        }
                    }
                    if (!empty($can_edit) && !empty($edited)) {
                        $action .= '<a data-toggle="modal" data-target="#myModal"
                                                               title="' . lang('clone') . ' ' . lang('estimate') . '"
                                                               href="' . base_url() . 'admin/estimates/clone_estimate/' . $v_estimates->estimates_id . '"
                                                               class="btn btn-xs btn-purple">
                                                                <i class="fa fa-copy"></i></a>' . ' ';
                        $action .= btn_edit('admin/estimates/index/edit_estimates/' . $v_estimates->estimates_id) . ' ';
                    }
                    if (!empty($can_delete) && !empty($deleted)) {
                        $action .= ajax_anchor(base_url("admin/estimates/delete/delete_estimates/$v_estimates->estimates_id"), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                    }
                    $change_status = null;
                    if (!empty($can_edit) && !empty($edited)) {
                        $ch_url = base_url() . 'admin/estimates/';
                        $change_status .= '<div class="btn-group">
        <button class="btn btn-xs btn-default dropdown-toggle"
                data-toggle="dropdown">
                    ' . lang('change') . '
            <span class="caret"></span></button>
        <ul class="dropdown-menu animated zoomIn">';
                        $change_status .= '<li><a href="' . $ch_url . 'index/estimates_details/' . $v_estimates->estimates_id . '">' . lang('preview') . '</a></li>';
                        $change_status .= '<li><a href="' . $ch_url . 'index/email_estimates' . $v_estimates->estimates_id . '">' . lang('send_email') . '</a></li>';
                        $change_status .= '<li><a href="' . $ch_url . 'index/estimates_history' . $v_estimates->estimates_id . '">' . lang('history') . '</a></li>';
                        $change_status .= '<li><a href="' . $ch_url . 'change_status/declined/' . $v_estimates->estimates_id . '">' . lang('declined') . '</a></li>';
                        $change_status .= '<li><a href="' . $ch_url . 'change_status/accepted/' . $v_estimates->estimates_id . '">' . lang('accepted') . '</a></li>';
                        $change_status .= '</ul></div>';
                        $action .= $change_status;
                    }

                    $sub_array[] = $action;
                    $data[] = $sub_array;
                }
            }
            render_table($data, $where, $where_in);
        } else {
            redirect('admin/dashboard');
        }
    }

    public
    function client_change_data($customer_id, $current_invoice = 'undefined')
    {
        if ($this->input->is_ajax_request()) {
            $data = array();
            $data['client_currency'] = $this->estimates_model->client_currency_symbol($customer_id);
            $_data['estimate_to_merge'] = $this->estimates_model->check_for_merge_invoice($customer_id, $current_invoice);
            $data['merge_info'] = $this->load->view('admin/estimates/merge_estimate', $_data, true);
            echo json_encode($data);
            exit();
        }
    }

    public
    function get_merge_data($id)
    {
        $invoice_items = $this->estimates_model->ordered_items_by_id($id);
        $i = 0;
        foreach ($invoice_items as $item) {
            $invoice_items[$i]->taxname = $this->estimates_model->get_invoice_item_taxes($item->estimate_items_id);
            $invoice_items[$i]->qty = $item->quantity;
            $invoice_items[$i]->rate = $item->unit_cost;
            $i++;
        }
        echo json_encode($invoice_items);
        exit();
    }

    public
    function pdf_estimates($id)
    {
        $data['estimates_info'] = $this->estimates_model->check_by(array('estimates_id' => $id), 'tbl_estimates');
        if (empty($data['estimates_info'])) {
            $type = "error";
            $message = "No Record Found";
            set_message($type, $message);
            redirect('admin/estimates');
        }
        $data['title'] = lang('estimates'); //Page title
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/estimates/estimates_pdf', $data, TRUE);
//        echo "<pre>";
//        print_r($viewfile);
//        exit();
        pdf_create($viewfile, slug_it(lang('estimates') . ' # ' . $data['estimates_info']->reference_no));
    }

    public
    function save_estimates($id = NULL)
    {
        $created = can_action('14', 'created');
        $edited = can_action('14', 'edited');
        if (!empty($created) || !empty($edited) && !empty($id)) {
            $data = $this->estimates_model->array_from_post(array('reference_no', 'client_id', 'project_id', 'discount_type', 'discount_percent', 'user_id', 'adjustment', 'discount_total', 'show_quantity_as'));
            $data['client_visible'] = ($this->input->post('client_visible') == 'Yes') ? 'Yes' : 'No';
            $data['estimate_date'] = date('Y-m-d', strtotime($this->input->post('estimate_date', TRUE)));
            if (empty($data['estimate_date'])) {
                $data['estimate_date'] = date('Y-m-d');
            }
            $data['estimate_year'] = date('Y', strtotime($this->input->post('estimate_date', TRUE)));
            $data['estimate_month'] = date('Y-m', strtotime($this->input->post('estimate_date', TRUE)));
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
            $save_as_draft = $this->input->post('status', TRUE);
            if (!empty($save_as_draft)) {
                $data['status'] = $save_as_draft;
            } else {
                $data['status'] = 'pending';
            }

            $currency = $this->estimates_model->client_currency_symbol($data['client_id']);
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
                    $assigned_to = $this->estimates_model->array_from_post(array('assigned_to'));
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
                    redirect('admin/estimates');
                } else {
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }

            // get all client
            $this->estimates_model->_table_name = 'tbl_estimates';
            $this->estimates_model->_primary_key = 'estimates_id';
            if (!empty($id)) {
                $estimates_id = $id;
                $can_edit = $this->estimates_model->can_action('tbl_estimates', 'edit', array('estimates_id' => $id));
                if (!empty($can_edit)) {
                    $this->estimates_model->save($data, $id);
                } else {
                    set_message('error', lang('there_in_no_value'));
                    redirect('admin/estimates');
                }
                $this->estimates_model->save($data, $id);
                $action = ('activity_estimates_updated');
                $msg = lang('estimate_updated');
                $description = 'not_estimate_updated';
            } else {
                $estimates_id = $this->estimates_model->save($data);
                $action = ('activity_estimates_created');
                $description = 'not_estimate_created';
                $msg = lang('estimate_created');
            }
            save_custom_field(10, $estimates_id);

            // save items
            $invoices_to_merge = $this->input->post('invoices_to_merge', TRUE);
            $cancel_merged_invoices = $this->input->post('cancel_merged_estimate', TRUE);
            if (!empty($invoices_to_merge)) {
                foreach ($invoices_to_merge as $inv_id) {
                    if (empty($cancel_merged_invoices)) {
                        $this->db->where('estimates_id', $inv_id);
                        $this->db->delete('tbl_estimates');

                        $this->db->where('estimates_id', $inv_id);
                        $this->db->delete('tbl_estimate_items');

                    } else {
                        $mdata = array('status' => 'cancelled');
                        $this->estimates_model->_table_name = 'tbl_estimates';
                        $this->estimates_model->_primary_key = 'estimates_id';
                        $this->estimates_model->save($mdata, $inv_id);
                    }
                }
            }

            $removed_items = $this->input->post('removed_items', TRUE);
            if (!empty($removed_items)) {
                foreach ($removed_items as $r_id) {
                    if ($r_id != 'undefined') {
                        $this->db->where('estimate_items_id', $r_id);
                        $this->db->delete('tbl_estimate_items');
                    }
                }
            }

            $itemsid = $this->input->post('estimate_items_id', TRUE);
            $items_data = $this->input->post('items', true);

            if (!empty($items_data)) {
                $index = 0;
                foreach ($items_data as $items) {
                    $items['estimates_id'] = $estimates_id;
                    unset($items['invoice_items_id']);
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
                    $price = $items['quantity'] * $items['unit_cost'];
                    $items['item_tax_total'] = ($price / 100 * $tax);
                    $items['total_cost'] = $price;
                    // get all client
                    $this->estimates_model->_table_name = 'tbl_estimate_items';
                    $this->estimates_model->_primary_key = 'estimate_items_id';
                    if (!empty($itemsid[$index])) {
                        $items_id = $itemsid[$index];
                        $this->estimates_model->save($items, $items_id);
                    } else {
                        $items_id = $this->estimates_model->save($items);
                    }
                    $index++;
                }
            }
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'estimates',
                'module_field_id' => $estimates_id,
                'activity' => $action,
                'icon' => 'fa-shopping-cart',
                'link' => 'admin/estimates/index/estimates_details/' . $estimates_id,
                'value1' => $data['reference_no']
            );
            $this->estimates_model->_table_name = 'tbl_activities';
            $this->estimates_model->_primary_key = 'activities_id';
            $this->estimates_model->save($activity);

            // send notification to client
            if (!empty($data['client_id'])) {
                $client_info = $this->estimates_model->check_by(array('client_id' => $data['client_id']), 'tbl_client');
                if (!empty($client_info->primary_contact)) {
                    $notifyUser = array($client_info->primary_contact);
                } else {
                    $user_info = $this->estimates_model->check_by(array('company' => $data['client_id']), 'tbl_account_details');
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
                            'link' => 'client/estimates/index/estimates_details/' . $estimates_id,
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
        }
        if (!empty($data['project_id']) && is_numeric($data['project_id'])) {
            redirect('admin/projects/project_details/' . $data['project_id']);
        } else {
            redirect('admin/estimates/index/estimates_details/' . $estimates_id);
        }
        redirect('admin/estimates');
    }

    public
    function insert_items($estimates_id)
    {
        $edited = can_action('14', 'edited');
        $can_edit = $this->estimates_model->can_action('tbl_estimates', 'edit', array('estimates_id' => $estimates_id));
        if (!empty($can_edit) && !empty($edited) && !empty($estimates_id)) {
            $data['estimates_id'] = $estimates_id;
            $data['modal_subview'] = $this->load->view('admin/estimates/_modal_insert_items', $data, FALSE);
            $this->load->view('admin/_layout_modal', $data);
        } else {
            set_message('error', lang('there_in_no_value'));
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/estimates');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public
    function add_insert_items($estimates_id)
    {
        $can_edit = $this->estimates_model->can_action('tbl_estimates', 'edit', array('estimates_id' => $estimates_id));
        $edited = can_action('14', 'edited');
        if (!empty($can_edit) && !empty($edited)) {
            $saved_items_id = $this->input->post('saved_items_id', TRUE);
            if (!empty($saved_items_id)) {
                foreach ($saved_items_id as $v_items_id) {
                    $items_info = $this->estimates_model->check_by(array('saved_items_id' => $v_items_id), 'tbl_saved_items');
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
                    $data['estimates_id'] = $estimates_id;
                    $data['item_name'] = $items_info->item_name;
                    $data['item_desc'] = $items_info->item_desc;
                    $data['hsn_code'] = $items_info->hsn_code;
                    $data['unit_cost'] = $items_info->unit_cost;
                    $data['item_tax_rate'] = '0.00';
                    $data['item_tax_name'] = json_encode($tax_name);
                    $data['item_tax_total'] = $items_info->item_tax_total;
                    $data['total_cost'] = $items_info->unit_cost;

                    $this->estimates_model->_table_name = 'tbl_estimate_items';
                    $this->estimates_model->_primary_key = 'estimate_items_id';
                    $items_id = $this->estimates_model->save($data);
                    $action = 'activity_estimates_items_added';
                    $msg = lang('estimate_item_save');
                    $activity = array(
                        'user' => $this->session->userdata('user_id'),
                        'module' => 'estimates',
                        'module_field_id' => $items_id,
                        'activity' => $action,
                        'icon' => 'fa-shopping-cart',
                        'link' => 'admin/estimates/index/estimates_details/' . $estimates_id,
                        'value1' => $items_info->item_name
                    );
                    $this->estimates_model->_table_name = 'tbl_activities';
                    $this->estimates_model->_primary_key = 'activities_id';
                    $this->estimates_model->save($activity);
                }
                $type = "success";
                $this->update_invoice_tax($saved_items_id, $estimates_id);

            } else {
                $type = "error";
                $msg = 'Please Select a items';
            }
            $message = $msg;
            set_message($type, $message);
            redirect('admin/estimates/index/estimates_details/' . $estimates_id);
        } else {
            set_message('error', lang('there_in_no_value'));
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/estimates');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    function update_invoice_tax($saved_items_id, $estimates_id)
    {

        $invoice_info = $this->estimates_model->check_by(array('estimates_id' => $estimates_id), 'tbl_estimates');
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
                $items_info = $this->estimates_model->check_by(array('saved_items_id' => $v_items_id), 'tbl_saved_items');

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

            $this->estimates_model->_table_name = 'tbl_estimates';
            $this->estimates_model->_primary_key = 'estimates_id';
            $this->estimates_model->save($invoice_data, $estimates_id);
        }
        return true;
    }

    public
    function add_item($id = NULL)
    {
        $data = $this->estimates_model->array_from_post(array('estimates_id', 'item_order'));
        $can_edit = $this->estimates_model->can_action('tbl_estimates', 'edit', array('estimates_id' => $data['estimates_id']));
        $edited = can_action('14', 'edited');
        if (!empty($can_edit) && !empty($edited)) {
            $quantity = $this->input->post('quantity', TRUE);
            $array_data = $this->estimates_model->array_from_post(array('item_name', 'item_desc', 'item_tax_rate', 'unit_cost'));
            if (!empty($quantity)) {
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
                    $this->estimates_model->_table_name = 'tbl_estimate_items';
                    $this->estimates_model->_primary_key = 'estimate_items_id';
                    if (!empty($id)) {
                        $estimate_items_id = $id;
                        $this->estimates_model->save($data, $id);
                        $action = ('activity_estimates_items_updated');
                    } else {
                        $estimate_items_id = $this->estimates_model->save($data);
                        $action = 'activity_estimates_items_added';
                    }
                    $activity = array(
                        'user' => $this->session->userdata('user_id'),
                        'module' => 'estimates',
                        'module_field_id' => $estimate_items_id,
                        'activity' => $action,
                        'icon' => 'fa-shopping-cart',
                        'link' => 'admin/estimates/index/estimates_details/' . $data['estimates_id'],
                        'value1' => $data['item_name']
                    );
                    $this->estimates_model->_table_name = 'tbl_activities';
                    $this->estimates_model->_primary_key = 'activities_id';
                    $this->estimates_model->save($activity);
                }
            }
            // messages for user
            $type = "success";
            $message = lang('estimate_item_save');
            set_message($type, $message);
            redirect('admin/estimates/index/estimates_details/' . $data['estimates_id']);
        } else {
            set_message('error', lang('there_in_no_value'));
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/estimates');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public
    function clone_estimate($estimates_id)
    {
        $edited = can_action('14', 'edited');
        $can_edit = $this->estimates_model->can_action('tbl_estimates', 'edit', array('estimates_id' => $estimates_id));
        if (!empty($can_edit) && !empty($edited) && !empty($estimates_id)) {
            $data['estimate_info'] = $this->estimates_model->check_by(array('estimates_id' => $estimates_id), 'tbl_estimates');
            // get all client
            $this->estimates_model->_table_name = 'tbl_client';
            $this->estimates_model->_order_by = 'client_id';
            $data['all_client'] = $this->estimates_model->get();

            $data['modal_subview'] = $this->load->view('admin/estimates/_modal_clone_estimate', $data, FALSE);
            $this->load->view('admin/_layout_modal', $data);
        } else {
            set_message('error', lang('there_in_no_value'));
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/estimates');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public
    function cloned_estimate($id)
    {
        $edited = can_action('14', 'edited');
        $can_edit = $this->estimates_model->can_action('tbl_estimates', 'edit', array('estimates_id' => $id));
        if (!empty($can_edit) && !empty($edited)) {
            if (config_item('increment_estimate_number') == 'FALSE') {
                $this->load->helper('string');
                $reference_no = config_item('estimate_prefix') . ' ' . random_string('nozero', 6);
            } else {
                $reference_no = config_item('estimate_prefix') . ' ' . $this->estimates_model->generate_estimate_number();
            }

            $invoice_info = $this->estimates_model->check_by(array('estimates_id' => $id), 'tbl_estimates');
            $data['estimate_date'] = date('Y-m-d', strtotime($this->input->post('estimate_date', TRUE)));
            if (empty($data['estimate_date'])) {
                $data['estimate_date'] = date('Y-m-d');
            }
            // save into invoice table
            $new_invoice = array(
                'reference_no' => $reference_no,
                'client_id' => $this->input->post('client_id', true),
                'project_id' => $invoice_info->project_id,
                'estimate_date' => date('Y-m-d', strtotime($this->input->post('estimate_date', TRUE))),
                'estimate_month' => date('Y-m', strtotime($this->input->post('estimate_date', TRUE))),
                'estimate_year' => date('Y', strtotime($this->input->post('estimate_date', TRUE))),
                'due_date' => date('Y-m-d', strtotime($this->input->post('due_date', TRUE))),
                'notes' => $invoice_info->notes,
                'total_tax' => $invoice_info->total_tax,
                'tax' => $invoice_info->tax,
                'discount_type' => $invoice_info->discount_type,
                'discount_percent' => $invoice_info->discount_percent,
                'user_id' => $invoice_info->user_id,
                'adjustment' => $invoice_info->adjustment,
                'discount_total' => $invoice_info->discount_total,
                'show_quantity_as' => $invoice_info->show_quantity_as,
                'currency' => $invoice_info->currency,
                'status' => $invoice_info->status,
                'date_sent' => $invoice_info->date_sent,
                'date_saved' => $invoice_info->date_saved,
                'emailed' => $invoice_info->emailed,
                'show_client' => $invoice_info->show_client,
                'invoiced' => $invoice_info->invoiced,
                'invoices_id' => $invoice_info->invoices_id,
                'permission' => $invoice_info->permission,
            );
            $this->estimates_model->_table_name = "tbl_estimates"; //table name
            $this->estimates_model->_primary_key = "estimates_id";
            $new_invoice_id = $this->estimates_model->save($new_invoice);

            $invoice_items = $this->db->where('estimates_id', $id)->get('tbl_estimate_items')->result();

            if (!empty($invoice_items)) {
                foreach ($invoice_items as $new_item) {
                    $items = array(
                        'estimates_id' => $new_invoice_id,
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
                    $this->estimates_model->_table_name = "tbl_estimate_items"; //table name
                    $this->estimates_model->_primary_key = "estimate_items_id";
                    $this->estimates_model->save($items);
                }
            }
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'estimates',
                'module_field_id' => $new_invoice_id,
                'activity' => ('activity_clone_estimate'),
                'icon' => 'fa-shopping-cart',
                'link' => 'admin/estimates/index/estimates_details/' . $new_invoice_id,
                'value1' => ' from ' . $invoice_info->reference_no . ' to ' . $reference_no,
            );
            // Update into tbl_project
            $this->estimates_model->_table_name = "tbl_activities"; //table name
            $this->estimates_model->_primary_key = "activities_id";
            $this->estimates_model->save($activities);

            // messages for user
            $type = "success";
            $message = lang('estimate_created');
            set_message($type, $message);
            redirect('admin/estimates/index/estimates_details/' . $new_invoice_id);
        } else {
            set_message('error', lang('there_in_no_value'));
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/estimates');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public
    function change_status($action, $id)
    {
        $can_edit = $this->estimates_model->can_action('tbl_estimates', 'edit', array('estimates_id' => $id));
        $edited = can_action('14', 'edited');
        if (!empty($can_edit) && !empty($edited)) {
            $where = array('estimates_id' => $id);
            if ($action == 'hide') {
                $data = array('show_client' => 'No');
            } elseif ($action == 'show') {
                $data = array('show_client' => 'Yes');
            } elseif ($action == 'sent') {
                $data = array('emailed' => 'Yes', 'date_sent' => date("Y-m-d H:i:s", time()), 'status' => 'sent');
            } elseif (!empty($action)) {
                $data = array('status' => $action);
            } else {
                $data = array('show_client' => 'Yes');
            }
            $this->estimates_model->set_action($where, $data, 'tbl_estimates');
            // messages for user
            $type = "success";
            $message = lang('estimate_status_changed', $action);
            set_message($type, $message);
            redirect('admin/estimates/index/estimates_details/' . $id);
        } else {
            set_message('error', lang('there_in_no_value'));
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/estimates');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public
    function delete($action, $estimates_id, $item_id = NULL)
    {
        $can_delete = $this->estimates_model->can_action('tbl_estimates', 'delete', array('estimates_id' => $estimates_id));
        $deleted = can_action('14', 'deleted');
        if (!empty($can_delete) && !empty($deleted)) {
            if ($action == 'delete_item') {
                $this->estimates_model->_table_name = 'tbl_estimate_items';
                $this->estimates_model->_primary_key = 'estimate_items_id';
                $this->estimates_model->delete($item_id);
            } elseif ($action == 'delete_estimates') {

                $this->estimates_model->_table_name = 'tbl_estimate_items';
                $this->estimates_model->delete_multiple(array('estimates_id' => $estimates_id));

                $this->estimates_model->_table_name = 'tbl_reminders';
                $this->estimates_model->delete_multiple(array('module' => 'estimate', 'module_id' => $estimates_id));

                $this->estimates_model->_table_name = 'tbl_pinaction';
                $this->estimates_model->delete_multiple(array('module_name' => 'estimates', 'module_id' => $estimates_id));

                $this->estimates_model->_table_name = 'tbl_estimates';
                $this->estimates_model->_primary_key = 'estimates_id';
                $this->estimates_model->delete($estimates_id);
            }
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'estimates',
                'module_field_id' => $estimates_id,
                'activity' => ('activity_' . $action),
                'icon' => 'fa-shopping-cart',
                'value1' => $action
            );

            $this->estimates_model->_table_name = 'tbl_activities';
            $this->estimates_model->_primary_key = 'activities_id';
            $this->estimates_model->save($activity);
            $type = 'success';
            if ($action == 'delete_item') {
                $text = lang('estimate_item_deleted');
//                set_message($type, $text);
//                redirect('admin/estimates/index/estimates_details/' . $estimates_id);
            } else {
                $text = lang('estimate_deleted');

//                set_message($type, $text);
//                redirect('admin/estimates');
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
    function send_estimates_email($estimates_id, $row = null)
    {
        if (!empty($row)) {
            $estimates_info = $this->estimates_model->check_by(array('estimates_id' => $estimates_id), 'tbl_estimates');
            $client_info = $this->estimates_model->check_by(array('client_id' => $estimates_info->client_id), 'tbl_client');
            if (!empty($client_info)) {
                $client = $client_info->name;
                $currency = $this->estimates_model->client_currency_symbol($client_info->client_id);;
            } else {
                $client = '-';
                $currency = $this->estimates_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
            }

            $amount = $this->estimates_model->estimate_calculation('total', $estimates_info->estimates_id);
            $currency = $currency->code;
            $email_template = email_templates(array('email_group' => 'estimate_email'), $estimates_info->client_id);
            $message = $email_template->template_body;
            $ref = $estimates_info->reference_no;
            $subject = $email_template->subject;
        } else {
            $message = $this->input->post('message', TRUE);
            $ref = $this->input->post('ref', TRUE);
            $subject = $this->input->post('subject', TRUE);
            $client = $this->input->post('client_name', TRUE);
            $amount = $this->input->post('amount', true);
            $currency = $this->input->post('currency', TRUE);
        }
        $client_name = str_replace("{CLIENT}", $client, $message);
        $Ref = str_replace("{ESTIMATE_REF}", $ref, $client_name);
        $Amount = str_replace("{AMOUNT}", $amount, $Ref);
        $Currency = str_replace("{CURRENCY}", $currency, $Amount);
        $link = str_replace("{ESTIMATE_LINK}", base_url() . 'client/estimates/index/estimates_details/' . $estimates_id, $Currency);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $link);


        $this->send_email_estimates($estimates_id, $message, $subject); // Email estimates

        $data = array('status' => 'sent', 'emailed' => 'Yes', 'date_sent' => date("Y-m-d H:i:s", time()));

        $this->estimates_model->_table_name = 'tbl_estimates';
        $this->estimates_model->_primary_key = 'estimates_id';
        $this->estimates_model->save($data, $estimates_id);

        // Log Activity
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'estimates',
            'module_field_id' => $estimates_id,
            'activity' => 'activity_estimates_sent',
            'icon' => 'fa-shopping-cart',
            'link' => 'admin/estimates/index/estimates_details/' . $estimates_id,
            'value1' => $ref
        );
        $this->estimates_model->_table_name = 'tbl_activities';
        $this->estimates_model->_primary_key = 'activities_id';
        $this->estimates_model->save($activity);

        $type = 'success';
        $text = lang('estimate_email_sent');
        set_message($type, $text);
        redirect('admin/estimates/index/estimates_details/' . $estimates_id);
    }

    function send_email_estimates($estimates_id, $message, $subject)
    {
        $estimates_info = $this->estimates_model->check_by(array('estimates_id' => $estimates_id), 'tbl_estimates');
        $client_info = $this->estimates_model->check_by(array('client_id' => $estimates_info->client_id), 'tbl_client');

        $recipient = $client_info->email;

        $data['message'] = $message;

        $message = $this->load->view('email_template', $data, TRUE);
        $params = array(
            'recipient' => $recipient,
            'subject' => $subject,
            'message' => $message
        );
        $params['resourceed_file'] = 'uploads/' . lang('estimate') . '_' . $estimates_info->reference_no . '.pdf';
        $params['resourcement_url'] = base_url() . 'uploads/' . lang('estimate') . '_' . $estimates_info->reference_no . '.pdf';

        $this->attach_pdf($estimates_id);
        $this->estimates_model->send_email($params);
        //Delete estimate in tmp folder
        if (is_file('uploads/' . lang('estimate') . '_' . $estimates_info->reference_no . '.pdf')) {
            unlink('uploads/' . lang('estimate') . '_' . $estimates_info->reference_no . '.pdf');
        }
        // send notification to client
        if (!empty($client_info->primary_contact)) {
            $notifyUser = array($client_info->primary_contact);
        } else {
            $user_info = $this->estimates_model->check_by(array('company' => $estimates_info->client_id), 'tbl_account_details');
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
                        'link' => 'client/estimates/index/estimates_details/' . $estimates_id,
                        'value' => lang('estimate') . ' ' . $estimates_info->reference_no,
                    ));
                }
            }
            show_notification($notifyUser);
        }
    }

    public
    function attach_pdf($id)
    {
        $data['page'] = lang('estimates');
        $data['estimates_info'] = $this->estimates_model->check_by(array('estimates_id' => $id), 'tbl_estimates');
        $data['title'] = lang('estimates'); //Page title
        $this->load->helper('dompdf');
        $html = $this->load->view('admin/estimates/estimates_pdf', $data, TRUE);
        $result = pdf_create($html, slug_it(lang('estimate') . '_' . $data['estimates_info']->reference_no), 1, null, true);
        return $result;
    }

    function estimate_email($estimates_id)
    {
        $data['estimates_info'] = $this->estimates_model->check_by(array('estimates_id' => $estimates_id), 'tbl_estimates');
        $estimates_info = $data['estimates_info'];
        $client_info = $this->estimates_model->check_by(array('client_id' => $data['estimates_info']->client_id), 'tbl_client');

        $recipient = $client_info->email;

        $message = $this->load->view('admin/estimates/estimates_pdf', $data, TRUE);

        $data['message'] = $message;

        $message = $this->load->view('email_template', $data, TRUE);
        $params = array(
            'recipient' => $recipient,
            'subject' => '[ ' . config_item('company_name') . ' ]' . ' New Estimate' . ' ' . $data['estimates_info']->reference_no,
            'message' => $message
        );
        $params['resourceed_file'] = '';

        $this->estimates_model->send_email($params);

        $data = array('emailed' => 'Yes', 'date_sent' => date("Y-m-d H:i:s", time()));

        $this->estimates_model->_table_name = 'tbl_estimates';
        $this->estimates_model->_primary_key = 'estimates_id';
        $this->estimates_model->save($data, $estimates_id);

        // Log Activity
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'estimates',
            'module_field_id' => $estimates_id,
            'activity' => 'activity_estimates_sent',
            'icon' => 'fa-shopping-cart',
            'link' => 'admin/estimates/index/estimates_details/' . $estimates_id,
            'value1' => $estimates_info->reference_no
        );
        $this->estimates_model->_table_name = 'tbl_activities';
        $this->estimates_model->_primary_key = 'activities_id';
        $this->estimates_model->save($activity);

        // send notification to client
        if (!empty($client_info->primary_contact)) {
            $notifyUser = array($client_info->primary_contact);
        } else {
            $user_info = $this->estimates_model->check_by(array('company' => $estimates_info->client_id), 'tbl_account_details');
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
                        'link' => 'client/estimates/index/estimates_details/' . $estimates_id,
                        'value' => lang('estimate') . ' ' . $estimates_info->reference_no,
                    ));
                }
            }
            show_notification($notifyUser);
        }


        $type = 'success';
        $text = lang('estimate_email_sent');
        set_message($type, $text);
        redirect('admin/estimates/index/estimates_details/' . $estimates_id);
    }

    public
    function convert_to_invoice($id)
    {
        $data['title'] = lang('convert_to_invoice');
        $edited = can_action('14', 'edited');
        $can_edit = $this->estimates_model->can_action('tbl_estimates', 'edit', array('estimates_id' => $id));
        if (!empty($can_edit) && !empty($edited)) {
            // get all client
            $this->estimates_model->_table_name = 'tbl_client';
            $this->estimates_model->_order_by = 'client_id';
            $data['all_client'] = $this->estimates_model->get();
            // get permission user
            $data['permission_user'] = $this->estimates_model->all_permission_user('14');

            $data['estimates_info'] = $this->estimates_model->check_by(array('estimates_id' => $id), 'tbl_estimates');

            $data['modal_subview'] = $this->load->view('admin/estimates/convert_to_invoice', $data, FALSE);
            $this->load->view('admin/_layout_modal_large', $data);
        } else {
            set_message('error', lang('there_in_no_value'));
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/estimates');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }

    }

    public
    function converted($estimate_id)
    {
        $data = $this->estimates_model->array_from_post(array('reference_no', 'client_id', 'project_id', 'discount_type', 'discount_percent', 'user_id', 'adjustment', 'discount_total', 'show_quantity_as'));

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
        $currency = $this->estimates_model->client_currency_symbol($data['client_id']);
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
                $assigned_to = $this->estimates_model->array_from_post(array('assigned_to'));
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
                redirect('admin/estimates');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }

        // get all client
        $this->estimates_model->_table_name = 'tbl_invoices';
        $this->estimates_model->_primary_key = 'invoices_id';

        $invoice_id = $this->estimates_model->save($data);
        $recuring_frequency = $this->input->post('recuring_frequency', TRUE);

        if (!empty($recuring_frequency) && $recuring_frequency != 'none') {
            $recur_data = $this->estimates_model->array_from_post(array('recur_start_date', 'recur_end_date'));
            $recur_data['recuring_frequency'] = $recuring_frequency;
            $this->get_recuring_frequency($invoice_id, $recur_data); // set recurring
        }
        // save items
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
                    $this->estimates_model->_table_name = 'tbl_invoices';
                    $this->estimates_model->_primary_key = 'invoices_id';
                    $this->estimates_model->save($mdata, $inv_id);
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
                $this->estimates_model->_table_name = 'tbl_items';
                $this->estimates_model->_primary_key = 'items_id';
                if (!empty($qty_calculation) && $qty_calculation == 'Yes') {
                    if (!empty($items['saved_items_id']) && $items['saved_items_id'] != 'undefined') {
                        $this->estimates_model->reduce_items($items['saved_items_id'], $items['quantity']);
                    }
                }
                $items_id = $this->estimates_model->save($items);
                $index++;
            }
        }

        $e_data = array('status' => 'accepted', 'invoiced' => 'Yes', 'invoices_id' => $invoice_id);

        $this->estimates_model->_table_name = 'tbl_estimates';
        $this->estimates_model->_primary_key = 'estimates_id';
        $this->estimates_model->save($e_data, $estimate_id);

        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'estimates',
            'module_field_id' => $estimate_id,
            'activity' => 'activity_estimate_convert_to_invoice',
            'icon' => 'fa-shopping-cart',
            'link' => 'admin/estimates/index/estimates_details/' . $estimate_id,
            'value1' => $data['reference_no']
        );
        $this->estimates_model->_table_name = 'tbl_activities';
        $this->estimates_model->_primary_key = 'activities_id';
        $this->estimates_model->save($activity);

        // send notification to client
        if (!empty($data['client_id'])) {
            $client_info = $this->estimates_model->check_by(array('client_id' => $data['client_id']), 'tbl_client');
            if (!empty($client_info->primary_contact)) {
                $notifyUser = array($client_info->primary_contact);
            } else {
                $user_info = $this->estimates_model->check_by(array('company' => $data['client_id']), 'tbl_account_details');
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
                        'description' => 'proposal_convert_to_invoice',
                        'link' => 'client/invoice/manage_invoice/invoice_details/' . $invoice_id,
                        'value' => $data['reference_no'],
                    ));
                }
            }
            show_notification($notifyUser);
        }
        // messages for user
        $type = "success";
        $message = lang('estimate_invoiced');
        set_message($type, $message);
        redirect('admin/estimates/index/estimates_details/' . $estimate_id);
    }

    function return_items($items_id)
    {
        $items_info = $this->db->where('items_id', $items_id)->get('tbl_items')->row();
        if (!empty($items_info->saved_items_id)) {
            $this->estimates_model->return_items($items_info->saved_items_id, $items_info->quantity);
        }
        return true;

    }

    function check_existing_qty($items_id, $qty)
    {
        $items_info = $this->db->where('items_id', $items_id)->get('tbl_items')->row();
        if (!empty($items_info)) {
            if ($items_info->quantity != $qty) {
                if ($qty > $items_info->quantity) {
                    $reduce_qty = $qty - $items_info->quantity;
                    if (!empty($items_info->saved_items_id)) {
                        $this->estimates_model->reduce_items($items_info->saved_items_id, $reduce_qty);
                    }
                }
                if ($qty < $items_info->quantity) {
                    $return_qty = $items_info->quantity - $qty;
                    if (!empty($items_info->saved_items_id)) {
                        $this->estimates_model->return_items($items_info->saved_items_id, $return_qty);
                    }
                }
            }
        }
        return true;

    }

    function get_recuring_frequency($invoices_id, $recur_data)
    {
        $recur_days = $this->get_calculate_recurring_days($recur_data['recuring_frequency']);
        $due_date = $this->estimates_model->get_table_field('tbl_invoices', array('invoices_id' => $invoices_id), 'due_date');

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
        $this->estimates_model->_table_name = 'tbl_invoices';
        $this->estimates_model->_primary_key = 'invoices_id';
        $this->estimates_model->save($update_invoice, $invoices_id);
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


}
