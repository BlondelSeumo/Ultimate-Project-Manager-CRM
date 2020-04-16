<?php

/**
 * Description of return_stock_model
 *
 * @author NaYeM
 */
class Return_stock_model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    public function get_payment_status($return_stock_id, $unmark = null)
    {
        if (!empty($return_stock_id)) {
            $tax = $this->get_return_stock_tax_amount($return_stock_id);
            $discount = $this->get_return_stock_discount($return_stock_id);
            $invoice_cost = $this->get_return_stock_cost($return_stock_id);
            $due = round(((($invoice_cost - $discount) + $tax)));
            $return_stock_info = $this->check_by(array('return_stock_id' => $return_stock_id), 'tbl_return_stock');
            if ($return_stock_info->status == 'Cancelled' && empty($unmark)) {
                return ('cancelled');
            } elseif ($due <= 0) {
                return ('fully_paid');
            } else {
                return ('partially_paid');
            }
        }
    }

    function calculate_to($value, $return_stock_id)
    {
        switch ($value) {
            case 'return_stock_cost':
                return $this->get_return_stock_cost($return_stock_id);
                break;
            case 'tax':
                return $this->get_return_stock_tax_amount($return_stock_id);
                break;
            case 'discount':
                return $this->get_return_stock_discount($return_stock_id);
                break;
            case 'return_stock_due':
                return $this->get_return_stock_due_amount($return_stock_id);
                break;
            case 'total':
                return $this->get_return_stock_total_amount($return_stock_id);
                break;
        }
    }

    function get_return_stock_cost($return_stock_id)
    {
        $this->db->select_sum('total_cost');
        $this->db->where('return_stock_id', $return_stock_id);
        $this->db->from('tbl_return_stock_items');
        $query_result = $this->db->get();
        $cost = $query_result->row();
        if (!empty($cost->total_cost)) {
            $result = $cost->total_cost;
        } else {
            $result = '0';
        }
        return $result;
    }

    public function get_return_stock_tax_amount($return_stock_id)
    {
        $return_stock_info = $this->check_by(array('return_stock_id' => $return_stock_id), 'tbl_return_stock');
        if (!empty($return_stock_info->total_tax)) {
            $tax_info = json_decode($return_stock_info->total_tax);
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

    public function get_return_stock_discount($return_stock_id)
    {
        $return_stock_info = $this->check_by(array('return_stock_id' => $return_stock_id), 'tbl_return_stock');
        if (!empty($return_stock_info)) {
            return $return_stock_info->discount_total;
        }

    }

    public function get_return_stock_due_amount($return_stock_id)
    {

        $return_stock_info = $this->check_by(array('return_stock_id' => $return_stock_id), 'tbl_return_stock');
        if (!empty($return_stock_info)) {
            $tax = $this->get_return_stock_tax_amount($return_stock_id);
            $discount = $this->get_return_stock_discount($return_stock_id);
            $return_stock_cost = $this->get_return_stock_cost($return_stock_id);
            $due_amount = (($return_stock_cost - $discount) + $tax) + $return_stock_info->adjustment;
            if ($due_amount <= 0) {
                $due_amount = 0;
            }
        } else {
            $due_amount = 0;
        }
        return $due_amount;
    }

    public function get_return_stock_total_amount($return_stock_id)
    {

        $return_stock_info = $this->check_by(array('return_stock_id' => $return_stock_id), 'tbl_return_stock');
        $tax = $this->get_return_stock_tax_amount($return_stock_id);
        $discount = $this->get_return_stock_discount($return_stock_id);
        $return_stock_cost = $this->get_return_stock_cost($return_stock_id);

        $total_amount = $return_stock_cost - $discount + $tax + $return_stock_info->adjustment;
        if ($total_amount <= 0) {
            $total_amount = 0;
        }
        return $total_amount;
    }

    function ordered_items_by_id($id, $json = null)
    {
        $rows = $this->db->where('return_stock_id', $id)->order_by('order', 'asc')->get('tbl_return_stock_items')->result();
        if (!empty($json)) {
            if (!empty($rows)) {
                foreach ($rows as $row) {
                    $row->qty = $row->quantity;
                    $row->rate = $row->unit_cost;
                    $row->cost_price = $row->unit_cost;
                    $row->new_itmes_id = $row->saved_items_id;
                    $row->taxname = $this->get_invoice_item_taxes($row->items_id, 'return_stock');;
                    $pr[$row->saved_items_id] = $row;
                }
                return json_encode($pr);
            }
        } else {
            return $rows;
        }
    }


}
