<?php

class Invoice_Model extends MY_Model
{
    public $_table_name;
    public $_order_by;
    public $_primary_key;

    public function get_payment_status($invoice_id)
    {
        $payment_made = round($this->get_invoice_paid_amount($invoice_id), 2);
        $due = $this->get_invoice_due_amount($invoice_id);
        $invoice_info = $this->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');
        if ($invoice_info->status == 'draft') {
            return lang('draft');
        } elseif ($invoice_info->status == 'Cancelled') {
            return lang('cancelled');
        } elseif ($payment_made < 1) {
            return lang('not_paid');
        } elseif ($due <= 0) {
            return lang('fully_paid');
        } else {
            return lang('partially_paid');
        }
    }

    public function invoice_payable($id)
    {
        return ($this->get_invoice_cost($id) + $this->get_invoice_tax_amount($id) - $this->get_invoice_discount($id));
    }


    public function invoice_perc($invoice)
    {
        $invoice_payment = $this->invoice_payment($invoice);
        $invoice_payable = $this->invoice_payable($invoice);
        if ($invoice_payable < 1 OR $invoice_payment < 1) {
            $perc_paid = 0;
        } else {
            $perc_paid = ($invoice_payment / $invoice_payable) * 100;
        }
        return round($perc_paid);
    }


