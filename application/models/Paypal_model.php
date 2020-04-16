<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Paypal_Model extends MY_Model {

    function invoice_info($invoice) {
        $this->db->where('invoice_id', $invoice);
        $query = $this->db->get('tbl_invoices');
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
    }

}

/* End of file mdl_pay.php */
/* Location: ./application/models/auth/users.php */