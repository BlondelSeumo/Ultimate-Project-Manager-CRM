<?php defined('BASEPATH') or exit('No direct script access allowed');

class Gst
{

    public function __construct()
    {
    }

    public function __get($var)
    {
        return get_instance()->$var;
    }

    function summary($items = [])
    {
        $code = '';
        $invoice_view = config_item('invoice_view');
        if ($invoice_view > 0 && !empty($items)) {
            $tax_summary = $this->taxSummary($items);
            $code = $this->genHTML($tax_summary);
        }
        return $code;
    }

    function taxSummary($items = [])
    {
        $tax_summary = array();
        if (!empty($items)) {
            foreach ($items as $key => $item) {
                $tax_amount = 0;
                $item_tax_name = json_decode($item->item_tax_name);
                if (!empty($item_tax_name)) {
                    foreach ($item_tax_name as $v_tax_name) {
                        $i_tax_name = explode('|', $v_tax_name);
                        $tax_amount += $item->total_cost / 100 * $i_tax_name[1];
                    }
                }
                $tax_summary[$key]['items'] = $item->item_name;
                $invoice_view = config_item('invoice_view');
                if (!empty($invoice_view) && $invoice_view == '2') {
                    $tax_summary[$key]['hsn_code'] = $item->hsn_code;
                }
                $tax_summary[$key]['qty'] = $item->quantity;
                $tax_summary[$key]['unit'] = $item->unit;
                $tax_summary[$key]['tax'] = $item->item_tax_name;
                $tax_summary[$key]['tax_amount'] = $tax_amount;
                $tax_summary[$key]['total_cost'] = $item->total_cost;
            }
        }
        return $tax_summary;
    }

    function genHTML($tax_summary = [])
    {
        $html = '';
        if (!empty($tax_summary)) {
            $html .= '<div class="panel panel-custom" style="border-top:1px solid #dde6e9 ">';
            $html .= '<div class="panel-heading"><div class="panel-title"> ' . lang('tax_summary') . '</div></div>';
            $html .= '<div class="table-responsive"><table class="table table-striped"><thead ><tr><th>' . lang('items') . '</th>';
            $invoice_view = config_item('invoice_view');
            if (!empty($invoice_view) && $invoice_view == '2') {
                $html .= '<th>' . lang('hsn_code') . '</th>';
            }
            $html .= '<th>' . lang('qty') . '</th><th>' . lang('tax') . '</th><th class="text-right">' . lang('total_tax') . '</th><th class="text-right">' . lang('tax_excl_amt') . '</th></tr></td><tbody>';
            $total_tax = 0;
            $total_cost = 0;
            foreach ($tax_summary as $summary) {
                $html .= '<tr><td >' . $summary['items'] . '</td>';
                if (!empty($invoice_view) && $invoice_view == '2') {
                    $html .= '<td>' . $summary['hsn_code'] . '</td>';
                }
                $html .= '<td class="text-center">' . $summary['qty'] . ' ' . $summary['unit'] . '</td><td>';
                $tax_name = json_decode($summary['tax']);
                $total_tax += $summary['tax_amount'];
                $total_cost += $summary['total_cost'];
                if (!empty($tax_name)) {
                    foreach ($tax_name as $v_tax_name) {
                        $i_tax_name = explode('|', $v_tax_name);
                        $html .= '<small class="pr-sm">' . $i_tax_name[0] . ' (' . $i_tax_name[1] . ' %)' . '</small>' . display_money($summary['total_cost'] / 100 * $i_tax_name[1]) . ' <br>';
                    }
                }
                $html .= '</td><td class="text-right">' . $summary['tax_amount'] . '</td><td class="text-right">' . $summary['total_cost'] . '</td></tr>';
            }
            $html .= '</tbody></tfoot>';
            $html .= '<tr class=""><th colspan="4" class="text-right">' . lang('total') . '</th><th class="text-right">' . display_money($total_tax) . '</th><th class="text-right">' . display_money($total_cost) . '</th></tr>';
            $html .= '</tfoot></table></div></div>';
        }
        return $html;
    }

    function getIndianStates($blank = false)
    {
        $istates = [
            'AN' => 'Andaman & Nicobar',
            'AP' => 'Andhra Pradesh',
            'AR' => 'Arunachal Pradesh',
            'AS' => 'Assam',
            'BR' => 'Bihar',
            'CH' => 'Chandigarh',
            'CT' => 'Chhattisgarh',
            'DN' => 'Dadra and Nagar Haveli',
            'DD' => 'Daman & Diu',
            'DL' => 'Delhi',
            'GA' => 'Goa',
            'GJ' => 'Gujarat',
            'HR' => 'Haryana',
            'HP' => 'Himachal Pradesh',
            'JK' => 'Jammu & Kashmir',
            'JH' => 'Jharkhand',
            'KA' => 'Karnataka',
            'KL' => 'Kerala',
            'LD' => 'Lakshadweep',
            'MP' => 'Madhya Pradesh',
            'MH' => 'Maharashtra',
            'MN' => 'Manipur',
            'ML' => 'Meghalaya',
            'MZ' => 'Mizoram',
            'NL' => 'Nagaland',
            'OR' => 'Odisha',
            'PY' => 'Puducherry',
            'PB' => 'Punjab',
            'RJ' => 'Rajasthan',
            'SK' => 'Sikkim',
            'TN' => 'Tamil Nadu',
            'TR' => 'Tripura',
            'UK' => 'Uttarakhand',
            'UP' => 'Uttar Pradesh',
            'WB' => 'West Bengal',
        ];
        if ($blank) {
            array_unshift($istates, lang('select'));
        }
        return $istates;
    }


}
