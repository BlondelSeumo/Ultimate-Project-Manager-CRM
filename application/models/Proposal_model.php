<?php

class Proposal_Model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    function proposal_calculation($proposal_value, $proposals_id)
    {
        switch ($proposal_value) {
            case 'proposal_cost':
                return $this->get_proposal_cost($proposals_id);
                break;
            case 'tax':
                return $this->get_proposal_tax_amount($proposals_id);
                break;
            case 'discount':
                return $this->get_proposal_discount($proposals_id);
                break;
            case 'proposal_amount':
                return $this->get_proposal_amount($proposals_id);
                break;
            case 'total':
                return $this->get_total_proposal_amount($proposals_id);
                break;
        }
    }

    function get_proposal_cost($proposals_id)
    {
        $this->db->select_sum('total_cost');
        $this->db->where('proposals_id', $proposals_id);
        $this->db->from('tbl_proposals_items');
        $query_result = $this->db->get();
        $cost = $query_result->row();
        if (!empty($cost->total_cost)) {
            $result = $cost->total_cost;
        } else {
            $result = '0';
        }
        return $result;
    }

    function get_proposal_tax_amount($proposals_id)
    {
        $invoice_info = $this->check_by(array('proposals_id' => $proposals_id), 'tbl_proposals');
        if (!empty($invoice_info)) {
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

    function get_proposal_discount($proposals_id)
    {
        $invoice_info = $this->check_by(array('proposals_id' => $proposals_id), 'tbl_proposals');
        if (!empty($invoice_info)) {
            return $invoice_info->discount_total;
        } else {
            return '0';
        }

    }

    function get_proposal_amount($proposals_id)
    {

        $tax = $this->get_proposal_tax_amount($proposals_id);
        $discount = $this->get_proposal_discount($proposals_id);
        $proposal_cost = $this->get_proposal_cost($proposals_id);
        return (($proposal_cost - $discount) + $tax);
    }

    function get_total_proposal_amount($proposals_id)
    {
        $invoice_info = $this->check_by(array('proposals_id' => $proposals_id), 'tbl_proposals');
        $tax = $this->get_proposal_tax_amount($proposals_id);
        $discount = $this->get_proposal_discount($proposals_id);
        $proposal_cost = $this->get_proposal_cost($proposals_id);
        return (($proposal_cost - $discount) + $tax + $invoice_info->adjustment);
    }

    function ordered_items_by_id($id)
    {
        $result = $this->db->where('proposals_id', $id)->order_by('order', 'asc')->get('tbl_proposals_items')->result();
        return $result;
    }



    public function check_for_merge_invoice($client_id, $current_proposal)
    {

        $proposal_info = $this->db->where('client_id', $client_id)->get('tbl_proposals')->result();

        foreach ($proposal_info as $v_proposal) {
            if ($v_proposal->proposals_id != $current_proposal) {
                if ($v_proposal->status == 'Pending' || $v_proposal->status == 'draft') {
                    $proposal[] = $v_proposal;
                }
            }
        }
        if (!empty($proposal)) {
            return $proposal;
        } else {
            return array();
        }
    }

    public function get_invoice_filter()
    {
        $all_invoice = $this->get_permission('tbl_proposals');
        if (!empty($all_invoice)) {
            $all_invoice = array_reverse($all_invoice);
            foreach ($all_invoice as $v_invoices) {
                $year[] = date('Y', strtotime($v_invoices->proposal_date));
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
                'value' => 'expired',
                'name' => lang('expired'),
                'order' => 1,
            ), array(
                'id' => 1,
                'value' => 'open',
                'name' => lang('open'),
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

    public function get_proposals($filterBy = null)
    {
        $all_invoice = $this->get_permission('tbl_proposals');

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
                        if (strtotime($v_invoices->proposal_month) == strtotime($month)) {
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
                        if (strtotime($v_invoices->proposal_year) == strtotime($year)) {
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

    public function get_client_proposals($filterBy = null)
    {
        $all_invoice = get_result('tbl_proposals', array('status !=' => 'draft', 'module' => 'client', 'module_id' => $this->session->userdata('client_id')));
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
                        if (strtotime($v_invoices->proposal_month) == strtotime($month)) {
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
                        if (strtotime($v_invoices->proposal_year) == strtotime($year)) {
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

    public function get_proposals_report($filterBy = null, $range = null)
    {
        if (!empty($filterBy) && is_numeric($filterBy)) {
            $proposals = $this->db->where(array('module' => 'client', 'module_id' => $filterBy))->get('tbl_proposals')->result();
        } else {
            $all_proposals = $this->get_permission('tbl_proposals');
        }
        if (empty($filterBy) || !empty($filterBy) && $filterBy == 'all') {
            $proposals = $all_proposals;
        } else {
            if (!empty($all_proposals)) {
                $all_proposals = array_reverse($all_proposals);
                foreach ($all_proposals as $v_proposals) {
                    if ($filterBy == 'last_month' || $filterBy == 'this_months') {
                        if ($filterBy == 'last_month') {
                            $month = date('Y-m', strtotime('-1 months'));
                        } else {
                            $month = date('Y-m');
                        }
                        if (strtotime($v_proposals->proposal_month) == strtotime($month)) {
                            $proposals[] = $v_proposals;
                        }
                    } else if ($filterBy == 'expired') {
                        if (strtotime($v_proposals->due_date) < strtotime(date('Y-m-d')) && $v_proposals->status == ('pending') || strtotime($v_proposals->due_date) < strtotime(date('Y-m-d')) && $v_proposals->status == ('draft')) {
                            $proposals[] = $v_proposals;
                        }
                    } else if ($filterBy == $v_proposals->status) {
                        $proposals[] = $v_proposals;
                    } else if (strstr($filterBy, '_')) {
                        $year = str_replace('_', '', $filterBy);
                        if (strtotime($v_proposals->proposal_year) == strtotime($year)) {
                            $proposals[] = $v_proposals;
                        }
                    }

                }
            }
        }
        if (!empty($proposals)) {
            $proposal_info = array();
            if (!empty($range[0])) {
                foreach ($proposals as $v_proposal) {
                    if ($v_proposal->proposal_date >= $range[0] && $v_proposal->proposal_date <= $range[1]) {
                        array_push($proposal_info, $v_proposal);
                    }
                }
                return $proposal_info;
            } else {
                return $proposals;
            }
        } else {
            return array();
        }

    }

}
