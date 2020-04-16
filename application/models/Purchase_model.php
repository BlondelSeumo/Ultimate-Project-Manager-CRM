<?php

/**
 * Description of purchase_model
 *
 * @author NaYeM
 */
class Purchase_model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    public function get_payment_status($purchase_id, $unmark = null)
    {
        if (!empty($purchase_id)) {
            $tax = $this->get_purchase_tax_amount($purchase_id);
            $discount = $this->get_purchase_discount($purchase_id);
            $invoice_cost = $this->get_purchase_cost($purchase_id);
            $payment_made = round($this->get_purchase_paid_amount($purchase_id), 2);
            $due = round(((($invoice_cost - $discount) + $tax) - $payment_made));
            $purchase_info = $this->check_by(array('purchase_id' => $purchase_id), 'tbl_purchases');
            if ($purchase_info->status == 'Cancelled' && empty($unmark)) {
                return ('cancelled');
            } elseif ($payment_made < 1) {
                return ('not_paid');
            } elseif ($due <= 0) {
                return ('fully_paid');
            } else {
                return ('partially_paid');
            }
        }
    }

    function calculate_to($value, $purchase_id)
    {
        switch ($value) {
            case 'purchase_cost':
                return $this->get_purchase_cost($purchase_id);
                break;
            case 'tax':
                return $this->get_purchase_tax_amount($purchase_id);
                break;
            case 'discount':
                return $this->get_purchase_discount($purchase_id);
                break;
            case 'paid_amount':
                return $this->get_purchase_paid_amount($purchase_id);
                break;
            case 'purchase_due':
                return $this->get_purchase_due_amount($purchase_id);
                break;
            case 'total':
                return $this->get_purchase_total_amount($purchase_id);
                break;
        }
    }

    function get_purchase_cost($purchase_id)
    {
        $this->db->select_sum('total_cost');
        $this->db->where('purchase_id', $purchase_id);
        $this->db->from('tbl_purchase_items');
        $query_result = $this->db->get();
        $cost = $query_result->row();
        if (!empty($cost->total_cost)) {
            $result = $cost->total_cost;
        } else {
            $result = '0';
        }
        return $result;
    }

    public function get_purchase_tax_amount($purchase_id)
    {
        $purchase_info = $this->check_by(array('purchase_id' => $purchase_id), 'tbl_purchases');
        if (!empty($purchase_info->total_tax)) {
            $tax_info = json_decode($purchase_info->total_tax);
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

    public function get_purchase_discount($purchase_id)
    {
        $purchase_info = $this->check_by(array('purchase_id' => $purchase_id), 'tbl_purchases');
        if (!empty($purchase_info)) {
            return $purchase_info->discount_total;
        }

    }

    public function get_purchase_paid_amount($purchase_id)
    {

        $this->db->select_sum('amount');
        $this->db->where('purchase_id', $purchase_id);
        $this->db->from('tbl_purchase_payments');
        $query_result = $this->db->get();
        $amount = $query_result->row();
//        $tax = $this->get_purchase_tax_amount($purchase_id);
        if (!empty($amount->amount)) {
            $result = $amount->amount;
        } else {
            $result = '0';
        }
        return $result;
    }

    public function get_purchase_due_amount($purchase_id)
    {

        $purchase_info = $this->check_by(array('purchase_id' => $purchase_id), 'tbl_purchases');
        if (!empty($purchase_info)) {
            $tax = $this->get_purchase_tax_amount($purchase_id);
            $discount = $this->get_purchase_discount($purchase_id);
            $purchase_cost = $this->get_purchase_cost($purchase_id);
            $payment_made = $this->get_purchase_paid_amount($purchase_id);
            $due_amount = (($purchase_cost - $discount) + $tax) - $payment_made + $purchase_info->adjustment;
            if ($due_amount <= 0) {
                $due_amount = 0;
            }
        } else {
            $due_amount = 0;
        }
        return $due_amount;
    }

    public function get_purchase_total_amount($purchase_id)
    {

        $purchase_info = $this->check_by(array('purchase_id' => $purchase_id), 'tbl_purchases');
        $tax = $this->get_purchase_tax_amount($purchase_id);
        $discount = $this->get_purchase_discount($purchase_id);
        $purchase_cost = $this->get_purchase_cost($purchase_id);
//        $payment_made = $this->get_purchase_paid_amount($purchase_id);

        $total_amount = $purchase_cost - $discount + $tax + $purchase_info->adjustment;
        if ($total_amount <= 0) {
            $total_amount = 0;
        }
        return $total_amount;
    }

    function ordered_items_by_id($id, $json = null)
    {
        $rows = $this->db->where('purchase_id', $id)->order_by('order', 'asc')->get('tbl_purchase_items')->result();
        if (!empty($json)) {
            if (!empty($rows)) {
                foreach ($rows as $row) {
                    $row->qty = $row->quantity;
                    $row->rate = $row->unit_cost;
                    $row->cost_price = $row->unit_cost;
                    $row->new_itmes_id = $row->saved_items_id;
                    $row->taxname = $this->get_invoice_item_taxes($row->items_id, 'purchase');;
                    $pr[$row->saved_items_id] = $row;
                }
                return json_encode($pr);
            }
        } else {
            return $rows;
        }
    }


}
