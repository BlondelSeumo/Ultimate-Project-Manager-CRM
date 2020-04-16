<?php

/**
 * Description of Cron_Model
 *
 * @author NaYeM
 */
class Cron_Model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    function get_overdue($tbl_name, $proposal = null)
    {
        if (empty($proposal)) {
            $this->db->join('tbl_client', 'tbl_client.client_id = ' . $tbl_name . '.client_id');
        }
        if ($tbl_name == 'tbl_project') {
            $due_date = 'end_date';
        } else {
            $due_date = 'due_date';
        }
        $where = array($due_date => date("Y-m-d"), 'alert_overdue' => 0);
        if ($tbl_name == 'tbl_invoices') {
            $where['status !='] = 'Paid';
            $where['status !='] = 'Cancelled';
            $where['status !='] = 'draft';
        }
        $query = $this->db->where($where)->get($tbl_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return FALSE;
        }
    }

    public function get_recurring_invoice()
    {
        $query = $this->db->query("SELECT * FROM tbl_invoices  WHERE recur_next_date <= date(NOW()) AND (recur_end_date > date(NOW()) OR recur_end_date = '0000-00-00') AND recur_start_date >= date(NOW())")->result();
        return $query;
    }

    public function get_date_due($invoice_date_created)
    {
        $invoice_date_due = new DateTime($invoice_date_created);
        $invoice_date_due->add(new DateInterval('P' . config_item('invoices_due_after') . 'D'));
        return $invoice_date_due->format('Y-m-d');
    }

    public function copy_invoice_items($invoices_id, $return_id)
    {
        $invoice_items = $this->db->where('invoices_id', $invoices_id)->get('tbl_items')->result();

        foreach ($invoice_items as $v_invoice_item) {
            $items_data = array(
                'invoices_id' => $return_id,
                'item_name' => $v_invoice_item->item_name,
                'item_desc' => $v_invoice_item->item_desc,
                'unit_cost' => $v_invoice_item->unit_cost,
                'quantity' => $v_invoice_item->quantity,
                'total_cost' => $v_invoice_item->total_cost,
            );

            $this->db->insert('tbl_items', $items_data);
        }
    }

    public function set_next_recur_date($invoices_id)
    {
        $invoice_recurring = $this->db->where('invoices_id', $invoices_id)->get('tbl_invoices')->row();
        $recur_next_date = $this->increment_date($invoice_recurring->recur_next_date, $invoice_recurring->recur_frequency);
        $data = array(
            'recur_next_date' => $recur_next_date
        );
        $this->db->where('invoices_id', $invoices_id);
        $this->db->update('tbl_invoices', $data);
    }

    function increment_date($date, $increment)
    {
        $new_date = new DateTime($date);
        $new_date->add(new DateInterval('P' . $increment));
        return $new_date->format('Y-m-d');
    }

}
