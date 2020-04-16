<?php

/**
 * Description of Project_Model
 *
 * @author NaYeM
 */
class Report_Model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    public function get_report_by_date($start_date, $end_date)
    {
        $this->db->select('*');
        $this->db->from('tbl_transactions');
        $this->db->where('date >=', $start_date);
        $this->db->where('date <=', $end_date);
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    static function total_paid()
    {
        $total = 0;
        $query = "SELECT * FROM tbl_payments WHERE refunded = 'No'";
        $payments = self::$db->query($query)->result();
        foreach ($payments as $p) {
            $amount = $p->amount;
            if ($p->currency != config_item('default_currency')) {
                $amount = Applib::convert_currency($p->currency, $amount);
            }
            $total += $amount;
        }
        return round($total, config_item('currency_decimals'));
    }


}