    public function invoice_payment($invoice)
    {
        $this->ci->db->where('invoice', $invoice);
        $this->ci->db->select_sum('amount');
        $query = $this->ci->db->get('payments');
        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->amount;
        }
    }

    function ordered_items_by_id($id, $type = 'invoices')
    {
        $table = ($type == 'invoices' ? '' : 'estimate_') . 'tbl_items';
        $result = $this->db->where($type . '_id', $id)->order_by('order', 'asc')->get($table)->result();
        return $result;
    }

    function calculate_to($invoice_value, $invoice_id)
    {
        switch ($invoice_value) {
            case 'invoice_cost':
                return $this->get_invoice_cost($invoice_id);
                break;
            case 'tax':
                return $this->get_invoice_tax_amount($invoice_id);
                break;
            case 'discount':
                return $this->get_invoice_discount($invoice_id);
                break;
            case 'paid_amount':
                return $this->get_invoice_paid_amount($invoice_id);
                break;
            case 'invoice_due':
                return $this->get_invoice_due_amount($invoice_id);
                break;
            case 'total':
                return $this->get_invoice_total_amount($invoice_id);
                break;
        }
    }

    function get_invoice_cost($invoice_id)
    {
        $this->db->select_sum('total_cost');
        $this->db->where('invoices_id', $invoice_id);
        $this->db->from('tbl_items');
        $query_result = $this->db->get();
        $cost = $query_result->row();
        if (!empty($cost->total_cost)) {
            $result = $cost->total_cost;
        } else {
            $result = '0';
        }
        return $result;
    }


    public function get_invoice_tax_amount($invoice_id)
    {
        $invoice_info = $this->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');
        if (!empty($invoice_info->total_tax)) {
            $tax_info = json_decode($invoice_info->total_tax);
        }
        $tax = 0;
        if (!empty($tax_info)) {
            $total_tax = $tax_info->total_tax;
            if (!empty($total_tax)) {
                foreach ($total_tax as $t_key => $v_tax_info) {
                    $tax += $v_tax_info;
                }
                }
            }
        return $tax;
    }

    public function get_invoice_discount($invoice_id)
    {
        $invoice_info = $this->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');
        if (empty($invoice_info)) {
            $invoice_info = new stdClass();
            $invoice_info->discount_total = 0;

        }
        return $invoice_info->discount_total;

    }

    public function get_invoice_paid_amount($invoice_id)
    {

        $this->db->select_sum('amount');
        $this->db->where('invoices_id', $invoice_id);
        $this->db->from('tbl_payments');
        $query_result = $this->db->get();
        $amount = $query_result->row();
        $tax = $this->get_invoice_tax_amount($invoice_id);
        if (!empty($amount->amount)) {
            $result = $amount->amount;
        } else {
            $result = '0';
        }
        return $result;
    }

    public function get_invoice_due_amount($invoice_id)
    {
        $invoice_info = $this->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');
        if (empty($invoice_info)) {
            $invoice_info = new stdClass();
            $invoice_info->adjustment = 0;

        }
        $tax = $this->get_invoice_tax_amount($invoice_id);
        $discount = $this->get_invoice_discount($invoice_id);
        $invoice_cost = $this->get_invoice_cost($invoice_id);
        $payment_made = $this->get_invoice_paid_amount($invoice_id);
        $due_amount = (($invoice_cost - $discount) + $tax) - $payment_made + $invoice_info->adjustment;
        if ($due_amount <= 0) {
            $due_amount = 0;
        }
        return $due_amount;
    }

    public function get_invoice_total_amount($invoice_id)
    {

        $invoice_info = $this->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');
        $tax = $this->get_invoice_tax_amount($invoice_id);
        $discount = $this->get_invoice_discount($invoice_id);
        $invoice_cost = $this->get_invoice_cost($invoice_id);
        $payment_made = $this->get_invoice_paid_amount($invoice_id);
        if (empty($invoice_info)) {
            $invoice_info = new stdClass();
            $invoice_info->adjustment = 0;

        }
        $total_amount = $invoice_cost - $discount + $tax + $invoice_info->adjustment;
        if ($total_amount <= 0) {
            $total_amount = 0;
        }
        return $total_amount;
    }

    function all_invoice_amount()
    {
        $invoices = $this->db->get('tbl_invoices')->result();
        $cost[] = array();
        foreach ($invoices as $invoice) {
            $tax = round($this->get_invoice_tax_amount($invoice->invoices_id));
            $discount = round($this->get_invoice_discount($invoice->invoices_id));
            $invoice_cost = round($this->get_invoice_cost($invoice->invoices_id));
            $cost[] = ($invoice_cost + $tax) - $discount;
        }
        if (is_array($cost)) {
            return round(array_sum($cost), 2);
        } else {
            return 0;
        }
    }

    function all_outstanding()
    {
        $invoices = $this->db->where(array('status !=' => 'draft'))->get('tbl_invoices')->result();
        $due[] = array();
        foreach ($invoices as $invoice) {
            $due[] = $this->get_invoice_due_amount($invoice->invoices_id);
        }
        if (is_array($due)) {
            return round(array_sum($due), 2);
        } else {
            return 0;
        }
    }

    function client_outstanding($client_id, $project_id = null)
    {
        $due[] = array();
        if (!empty($project_id)) {
            $invoices_info = $this->db->where(array('project_id' => $project_id, 'status !=' => 'draft'))->get('tbl_invoices')->result();
        } else {
            $invoices_info = $this->db->where(array('client_id' => $client_id, 'status !=' => 'draft'))->get('tbl_invoices')->result();
        }

        foreach ($invoices_info as $v_invoice) {
            $due[] = $this->get_invoice_due_amount($v_invoice->invoices_id);
        }
        if (is_array($due)) {
            return round(array_sum($due), 2);
        } else {
            return 0;
        }
    }

    public function check_for_merge_invoice($client_id, $current_invoice)
    {

        $invoice_info = $this->db->where('client_id', $client_id)->get('tbl_invoices')->result();

        foreach ($invoice_info as $v_invoice) {
            if ($v_invoice->invoices_id != $current_invoice) {
                $payment_status = $this->get_payment_status($v_invoice->invoices_id);
                if ($payment_status == lang('not_paid') || $payment_status == lang('draft')) {
                    $invoice[] = $v_invoice;
                }
            }
        }
        if (!empty($invoice)) {
            return $invoice;
        } else {
            return array();
        }
    }

    public function get_invoice_filter()
    {
        $all_invoice = $this->get_permission('tbl_invoices');
        if (!empty($all_invoice)) {
            $all_invoice = array_reverse($all_invoice);
            foreach ($all_invoice as $v_invoices) {
                $year[] = date('Y', strtotime($v_invoices->invoice_date));
            }
        }
        if (!empty($year)) {
            $result = array_unique($year);
        }

        $statuses = array(
            array(
                'id' => 1,
                'value' => 'paid',
                'name' => lang('paid'),
                'order' => 1,
            ),
            array(
                'id' => 2,
                'value' => 'not_paid',
                'name' => lang('not_paid'),
                'order' => 2,
            ),
            array(
                'id' => 3,
                'value' => 'partially_paid',
                'name' => lang('partially_paid'),
                'order' => 3,
            ),
            array(
                'id' => 1,
                'value' => 'draft',
                'name' => lang('draft'),
                'order' => 1,
            ), array(
                'id' => 1,
                'value' => 'cancelled',
                'name' => lang('cancelled'),
                'order' => 1,
            ), array(
                'id' => 1,
                'value' => 'overdue',
                'name' => lang('overdue'),
                'order' => 1,
            ),
            array(
                'id' => 4,
                'value' => 'recurring',
                'name' => lang('recurring'),
                'order' => 4,
            ),
            array(
                'id' => 4,
                'value' => 'last_month',
                'name' => lang('last_month'),
                'order' => 4,
            ),
            array(
                'id' => 4,
                'value' => 'this_months',
                'name' => lang('this_months'),
                'order' => 4,
            )
        );
        if (!empty($result)) {
            foreach ($result as $v_year) {
                $test = array(
                    'id' => 1,
                    'value' => '_' . $v_year,
                    'name' => $v_year,
                    'order' => 1);
                if (!empty($test)) {
                    array_push($statuses, $test);
                }
            }
        }
        return $statuses;
    }

    // Get a list of recurring invoices
    public function recurring_invoices($client_id = null)
    {
        if (!empty($client_id)) {
            return $this->db->where(array('recurring' => 'Yes', 'client_id' => $client_id))->get('tbl_invoices')->result();
        } else {
            return $this->db->where(array('recurring' => 'Yes', 'invoices_id >' => 0))->get('tbl_invoices')->result();
        }
    }

    public function get_client_invoices($filterBy = null, $client_id = null)
    {
        $all_invoice = get_result('tbl_invoices', array('client_id' => $client_id, 'status !=' => 'draft'));
        if (empty($filterBy) || !empty($filterBy) && $filterBy == 'all') {
            return $all_invoice;
        } elseif ($filterBy == 'recurring') {
            return $this->recurring_invoices($client_id);
        } else {
            if (!empty($all_invoice)) {
                $all_invoice = array_reverse($all_invoice);
                foreach ($all_invoice as $v_invoices) {
                    if ($filterBy == 'paid') {
                        if ($this->get_payment_status($v_invoices->invoices_id) == lang('fully_paid')) {
                            $invoice[] = $v_invoices;
                        }
                    } else if ($filterBy == 'not_paid') {
                        if ($this->get_payment_status($v_invoices->invoices_id) == lang('not_paid')) {
                            $invoice[] = $v_invoices;
                        }
                    } else if ($filterBy == 'draft') {
                        if ($this->get_payment_status($v_invoices->invoices_id) == lang('draft')) {
                            $invoice[] = $v_invoices;
                        }
                    } else if ($filterBy == 'partially_paid') {
                        if ($this->get_payment_status($v_invoices->invoices_id) == lang('partially_paid')) {
                            $invoice[] = $v_invoices;
                        }
                    } else if ($filterBy == 'cancelled') {
                        if ($this->get_payment_status($v_invoices->invoices_id) == lang('cancelled')) {
                            $invoice[] = $v_invoices;
                        }
                    } else if ($filterBy == 'overdue') {
                        $payment_status = $this->get_payment_status($v_invoices->invoices_id);
                        if (strtotime($v_invoices->due_date) < strtotime(date('Y-m-d')) && $payment_status != lang('fully_paid')) {
                            $invoice[] = $v_invoices;
                        }
                    } else if ($filterBy == 'last_month' || $filterBy == 'this_months') {
                        if ($filterBy == 'last_month') {
                            $month = date('Y-m', strtotime('-1 months'));
                        } else {
                            $month = date('Y-m');
                        }
                        if (strtotime($v_invoices->invoice_month) == strtotime($month)) {
                            $invoice[] = $v_invoices;
                        }
                    } else if (strstr($filterBy, '_')) {
                        $year = str_replace('_', '', $filterBy);
                        if (strtotime($v_invoices->invoice_year) == strtotime($year)) {
                            $invoice[] = $v_invoices;
                        }
                    }

                }
            }
        }
        if (!empty($invoice)) {
            return $invoice;
        } else {
            return array();
        }
    }

    public function get_invoices($filterBy = null, $client_id = null)
    {
        if (!empty($client_id)) {
            $all_invoice = get_result('tbl_invoices', array('client_id' => $client_id));
        } else {
            $all_invoice = $this->get_permission('tbl_invoices');
        }
        if (empty($filterBy) || !empty($filterBy) && $filterBy == 'all') {
            return $all_invoice;
        } elseif ($filterBy == 'recurring') {
            return $this->recurring_invoices($client_id);
        } else {
            if (!empty($all_invoice)) {
                $all_invoice = array_reverse($all_invoice);
                foreach ($all_invoice as $v_invoices) {
                    if ($filterBy == 'paid') {
                        if ($this->get_payment_status($v_invoices->invoices_id) == lang('fully_paid')) {
                            $invoice[] = $v_invoices;
                        }
                    } else if ($filterBy == 'not_paid') {
                        if ($this->get_payment_status($v_invoices->invoices_id) == lang('not_paid')) {
                            $invoice[] = $v_invoices;
                        }
                    } else if ($filterBy == 'draft') {
                        if ($this->get_payment_status($v_invoices->invoices_id) == lang('draft')) {
                            $invoice[] = $v_invoices;
                        }
                    } else if ($filterBy == 'partially_paid') {
                        if ($this->get_payment_status($v_invoices->invoices_id) == lang('partially_paid')) {
                            $invoice[] = $v_invoices;
                        }
                    } else if ($filterBy == 'cancelled') {
                        if ($this->get_payment_status($v_invoices->invoices_id) == lang('cancelled')) {
                            $invoice[] = $v_invoices;
                        }
                    } else if ($filterBy == 'overdue') {
                        $payment_status = $this->get_payment_status($v_invoices->invoices_id);
                        if (strtotime($v_invoices->due_date) < strtotime(date('Y-m-d')) && $payment_status != lang('fully_paid')) {
                            $invoice[] = $v_invoices;
                        }
                    } else if ($filterBy == 'last_month' || $filterBy == 'this_months') {
                        if ($filterBy == 'last_month') {
                            $month = date('Y-m', strtotime('-1 months'));
                        } else {
                            $month = date('Y-m');
                        }
                        if (strtotime($v_invoices->invoice_month) == strtotime($month)) {
                            $invoice[] = $v_invoices;
                        }
                    } else if (strstr($filterBy, '_')) {
                        $year = str_replace('_', '', $filterBy);
                        if (strtotime($v_invoices->invoice_year) == strtotime($year)) {
                            $invoice[] = $v_invoices;
                        }
                    }

                }
            }
        }
        if (!empty($invoice)) {
            return $invoice;
        } else {
            return array();
        }
    }

    public function get_client_wise_invoice()
    {
        $all_invoice = $this->get_permission('tbl_invoices');
        $client_invoice = array();
        if (!empty($all_invoice)) {
            $all_invoice = array_reverse($all_invoice);
            foreach ($all_invoice as $v_invoices) {
                $due = $this->calculate_to('invoice_due', $v_invoices->invoices_id);
                if ($due != 0) {
                    $client_invoice[$v_invoices->client_id][] = $v_invoices;
                }
            }
            return $client_invoice;
        }
    }

    public function get_invoice_payment()
    {
        $all_invoice = $this->db->get('tbl_payments')->result();
        $all_method = $this->db->get('tbl_payment_methods')->result();
        if (!empty($all_invoice)) {
            $all_invoice = array_reverse($all_invoice);
            foreach ($all_invoice as $v_invoices) {
                $years[] = $v_invoices->year_paid;
            }
        }
        if (!empty($years)) {
            $result = array_unique($years);
        }

        $statuses = array(
            array(
                'id' => 4,
                'value' => 'last_month',
                'name' => lang('last_month'),
                'order' => 4,
            ),
            array(
                'id' => 4,
                'value' => 'this_months',
                'name' => lang('this_months'),
                'order' => 4,
            )
        );
        if (!empty($result)) {
            foreach ($result as $v_year) {
                $year = array(
                    'id' => 1,
                    'value' => '_' . $v_year,
                    'name' => $v_year,
                    'order' => 1);
                if (!empty($year)) {
                    array_push($statuses, $year);
                }
            }
        }
        if (!empty($all_method)) {
            foreach ($all_method as $v_method) {
                $method = array(
                    'id' => 1,
                    'value' => $v_method->payment_methods_id,
                    'name' => $v_method->method_name,
                    'order' => 1);
                if (!empty($method)) {
                    array_push($statuses, $method);
                }
            }
        }
        return $statuses;
    }

    public function get_payments($filterBy = null, $client_id = null)
    {
        if (!empty($client_id)) {
            $all_payments = $this->db->where('paid_by', $client_id)->get('tbl_payments')->result();
        } else {
            $all_payments = $this->db->get('tbl_payments')->result();
        }
        if (empty($filterBy) || !empty($filterBy) && $filterBy == 'all') {
            return $all_payments;
        } else {
            if (!empty($all_payments)) {
                foreach ($all_payments as $v_payments) {
                    if (is_numeric($filterBy)) {
                        if ($v_payments->payment_method == $filterBy) {
                            $payment[] = $v_payments;
                        }
                    } else if ($filterBy == 'last_month' || $filterBy == 'this_months') {
                        if ($filterBy == 'last_month') {
                            $month = date('Y-m', strtotime('-1 months'));
                        } else {
                            $month = date('Y-m');
                        }
                        $month_paid = $v_payments->month_paid . '-' . $v_payments->year_paid;
                        if ($month_paid == $month) {
                            $payment[] = $v_payments;
                        }
                    } else if (strstr($filterBy, '_')) {
                        $year = str_replace('_', '', $filterBy);
                        if (strtotime($v_payments->year_paid) == strtotime($year)) {
                            $payment[] = $v_payments;
                        }
                    }

                }
            }
        }

        if (!empty($payment)) {
            return $payment;
        } else {
            return array();
        }
    }

    public function total_sales($filter = null)
    {

        $total = 0;
        $all_payments = get_result('tbl_payments');
//        $currency = get_row(array('symbol' => $payment->currency));
        foreach ($all_payments as $payment) {
            $amount = $payment->amount;
//            if ($payment->currency != config_item('default_currency')) {
//                $amount = convert_currency($p->currency, $amount);
//            }
            $total += $amount;
        }
        return $total;

    }

    public function paid_by_date($year, $month = null)
    {
        $total = 0;
        if (!empty($month)) {
            $where = array('year_paid' => $year, 'month_paid' => $month);
        } else {
            $where = array('year_paid' => $year);
        }
        $payments = $this->db->where($where)->get('tbl_payments')->result();

        foreach ($payments as $p) {
            $amount = $p->amount;
//            if ($p->currency != config_item('default_currency')) {
//                $amount = Applib::convert_currency($p->currency, $amount);
//            }
            $total += $amount;
        }
        return $total;
    }

    public function get_invoice_report($filterBy = null, $range = null)
    {
        if (!empty($filterBy) && is_numeric($filterBy)) {
            $invoice = $this->db->where('client_id', $filterBy)->get('tbl_invoices')->result();
        } else {
            $all_data = $this->get_permission('tbl_invoices');
        }
        if (empty($filterBy) || !empty($filterBy) && $filterBy == 'all') {
            $invoice = $all_data;
        } elseif ($filterBy == 'recurring') {
            $invoice = $this->recurring_invoices();
        } else {
            if (!empty($all_data)) {
                $all_data = array_reverse($all_data);
                foreach ($all_data as $v_invoices) {
                    if ($filterBy == 'paid') {
                        if ($this->get_payment_status($v_invoices->invoices_id) == lang('fully_paid')) {
                            $invoice[] = $v_invoices;
                        }
                    } else if ($filterBy == 'not_paid') {
                        if ($this->get_payment_status($v_invoices->invoices_id) == lang('not_paid')) {
                            $invoice[] = $v_invoices;
                        }
                    } else if ($filterBy == 'draft') {
                        if ($this->get_payment_status($v_invoices->invoices_id) == lang('draft')) {
                            $invoice[] = $v_invoices;
                        }
                    } else if ($filterBy == 'partially_paid') {
                        if ($this->get_payment_status($v_invoices->invoices_id) == lang('partially_paid')) {
                            $invoice[] = $v_invoices;
                        }
                    } else if ($filterBy == 'cancelled') {
                        if ($this->get_payment_status($v_invoices->invoices_id) == lang('cancelled')) {
                            $invoice[] = $v_invoices;
                        }
                    } else if ($filterBy == 'overdue') {
                        $payment_status = $this->get_payment_status($v_invoices->invoices_id);
                        if (strtotime($v_invoices->due_date) < strtotime(date('Y-m-d')) && $payment_status != lang('fully_paid')) {
                            $invoice[] = $v_invoices;
                        }
                    } else if ($filterBy == 'last_month' || $filterBy == 'this_months') {
                        if ($filterBy == 'last_month') {
                            $month = date('Y-m', strtotime('-1 months'));
                        } else {
                            $month = date('Y-m');
                        }
                        if (strtotime($v_invoices->invoice_month) == strtotime($month)) {
                            $invoice[] = $v_invoices;
                        }
                    } else if (strstr($filterBy, '_')) {
                        $year = str_replace('_', '', $filterBy);
                        if (strtotime($v_invoices->invoice_year) == strtotime($year)) {
                            $invoice[] = $v_invoices;
                        }
                    }
                }
            }
        }

        if (!empty($invoice)) {
            $invoices = array();

            if (!empty($range[0])) {
                foreach ($invoice as $v_invoice) {
                    if ($v_invoice->invoice_date >= $range[0] && $v_invoice->invoice_date <= $range[1]) {
                        array_push($invoices, $v_invoice);
                    }
                }
                return $invoices;
            } else {
                return $invoice;
            }
        } else {
            return array();
        }
    }

    public function get_payment_report($client_id = null, $range = null)
    {

        if (!empty($range[0])) {
            $where = array('paid_by' => $client_id, 'payment_date >=' => $range[0], 'payment_date <=' => $range[1]);
        } else if (!empty($client_id) && is_numeric($client_id)) {
            $where = array('paid_by' => $client_id);
        }
        if (!empty($where)) {
            return $this->db->where($where)->get('tbl_payments')->result();
        } else {
            return $this->db->get('tbl_payments')->result();
        }


    }

    public function send_invoice_sms($invoices_id)
    {

        $inv_info = $this->check_by(array('invoices_id' => $invoices_id), 'tbl_invoices');
        $client_info = $this->check_by(array('client_id' => $inv_info->client_id), 'tbl_client');

        if (!empty($client_info)) {
            $currency = $this->client_currency_symbol($client_info->client_id);;
            $mobile = $client_info->mobile;
        } else {
            $currency = $this->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
            $mobile = null;
        }

        $message = config_item('sms_overdue_invoice_template');

        $client = str_replace("{client}", client_name($client_info->client_id), $message);
        $reference_no = str_replace("{invoice_number}", $inv_info->reference_no, $client);
        $invoice_link = str_replace("{invoice_link}", base_url('admin/client/manage_invoice/invoice_details/' . $invoices_id), $reference_no);
        $invoice_duedate = str_replace("{invoice_duedate}", strftime(config_item('date_format'), strtotime($inv_info->due_date)), $invoice_link);
        $invoice_date = str_replace("{invoice_date}", strftime(config_item('date_format'), strtotime($inv_info->invoice_date)), $invoice_duedate);
        $subtotal = str_replace("{invoice_subtotal}", display_money($this->invoice_model->calculate_to('invoice_cost', $inv_info->invoices_id), $currency->symbol), $invoice_date);
        $total = str_replace("{invoice_total}", display_money($this->invoice_model->calculate_to('total', $inv_info->invoices_id), $currency->symbol), $subtotal);
        $status = str_replace("{invoice_status}", lang($this->invoice_model->get_payment_status($inv_info->invoices_id)), $total);
        $message = str_replace("{site_name}", config_item('company_name'), $status);

        if (!empty($mobile)) {
            $twilio_status = config_item('twilio_status');
            if ($twilio_status == 1 && client_can_received_sms($client_info->client_id)) {
                if (function_exists('twilio_send_sms')) {
                    $retval = call_user_func_array('twilio_send_sms', array($mobile, clear_textarea_breaks($message)));
                    return $retval;
                }
            }
        }
    }


}
