<?php

class Estimates_Model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    function estimate_calculation($estimate_value, $estimates_id)
    {
        switch ($estimate_value) {
            case 'estimate_cost':
                return $this->get_estimate_cost($estimates_id);
                break;
            case 'tax':
                return $this->get_estimate_tax_amount($estimates_id);
                break;
            case 'discount':
                return $this->get_estimate_discount($estimates_id);
                break;
            case 'estimate_amount':
                return $this->get_estimate_amount($estimates_id);
                break;
            case 'total':
                return $this->get_total_estimate_amount($estimates_id);
                break;
        }
    }

    function get_estimate_cost($estimates_id)
    {
        $this->db->select_sum('total_cost');
        $this->db->where('estimates_id', $estimates_id);
        $this->db->from('tbl_estimate_items');
        $query_result = $this->db->get();
        $cost = $query_result->row();
        if (!empty($cost->total_cost)) {
            $result = $cost->total_cost;
        } else {
            $result = '0';
        }
        return $result;
    }

    function get_estimate_tax_amount($estimates_id)
    {

        $invoice_info = $this->check_by(array('estimates_id' => $estimates_id), 'tbl_estimates');
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

    function get_estimate_discount($estimates_id)
    {
        $invoice_info = $this->check_by(array('estimates_id' => $estimates_id), 'tbl_estimates');
        return $invoice_info->discount_total;
    }

    function get_estimate_amount($estimates_id)
    {

        $tax = $this->get_estimate_tax_amount($estimates_id);
        $discount = $this->get_estimate_discount($estimates_id);
        $estimate_cost = $this->get_estimate_cost($estimates_id);
        return (($estimate_cost - $discount) + $tax);
    }

    function get_total_estimate_amount($estimates_id)
    {
        $invoice_info = $this->check_by(array('estimates_id' => $estimates_id), 'tbl_estimates');
        $tax = $this->get_estimate_tax_amount($estimates_id);
        $discount = $this->get_estimate_discount($estimates_id);
        $estimate_cost = $this->get_estimate_cost($estimates_id);
        return (($estimate_cost - $discount) + $tax + $invoice_info->adjustment);
    }

    function ordered_items_by_id($id)
    {
        $result = $this->db->where('estimates_id', $id)->order_by('order', 'asc')->get('tbl_estimate_items')->result();
        return $result;
    }


    public function check_for_merge_invoice($client_id, $current_estimate)
    {

        $estimate_info = $this->db->where('client_id', $client_id)->get('tbl_estimates')->result();

        foreach ($estimate_info as $v_estimate) {
            if ($v_estimate->estimates_id != $current_estimate) {
                if (strtolower($v_estimate->status) == 'pending' || $v_estimate->status == 'draft') {
                    $estimate[] = $v_estimate;
                }
            }
        }
        if (!empty($estimate)) {
            return $estimate;
        } else {
            return array();
        }
    }

    public function get_invoice_filter()
    {
        $all_invoice = $this->get_permission('tbl_estimates');
        if (!empty($all_invoice)) {
            $all_invoice = array_reverse($all_invoice);
            foreach ($all_invoice as $v_invoices) {
                $year[] = date('Y', strtotime($v_invoices->estimate_date));
            }
        }
        if (!empty($year)) {
            $result = array_unique($year);
        }

        $statuses = array(
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
                'value' => 'expired',
                'name' => lang('expired'),
                'order' => 1,
            ),
            array(
                'id' => 4,
                'value' => 'declined',
                'name' => lang('declined'),
                'order' => 4,
            ),
            array(
                'id' => 4,
                'value' => 'accepted',
                'name' => lang('accepted'),
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

    public function get_estimate_filter()
    {
        $all_invoice = $this->get_permission('tbl_estimates');
        if (!empty($all_invoice)) {
            $all_invoice = array_reverse($all_invoice);
            foreach ($all_invoice as $v_invoices) {
                $year[] = date('Y', strtotime($v_invoices->estimate_date));
            }
        }
        if (!empty($year)) {
            $result = array_unique($year);
        }

        $statuses = array(
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
                'value' => 'expired',
                'name' => lang('expired'),
                'order' => 1,
            ),
            array(
                'id' => 4,
                'value' => 'declined',
                'name' => lang('declined'),
                'order' => 4,
            ),
            array(
                'id' => 4,
                'value' => 'accepted',
                'name' => lang('accepted'),
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

    public function get_estimates($filterBy = null, $client_id = null)
    {
        if (!empty($client_id)) {
            $all_invoice = get_result('tbl_estimates', array('client_id' => $client_id));
        } else {
            $all_invoice = $this->get_permission('tbl_estimates');
        }
        if (empty($filterBy) || !empty($filterBy) && $filterBy == 'all') {
            return $all_invoice;
        } else {
            if (!empty($all_invoice)) {
                $all_invoice = array_reverse($all_invoice);
                foreach ($all_invoice as $v_invoices) {

                    if ($filterBy == 'last_month' || $filterBy == 'this_months') {
                        if ($filterBy == 'last_month') {
                            $month = date('Y-m', strtotime('-1 months'));
                        } else {
                            $month = date('Y-m');
                        }
                        if (strtotime($v_invoices->estimate_month) == strtotime($month)) {
                            $invoice[] = $v_invoices;
                        }
                    } else if ($filterBy == 'expired') {
                        if (strtotime($v_invoices->due_date) < strtotime(date('Y-m-d')) && $v_invoices->status == ('pending') || strtotime($v_invoices->due_date) < strtotime(date('Y-m-d')) && $v_invoices->status == ('draft')) {
                            $invoice[] = $v_invoices;
                        }

                    } else if ($filterBy == $v_invoices->status) {
                        $invoice[] = $v_invoices;
                    } else if (strstr($filterBy, '_')) {
                        $year = str_replace('_', '', $filterBy);
                        if (strtotime($v_invoices->estimate_year) == strtotime($year)) {
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

    public function get_client_estimates($filterBy = null, $client_id = null)
    {
        if (!empty($client_id)) {
            $all_invoice = get_result('tbl_estimates', array('client_id' => $client_id, 'status !=' => 'draft'));
        } else {
            $all_invoice = $this->get_permission('tbl_estimates');
        }
        if (empty($filterBy) || !empty($filterBy) && $filterBy == 'all') {
            return $all_invoice;
        } else {
            if (!empty($all_invoice)) {
                $all_invoice = array_reverse($all_invoice);
                foreach ($all_invoice as $v_invoices) {

                    if ($filterBy == 'last_month' || $filterBy == 'this_months') {
                        if ($filterBy == 'last_month') {
                            $month = date('Y-m', strtotime('-1 months'));
                        } else {
                            $month = date('Y-m');
                        }
                        if (strtotime($v_invoices->estimate_month) == strtotime($month)) {
                            $invoice[] = $v_invoices;
                        }
                    } else if ($filterBy == 'expired') {
                        if (strtotime($v_invoices->due_date) < strtotime(date('Y-m-d')) && $v_invoices->status == ('pending') || strtotime($v_invoices->due_date) < strtotime(date('Y-m-d')) && $v_invoices->status == ('draft')) {
                            $invoice[] = $v_invoices;
                        }

                    } else if ($filterBy == $v_invoices->status) {
                        $invoice[] = $v_invoices;
                    } else if (strstr($filterBy, '_')) {
                        $year = str_replace('_', '', $filterBy);
                        if (strtotime($v_invoices->estimate_year) == strtotime($year)) {
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

    public function get_estimate_report($filterBy = null, $range = null)
    {
        if (!empty($filterBy) && is_numeric($filterBy)) {
            $estimates = $this->db->where('client_id', $filterBy)->get('tbl_estimates')->result();
        } else {
            $all_estimates = $this->get_permission('tbl_estimates');
        }
        if (empty($filterBy) || !empty($filterBy) && $filterBy == 'all') {
            $estimates = $all_estimates;
        } else {
            if (!empty($all_estimates)) {
                $all_estimates = array_reverse($all_estimates);
                foreach ($all_estimates as $v_estimate) {
                    if ($filterBy == 'last_month' || $filterBy == 'this_months') {
                        if ($filterBy == 'last_month') {
                            $month = date('Y-m', strtotime('-1 months'));
                        } else {
                            $month = date('Y-m');
                        }
                        if (strtotime($v_estimate->estimate_month) == strtotime($month)) {
                            $estimates[] = $v_estimate;
                        }
                    } else if ($filterBy == 'expired') {
                        if (strtotime($v_estimate->due_date) < strtotime(date('Y-m-d')) && $v_estimate->status == ('pending') || strtotime($v_estimate->due_date) < strtotime(date('Y-m-d')) && $v_estimate->status == ('draft')) {
                            $estimates[] = $v_estimate;
                        }
                    } else if ($filterBy == $v_estimate->status) {
                        $estimates[] = $v_estimate;
                    } else if (strstr($filterBy, '_')) {
                        $year = str_replace('_', '', $filterBy);
                        if (strtotime($v_estimate->estimate_year) == strtotime($year)) {
                            $estimates[] = $v_estimate;
                        }
                    }

                }
            }
        }
        if (!empty($estimates)) {
            $estimate_info = array();
            if (!empty($range[0])) {
                foreach ($estimates as $v_estimate) {
                    if ($v_estimate->estimate_date >= $range[0] && $v_estimate->estimate_date <= $range[1]) {
                        array_push($estimate_info, $v_estimate);
                    }
                }
                return $estimate_info;
            } else {
                return $estimates;
            }
        } else {
            return array();
        }

    }

}
